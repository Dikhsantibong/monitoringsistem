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
            // Ambil data power plants dengan relasi machines dan status logs
            $powerPlants = PowerPlant::with(['machines.statusLogs' => function($query) {
                $query->latest()->take(1);
            }])->get();

            // Inisialisasi array untuk data grafik dan tanggal
            $dates = [];
            $dmn_data = [];
            $dmp_data = [];
            $load_value_data = [];
            $capacity_data = [];
            $total_capacity_data = [];
            $total_units_data = [];
            $active_units_data = [];
            
            // Ambil data 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dates[] = $date->format('Y-m-d');
                
                // Capacity data
                $capacity_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum(function($machine) {
                        return $machine->capacity ?? 0;
                    });
                });

                // DMN dan DMP data
                $dmn_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum(function($machine) {
                        return $machine->statusLogs->first()->dmn ?? 0;
                    });
                });

                $dmp_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum(function($machine) {
                        return $machine->statusLogs->first()->dmp ?? 0;
                    });
                });

                // Load value data
                $load_value_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum(function($machine) {
                        return $machine->statusLogs->first()->load_value ?? 0;
                    });
                });

                // Total kapasitas
                $total_capacity_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->sum('capacity');
                });

                // Total unit
                $total_units_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->count();
                });

                // Unit aktif
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

            // Status logs
            try {
                $statusLogs = MachineStatusLog::with(['machine.powerPlant'])
                    ->whereIn('id', function($query) {
                        $query->selectRaw('MAX(id)')
                            ->from('machine_status_logs')
                            ->groupBy('machine_id');
                    })
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch status logs: ' . $e->getMessage());
                $statusLogs = collect([]);
            }

            // Tambahkan lastUpdate
            $lastUpdate = now()->format('Y-m-d H:i:s');

            return view('homepage', compact(
                'statusLogs',
                'powerPlants',
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
                'lastUpdate'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in HomeController@index: ' . $e->getMessage());
            return view('homepage', [
                'statusLogs' => collect([]),
                'powerPlants' => collect([]),
                'dmn_data' => [],
                'dmp_data' => [],
                'load_value_data' => [],
                'capacity_data' => [],
                'total_capacity_data' => [],
                'total_units_data' => [],
                'active_units_data' => [],
                'dates' => [],
                'lastUpdate' => now()->format('Y-m-d H:i:s'),
                'error' => 'Terjadi kesalahan saat memuat data.'
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
