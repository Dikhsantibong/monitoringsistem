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
            // Ambil data power plants dengan eager loading
            $powerPlants = PowerPlant::with(['machines.statusLogs'])->get();
            
            // Ambil status logs
            $statusLogs = MachineStatusLog::with(['machine.powerPlant'])
                ->whereIn('id', function($query) {
                    $query->selectRaw('MAX(id)')
                        ->from('machine_status_logs')
                        ->groupBy('machine_id');
                })
                ->get();

            // Inisialisasi array untuk data
            $total_capacity_data = [];
            $total_units_data = [];
            $active_units_data = [];
            $dmn_data = [];
            $dmp_data = [];
            $load_value_data = [];
            $capacity_data = [];
            
            // Generate tanggal untuk 7 hari terakhir
            $dates = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dates[] = $date->format('d M Y');
                
                // Hitung total capacity
                $total_capacity_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum('capacity');
                });

                // Hitung total units
                $total_units_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->count();
                });

                // Hitung active units
                $active_units_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->filter(function($machine) {
                        return $machine->statusLogs->first() && 
                               $machine->statusLogs->first()->status === 'Operasi';
                    })->count();
                });
            }

            // Hitung statistik
            $total_capacity = array_sum($total_capacity_data) / count($total_capacity_data);
            $total_units = array_sum($total_units_data) / count($total_units_data);
            $active_units = array_sum($active_units_data) / count($active_units_data);

            // Siapkan data untuk grafik beban tak tersalur
            $datasets = [];
            foreach ($powerPlants as $plant) {
                $unservedLoadData = [];
                
                foreach ($dates as $date) {
                    $dateFormatted = Carbon::createFromFormat('d M Y', $date)->format('Y-m-d');
                    
                    // Perbaikan query untuk menghitung beban tak tersalur
                    $totalUnserved = $plant->machines()
                        ->with(['statusLogs' => function($query) use ($dateFormatted) {
                            $query->whereDate('tanggal', $dateFormatted)
                                  ->whereIn('status', ['Gangguan', 'Mothballed', 'Overhaul']);
                        }])
                        ->get()
                        ->sum(function($machine) {
                            // Ambil status log terakhir pada tanggal tersebut
                            $lastLog = $machine->statusLogs->first();
                            // Jika status dalam kondisi gangguan/mothballed/overhaul, ambil DMP nya
                            return $lastLog ? $lastLog->dmp : 0;
                        });
                    
                    $unservedLoadData[] = floatval($totalUnserved);
                }
                
                // Debug log untuk melihat data per pembangkit
                \Log::info("Beban tak tersalur untuk {$plant->name}:", [
                    'data' => $unservedLoadData
                ]);
                
                $datasets[] = [
                    'name' => $plant->name,
                    'data' => array_values($unservedLoadData)
                ];
            }

            $chartData = [
                'dates' => array_values($dates),
                'datasets' => $datasets
            ];

            // Debug log untuk melihat keseluruhan data
            \Log::info('Final Chart Data:', $chartData);

            return view('homepage', compact(
                'statusLogs',
                'powerPlants',
                'total_capacity',
                'total_units',
                'active_units',
                'total_capacity_data',
                'total_units_data',
                'active_units_data',
                'dmn_data',
                'dmp_data',
                'load_value_data',
                'capacity_data',
                'dates',
                'chartData'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in HomeController@index: ' . $e->getMessage());
            return view('homepage', [
                'statusLogs' => collect([]),
                'powerPlants' => collect([]),
                'total_capacity' => 0,
                'total_units' => 0,
                'active_units' => 0,
                'total_capacity_data' => [],
                'total_units_data' => [],
                'active_units_data' => [],
                'dmn_data' => [],
                'dmp_data' => [],
                'load_value_data' => [],
                'capacity_data' => [],
                'dates' => [],
                'chartData' => [
                    'dates' => [],
                    'datasets' => []
                ]
            ]);
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
        try {
            \Log::info('Starting getPlantChartData', ['plant_id' => $plantId]);
            
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays(6);

            $query = MachineStatusLog::query()
                ->join('machines', 'machines.id', '=', 'machine_status_logs.machine_id')
                ->where('machines.power_plant_id', $plantId)
                ->whereBetween('machine_status_logs.created_at', [$startDate, $endDate])
                ->selectRaw('
                    DATE(machine_status_logs.created_at) as date,
                    COALESCE(SUM(machine_status_logs.load_value), 0) as total_load,
                    COALESCE(SUM(machines.capacity), 0) as total_capacity
                ')
                ->groupBy('date')
                ->orderBy('date');

            \Log::info('Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

            $chartData = $query->get();

            if ($chartData->isEmpty()) {
                \Log::warning('No data found for plant', ['plant_id' => $plantId]);
                return response()->json([
                    'dates' => [],
                    'beban' => [],
                    'kapasitas' => []
                ]);
            }

            $formattedData = [
                'dates' => $chartData->pluck('date')->map(function($date) {
                    return Carbon::parse($date)->format('d/m');
                })->values()->all(),
                'beban' => $chartData->pluck('total_load')->map(function($value) {
                    return (float) $value;
                })->values()->all(),
                'kapasitas' => $chartData->pluck('total_capacity')->map(function($value) {
                    return (float) $value;
                })->values()->all()
            ];

            \Log::info('Successfully retrieved chart data', ['data' => $formattedData]);

            return response()->json($formattedData);

        } catch (\Exception $e) {
            \Log::error('Error in getPlantChartData', [
                'plant_id' => $plantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load chart data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
