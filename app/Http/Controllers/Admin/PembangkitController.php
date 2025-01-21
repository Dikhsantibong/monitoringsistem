<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PowerPlant;
use App\Models\Machine;
use App\Models\MachineOperation;
use App\Models\MachineStatusLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use App\Models\UnitOperationHour;

class PembangkitController extends Controller
{
    public function ready()
    {
        $units = PowerPlant::orderByRaw("
            CASE 
                WHEN name LIKE 'PLTU%' THEN 1
                WHEN name LIKE 'PLTM%' THEN 2
                WHEN name LIKE 'PLTD%' THEN 3
                WHEN name LIKE 'PLTMG%' THEN 4
                ELSE 5
            END
        ")->get();
        $machines = Machine::with('issues', 'metrics')->get();
        $operations = MachineOperation::all();
        
        // Ambil status log dan HOP hari ini
        $todayLogs = MachineStatusLog::whereDate('tanggal', Carbon::today())->get();
        $todayHops = UnitOperationHour::whereDate('tanggal', Carbon::today())->get();

        return view('admin.pembangkit.ready', compact('units', 'machines', 'operations', 'todayLogs', 'todayHops'));
    }

    public function saveStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Simpan data HOP
            foreach ($request->hops as $hopData) {
                UnitOperationHour::updateOrCreate(
                    [
                        'power_plant_id' => $hopData['power_plant_id'],
                        'tanggal' => $hopData['tanggal']
                    ],
                    [
                        'hop_value' => $hopData['hop_value'],
                        'unit_source' => session('unit')
                    ]
                );
            }

            // Simpan data status mesin
            foreach ($request->logs as $log) {
                $equipment = isset($log['equipment']) ? trim($log['equipment']) : null;
                
                $operation = MachineOperation::where('machine_id', $log['machine_id'])
                    ->latest('recorded_at')
                    ->first();

                // Cek status untuk menentukan nilai DMP
                $dmp = $operation ? $operation->dmp : 0;
                if (in_array($log['status'], ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'])) {
                    $dmp = 0; // Set DMP ke 0 untuk status tertentu
                }

                if (!empty($log['status']) || !empty($log['deskripsi']) || !empty($log['load_value']) || !empty($log['progres'])) {
                    MachineStatusLog::updateOrCreate(
                        [
                            'machine_id' => $log['machine_id'],
                            'tanggal' => $log['tanggal']
                        ],
                        [
                            'dmn' => $operation ? $operation->dmn : 0,
                            'dmp' => $dmp, // Gunakan nilai DMP yang sudah ditentukan
                            'load_value' => $log['load_value'],
                            'status' => $log['status'],
                            'component' => $log['component'],
                            'equipment' => $equipment,
                            'deskripsi' => $log['deskripsi'] ?? null,
                            'kronologi' => $log['kronologi'] ?? null,
                            'action_plan' => $log['action_plan'] ?? null,
                            'progres' => $log['progres'] ?? null,
                            'tanggal_mulai' => $log['tanggal_mulai'] ?? null,
                            'target_selesai' => $log['target_selesai'] ?? null,
                            'unit_source' => session('unit')
                        ]
                    );
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $tanggal = $request->tanggal;
            
            // Ambil data status mesin untuk tanggal yang diminta
            $logs = MachineStatusLog::with(['machine.powerPlant'])
                ->whereDate('tanggal', $tanggal)
                ->get();
            
            // Jika tidak ada data untuk tanggal yang diminta, 
            // ambil data terakhir untuk setiap mesin
            if ($logs->isEmpty()) {
                $logs = MachineStatusLog::with(['machine.powerPlant'])
                    ->whereIn('id', function($query) use ($tanggal) {
                        $query->selectRaw('MAX(id)')
                            ->from('machine_status_logs')
                            ->where('tanggal', '<', $tanggal)
                            ->groupBy('machine_id');
                    })
                    ->get()
                    ->map(function ($lastLog) use ($tanggal) {
                        // Buat salinan data dengan tanggal yang baru
                        $newLog = $lastLog->replicate();
                        $newLog->tanggal = $tanggal;
                        
                        // Simpan log baru ke database
                        $newLog->save();
                        
                        return $newLog;
                    });
            }

            // Ambil data HOP dengan cara yang sama
            $hops = UnitOperationHour::whereDate('tanggal', $tanggal)->get();
            if ($hops->isEmpty()) {
                $hops = UnitOperationHour::whereIn('id', function($query) use ($tanggal) {
                    $query->selectRaw('MAX(id)')
                        ->from('unit_operation_hours')
                        ->where('tanggal', '<', $tanggal)
                        ->groupBy('power_plant_id');
                })
                ->get()
                ->map(function ($lastHop) use ($tanggal) {
                    // Buat salinan data dengan tanggal yang baru
                    $newHop = $lastHop->replicate();
                    $newHop->tanggal = $tanggal;
                    
                    // Simpan hop baru ke database
                    $newHop->save();
                    
                    return $newHop;
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => $logs,
                    'hops' => $hops
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    public function getStatusHistory(Request $request)
    {
        try {
            $startDate = $request->start_date ?? Carbon::now()->subDays(30);
            $endDate = $request->end_date ?? Carbon::now();
            $machineId = $request->machine_id;

            // Query untuk mengambil history status
            $history = DB::table('machine_status_logs as msl')
                ->select([
                    'msl.tanggal',
                    'msl.status',
                    'msl.deskripsi',
                    'm.name as machine_name',
                    'pp.name as unit_name'
                ])
                ->join('machines as m', 'm.id', '=', 'msl.machine_id')
                ->join('power_plants as pp', 'pp.id', '=', 'm.power_plant_id')
                ->when($machineId, function($query) use ($machineId) {
                    return $query->where('msl.machine_id', $machineId);
                })
                ->whereBetween('msl.tanggal', [$startDate, $endDate])
                ->orderBy('msl.tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
            
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil history: ' . $e->getMessage()
            ]);
        }
    }

    public function report(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');
        
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select([
                'machine_status_logs.*',
                'machines.name as machine_name',
                'power_plants.name as power_plant_name'
            ])
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->join('power_plants', 'power_plants.id', '=', 'machines.power_plant_id')
            ->whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.pembangkit.report-table', compact('logs'))->render()
            ]);
        }

        return view('admin.pembangkit.report', compact('logs'));
    }

    public function downloadReport(Request $request)
    {
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select([
                'machine_status_logs.*',
                'machines.name as machine_name',
                'power_plants.name as power_plant_name'
            ])
            ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
            ->join('power_plants', 'power_plants.id', '=', 'machines.power_plant_id')
            ->whereDate('tanggal', $request->date ?? now())
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.pembangkit.report-pdf', compact('logs'));
        
        return $pdf->download('laporan-kesiapan-pembangkit.pdf');
    }

    public function printReport(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->whereDate('tanggal', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pembangkit.report-print', compact('logs'));
    }

    public function resetStatus(Request $request)
    {
        try {
            $tanggal = $request->tanggal ?? now()->format('Y-m-d');
            
            // Ambil semua log status pada tanggal tersebut
            $currentLogs = MachineStatusLog::whereDate('tanggal', $tanggal)->get();
            
            // Ambil data mesin yang sedang gangguan dan masih dalam periode gangguan
            $activeIssues = MachineStatusLog::where('status', 'Gangguan')
                ->where(function($query) use ($tanggal) {
                    $query->whereNull('target_selesai')
                        ->orWhereDate('target_selesai', '>=', $tanggal);
                })
                ->whereDate('tanggal_mulai', '<=', $tanggal)
                ->get();
            
            // Kumpulkan machine_id yang sedang gangguan
            $machineIdsWithIssues = $activeIssues->pluck('machine_id')->toArray();
            
            // Cek apakah ada input baru dengan status lain untuk mesin yang gangguan
            $newInputsForIssues = MachineStatusLog::whereIn('machine_id', $machineIdsWithIssues)
                ->where('status', '!=', 'Gangguan')
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->pluck('machine_id')
                ->toArray();
            
            // Machine IDs yang akan dipertahankan (tidak direset)
            $preservedMachineIds = array_diff($machineIdsWithIssues, $newInputsForIssues);
            
            DB::beginTransaction();
            
            foreach ($currentLogs as $log) {
                // Jika mesin tidak dalam daftar yang dipreservasi, reset datanya
                if (!in_array($log->machine_id, $preservedMachineIds)) {
                    // Simpan DMN dan DMP
                    $dmn = $log->dmn;
                    $dmp = $log->dmp;
                    
                    // Update log dengan nilai default kecuali DMN dan DMP
                    $log->update([
                        'status' => 'Operasi',
                        'component' => null,
                        'equipment' => '',
                        'deskripsi' => '',
                        'kronologi' => '',
                        'action_plan' => '',
                        'progres' => '',
                        'load_value' => '',
                        'tanggal_mulai' => null,
                        'target_selesai' => null,
                        'dmn' => $dmn,
                        'dmp' => $dmp
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil direset',
                'preserved_machines' => $preservedMachineIds
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reset Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset data: ' . $e->getMessage()
            ]);
        }
    }
}