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
            
            foreach($request->logs as $log) {
                MachineStatusLog::updateOrCreate(
                    [
                        'machine_id' => $log['machine_id'],
                        'tanggal' => $log['tanggal']
                    ],
                    [
                        'status' => $log['status'],
                        'keterangan' => $log['keterangan'],
                        'dmn' => $log['dmn'] ?? null,
                        'dmp' => $log['dmp'] ?? null,
                        'load_value' => $log['load_value'] ?? null
                    ]
                );
            }
            
            DB::commit();
            return response()->json(['success' => true]);
            
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getStatus(Request $request)
    {
        try {
            $tanggal = $request->tanggal;
            
            // Query untuk mengambil log status berdasarkan tanggal
            $logs = DB::table('machine_status_logs as msl')
                ->select([
                    'msl.machine_id',
                    'msl.status',
                    'msl.keterangan',
                    'm.name as machine_name',
                    'pp.name as unit_name'
                ])
                ->join('machines as m', 'm.id', '=', 'msl.machine_id')
                ->join('power_plants as pp', 'pp.id', '=', 'm.power_plant_id')
                ->whereDate('msl.tanggal', $tanggal)
                ->get();

            if ($logs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk tanggal tersebut'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $logs
            ]);
            
        } catch(\Exception $e) {
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
}
