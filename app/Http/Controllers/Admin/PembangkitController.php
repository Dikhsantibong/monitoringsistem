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
        $units = PowerPlant::all();
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
            
            foreach ($request->logs as $log) {
                // Hanya simpan jika ada nilai yang diinputkan
                if (!empty($log['status']) || !empty($log['keterangan']) || !empty($log['load_value'])) {
                    // Simpan ke machine_status_logs
                    MachineStatusLog::create([
                        'machine_id' => $log['machine_id'],
                        'tanggal' => $log['tanggal'],
                        'status' => $log['status'],
                        'keterangan' => $log['keterangan'],
                        'load_value' => $log['load_value'],
                        'dmn' => $log['dmn'] ?? 0,
                        'dmp' => $log['dmp'] ?? 0,
                        'kronologi' => $log['kronologi'] ?? null, // Tambahkan kolom kronologi
                        'action_plan' => $log['action_plan'] ?? null, // Tambahkan kolom action_plan
                        'progres' => $log['progres'] ?? null, // Tambahkan kolom progres
                        'target_selesai' => $log['target_selesai'] ?? null // Tambahkan kolom target_selesai
                    ]);

                    // Simpan ke machine_operations
                    // MachineOperation::create([
                    //     'machine_id' => $log['machine_id'],
                    //     'dmn' => $log['dmn'] ?? 0,
                    //     'dmp' => $log['dmp'] ?? 0,
                    //     'load_value' => $log['load_value'],
                    //     'recorded_at' => $log['tanggal'],
                    //     'status' => $log['status'],
                    //     'keterangan' => $log['keterangan']
                    // ]);
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
                    ->orWhere('keterangan', 'LIKE', "%{$search}%");
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
                    'msl.keterangan',
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
        try {
            $date = $request->date ?? now()->format('Y-m-d');
            
            $logs = MachineStatusLog::with(['machine.powerPlant'])
                ->whereDate('tanggal', $date)
                ->get();

            if ($request->ajax()) {
                $view = view('admin.pembangkit.report-table', compact('logs'))->render();
                return response()->json([
                    'success' => true,
                    'html' => $view
                ]);
            }

            return view('admin.pembangkit.report', compact('logs'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            
            throw $e;
        }
    }

    public function downloadReport(Request $request)
    {
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->whereDate('tanggal', $request->date ?? now())
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
}
