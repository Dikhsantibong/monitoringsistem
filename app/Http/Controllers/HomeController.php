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
        try {
            // Ambil data power plant dengan machines
            $powerPlants = PowerPlant::select('id', 'name', 'latitude', 'longitude')
                ->with(['machines:id,power_plant_id,name,status,capacity'])
                ->get();
            
            $markers = Marker::all();
            // Ambil data status log hari ini
            $units = MachineStatusLog::with(['machine', 'machine.powerPlant'])
                ->select(
                    'machine_id',
                    'status',
                    'dmn',
                    'dmp',
                    'load_value',
                    'tanggal',
                    'created_at',
                    'tanggal_mulai',
                    'target_selesai'
                )
                ->where('status', 'Gangguan')
                ->where(function ($query) {
                    $query->where('target_selesai', '>=', Carbon::now())
                        ->orWhereNull('target_selesai');
                })
                ->whereNotExists(function ($query) {
                    $query->from('machine_status_logs as msl2')
                        ->whereColumn('msl2.machine_id', 'machine_status_logs.machine_id')
                        ->where('msl2.created_at', '>', 'machine_status_logs.created_at')
                        ->where('msl2.status', 'Gangguan')
                        ->whereBetween('msl2.created_at', [
                            DB::raw('machine_status_logs.tanggal_mulai'),
                            DB::raw('machine_status_logs.target_selesai')
                        ]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Gunakan created_at untuk lastUpdate
            $lastUpdate = $units->max('created_at');
            
            // Data untuk grafik dari MachineStatusLog
            $monthlyData = MachineStatusLog::getDummyMonthlyData();
            
            // Siapkan data untuk grafik
            $dates = $monthlyData->pluck('month')->toArray();
            $dmn_data = $monthlyData->pluck('count')->map(function($item) { return rand(70, 90); })->toArray();
            $dmp_data = $monthlyData->pluck('count')->map(function($item) { return rand(60, 85); })->toArray();
            $load_value_data = $monthlyData->pluck('count')->map(function($item) { return rand(100, 500); })->toArray();
            $capacity_data = $monthlyData->pluck('count')->map(function($item) { return rand(500, 1000); })->toArray();
            $total_capacity_data = $monthlyData->pluck('count')->map(function($item) { return rand(1000, 2000); })->toArray();
            $total_units_data = $monthlyData->pluck('count')->toArray();
            $active_units_data = $monthlyData->pluck('count')->map(function($item) { return rand(1, $item); })->toArray();
            
            // Sederhanakan data marker
            $markers = [];
            foreach ($powerPlants as $plant) {
                $markers[] = [
                    'id' => $plant->id,
                    'name' => $plant->name,
                    'latitude' => $plant->latitude,
                    'longitude' => $plant->longitude,
                    'total_machines' => $plant->machines->count(),
                    'active_machines' => $plant->machines->where('status', 'Aktif')->count(),
                    'total_capacity' => $plant->machines->sum('capacity')
                ];
            }

            // Hitung total statistik
            $total_capacity = $powerPlants->sum(function($plant) {
                return $plant->machines->sum('capacity');
            });
            $total_units = $powerPlants->count();
            $active_units = $powerPlants->filter(function($plant) {
                return $plant->machines->where('status', 'Aktif')->count() > 0;
            })->count();

            \Log::info('Data markers:', $markers); // Debug log

            $markers = Marker::all()->map(function($marker) {
                $data = [
                    'id' => $marker->id,
                    'name' => $marker->name,
                    'latitude' => $marker->lat,
                    'longitude' => $marker->lng,
                    'total_machines' => 1,
                    'active_machines' => $marker->status == 'Aktif' ? 1 : 0,
                    'total_capacity' => (float) $marker->capacity
                ];
                \Log::info("Processing marker: ", $data);
                return $data;
            })->toArray();

            return view('homepage', compact(
                'markers',
                'total_capacity',
                'total_units',
                'active_units',
                'units',
                'lastUpdate',
                'dates',
                'dmn_data',
                'dmp_data',
                'load_value_data',
                'capacity_data',
                'total_capacity_data',
                'total_units_data',
                'active_units_data'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            return view('homepage')->with('error', 'Terjadi kesalahan saat memuat data');
        }
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
