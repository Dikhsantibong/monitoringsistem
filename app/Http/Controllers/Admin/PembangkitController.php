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
        
        // Ambil status log hari ini
        $todayLogs = MachineStatusLog::whereDate('tanggal', Carbon::today())->get();

        return view('admin.pembangkit.ready', compact('units', 'machines', 'operations', 'todayLogs'));
    }

    public function saveStatus(Request $request)
    {
        try {
            DB::beginTransaction();
            
            \Log::info('Data yang diterima untuk disimpan:', $request->logs);
            
            foreach ($request->logs as $log) {
                // Pastikan equipment diambil dengan benar dari request
                $equipment = isset($log['equipment']) ? trim($log['equipment']) : null;
                
                $operation = MachineOperation::where('machine_id', $log['machine_id'])
                    ->latest('recorded_at')
                    ->first();

                if (!empty($log['status']) || !empty($log['deskripsi']) || !empty($log['load_value']) || !empty($log['progres'])) {
                    MachineStatusLog::create([
                        'machine_id' => $log['machine_id'],
                        'dmn' => $operation ? $operation->dmn : 0,
                        'dmp' => $operation ? $operation->dmp : 0,
                        'load_value' => $log['load_value'],
                        'tanggal' => $log['tanggal'],
                        'status' => $log['status'],
                        'component' => $log['component'],
                        'equipment' => $equipment,
                        'deskripsi' => $log['deskripsi'] ?? null,
                        'kronologi' => $log['kronologi'] ?? null,
                        'action_plan' => $log['action_plan'] ?? null,
                        'progres' => $log['progres'] ?? null,
                        'tanggal_mulai' => $log['tanggal_mulai'] ?? null,
                        'target_selesai' => $log['target_selesai'] ?? null
                    ]);
                }
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving machine status: ' . $e->getMessage());
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
            $search = $request->search;
            
            $query = MachineStatusLog::with(['machine.powerPlant'])
                ->when($tanggal, function($q) use ($tanggal) {
                    return $q->whereDate('tanggal', $tanggal);
                });
                
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('machine.powerPlant', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('machine', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$search}%");
                });
            }
            
            // Tambahkan pengurutan berdasarkan status
            $logs = $query->orderByRaw("CASE WHEN status = 'Gangguan' THEN 0 ELSE 1 END")->get();
            
            if ($logs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $logs
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