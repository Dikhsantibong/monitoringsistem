<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;
use App\Models\MachineStatusLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Menggunakan model PowerPlant untuk mengambil data dengan eager loading machines
        $powerPlants = PowerPlant::with(['machines' => function($query) {
            $query->select('id', 'power_plant_id', 'name', 'status', 'capacity');
        }])->get();
        
        // Mengambil data dari MachineStatusLog sebagai pengganti Unit
        $units = MachineStatusLog::with(['machine', 'machine.powerPlant'])
            ->select('machine_id', 'status', 'dmn', 'dmp', 'load_value')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('machine_id')
            ->map(function ($logs) {
                $latestLog = $logs->first();
                return [
                    'name' => $latestLog->machine->name ?? 'N/A',
                    'power_plant' => $latestLog->machine->powerPlant->name ?? 'N/A',
                    'status' => $latestLog->status,
                    'capacity' => $latestLog->machine->capacity ?? 0,
                    'dmn' => $latestLog->dmn,
                    'dmp' => $latestLog->dmp,
                    'load_value' => $latestLog->load_value
                ];
            })->values();
        
        // Mengambil data markers untuk map dengan data mesin dari relasi
        $markers = $powerPlants->map(function ($powerPlant) {
            $machines = $powerPlant->machines;
            
            return [
                'id' => $powerPlant->id,
                'name' => $powerPlant->name,
                'latitude' => $powerPlant->latitude,
                'longitude' => $powerPlant->longitude,
                'total_machines' => $machines->count(), // Total mesin per power plant
                'total_capacity' => $machines->sum('capacity'),
                'active_machines' => $machines->where('status', 'Aktif')->count(),
                'machines' => $machines->map(function ($machine) {
                    return [
                        'name' => $machine->name,
                        'status' => $machine->status,
                        'capacity' => $machine->capacity
                    ];
                })
            ];
        });

        // Hitung statistik berdasarkan data MachineStatusLog
        $total_capacity = $units->sum('capacity');
        $total_units = $units->count();
        $active_units = $units->where('status', 'Aktif')->count();

        // Ambil data untuk grafik
        $machineOperations = MachineOperation::with('machine')
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->recorded_at)->format('Y-m-d');
            });

        $dmn_data = [];
        $dmp_data = [];
        $load_value_data = [];
        $capacity_data = [];
        $total_capacity_data = [];
        $total_units_data = [];
        $active_units_data = [];
        $dates = [];

        foreach ($machineOperations as $date => $operations) {
            $dates[] = $date;
            $dmn_data[] = $operations->avg('dmn');
            $dmp_data[] = $operations->avg('dmp');
            $load_value_data[] = $operations->avg('load_value');
            $capacity_data[] = $operations->first()->machine->capacity;
            $total_capacity_data[] = $total_capacity;
            $total_units_data[] = $total_units;
            $active_units_data[] = $active_units;
        }

        \Log::info('Markers Data:', ['markers' => $markers]); // Debug log

        return view('homepage', compact(
            'powerPlants',
            'markers',
            'total_capacity',
            'total_units',
            'active_units',
            'dmn_data',
            'dmp_data',
            'load_value_data',
            'capacity_data',
            'total_capacity_data',
            'total_units_data',
            'active_units_data',
            'dates',
            'units'
        ));
    }

    public function getAccumulationData($markerId)
    {
        try {
            // Gunakan model PowerPlant untuk mendapatkan data pembangkit
            $powerPlant = PowerPlant::find($markerId);
            
            if (!$powerPlant) {
                return response()->json([
                    'message' => 'Power Plant tidak ditemukan',
                    'status' => 'error'
                ], 404);
            }

            // Dapatkan semua mesin dari pembangkit tersebut
            $machineIds = $powerPlant->machines()->pluck('id')->toArray();

            // Gunakan model MachineStatusLog untuk mendapatkan data gangguan
            $statusLogs = MachineStatusLog::with(['machine', 'machine.powerPlant'])
                ->whereIn('machine_id', $machineIds)
                ->where('status', 'Gangguan')
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'tanggal' => $log->tanggal,
                        'machine_name' => $log->machine->name,
                        'power_plant_name' => $log->machine->powerPlant->name,
                        'component' => $log->component,
                        'equipment' => $log->equipment,
                        'deskripsi' => $log->deskripsi,
                        'kronologi' => $log->kronologi,
                        'action_plan' => $log->action_plan,
                        'progres' => $log->progres,
                        'status' => $log->status
                    ];
                });

            // Debug: Log data yang diambil
            \Log::info('Status Logs Data:', ['data' => $statusLogs]);

            if ($statusLogs->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data gangguan untuk pembangkit ini',
                    'status' => 'empty'
                ]);
            }

            return response()->json($statusLogs);

        } catch (\Exception $e) {
            \Log::error('Error in getAccumulationData: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
