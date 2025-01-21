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
            // Eager load machines dan status logs
            $powerPlants = PowerPlant::with([
                'machines',
                'machines.statusLogs' => function($query) {
                    $query->latest()->take(1);
                }
            ])->get();

            $markersData = Marker::all();
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
            $markers = $markersData->map(function($marker) {
                // Ambil data chart dari MachineStatusLog
                $chartData = MachineStatusLog::getChartData($marker->id);
                
                return [
                    // Data dari Marker
                    'id' => $marker->id,
                    'name' => $marker->name,
                    'latitude' => $marker->getLatitudeAttribute(),
                    'longitude' => $marker->getLongitudeAttribute(),
                    'mesin' => $marker->mesin,
                    'capacity' => $marker->capacity,
                    'status' => $marker->status,
                    'DMN' => $marker->DMN,
                    'DMP' => $marker->DMP,
                    'Beban' => $marker->Beban,
                    'HOP' => $marker->HOP,
                    
                    // Data tambahan untuk grafik
                    'chart_data' => [
                        'dates' => $chartData->pluck('date')->toArray(),
                        'beban' => $chartData->pluck('load')->toArray(),
                        'kapasitas' => $chartData->pluck('capacity')->toArray()
                    ]
                ];
            })->toArray(); // Konversi ke array

            // Debug dengan format array yang benar
            \Log::info('Markers data:', ['count' => count($markers)]);

            // Hitung total statistik
            $total_capacity = $powerPlants->sum(function($plant) {
                return $plant->machines->sum('capacity');
            });
            $total_units = $powerPlants->count();
            $active_units = $powerPlants->filter(function($plant) {
                return $plant->machines->where('status', 'Aktif')->count() > 0;
            })->count();

            \Log::info('Data markers:', $markers); // Debug log

            // Ambil data untuk live unit operational
            $statusLogs = MachineStatusLog::with(['machine.powerPlant'])
                ->whereIn('status', ['Gangguan', 'Mothballed', 'Overhaul'])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('homepage', compact(
                'powerPlants',
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
                'active_units_data',
                'statusLogs'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in HomeController@index: ' . $e->getMessage());
            return view('homepage', ['error' => 'Terjadi kesalahan saat memuat data.']);
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
                ->whereIn('status', ['Gangguan', 'Mothballed', 'Overhaul'])
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

    public function getPlantChartData($plantId)
    {
        $chartData = MachineStatusLog::getChartData($plantId);
        
        return response()->json([
            'dates' => $chartData->pluck('date')->toArray(),
            'beban' => $chartData->pluck('load')->toArray(),
            'kapasitas' => $chartData->pluck('capacity')->toArray()
        ]);
    }
}
