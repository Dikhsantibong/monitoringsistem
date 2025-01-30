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
            // Ambil data power plants dengan eager loading yang tepat
            $powerPlants = PowerPlant::with(['machines.statusLogs' => function($query) {
                $query->whereIn('status', ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'])
                      ->latest();
            }])->get();
            
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
                $dates[] = now()->subDays($i)->format('d M Y');
                
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
                    $totalUnserved = 0;

                    foreach ($plant->machines as $machine) {
                        // Cek status mesin pada tanggal tersebut
                        $statusLog = MachineStatusLog::where('machine_id', $machine->id)
                            ->whereDate('tanggal', $dateFormatted)
                            ->whereIn('status', ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'])
                            ->first();

                        if ($statusLog) {
                            // Jika ada status gangguan, ambil DMP dari MachineOperation terakhir sebelum gangguan
                            $lastOperation = MachineOperation::where('machine_id', $machine->id)
                                ->whereDate('recorded_at', '<=', $dateFormatted)
                                ->orderBy('recorded_at', 'desc')
                                ->first();

                            if ($lastOperation) {
                                $totalUnserved += floatval($lastOperation->dmp);
                            }
                        }
                    }
                    
                    $unservedLoadData[] = round($totalUnserved, 2);
                }
                
                // Hanya tambahkan ke dataset jika ada beban tak tersalur
                if (array_sum($unservedLoadData) > 0) {
                    $datasets[] = [
                        'name' => $plant->name,
                        'data' => $unservedLoadData
                    ];
                }
            }

            // Debug log
            \Log::info('Chart Data:', [
                'dates' => $dates,
                'datasets' => $datasets
            ]);

            $chartData = [
                'dates' => $dates,
                'datasets' => $datasets
            ];

            // Tambahkan perhitungan total DMP untuk semua mesin
            $totalPowerPlantCapacity = [];
            foreach ($dates as $date) {
                $dateFormatted = Carbon::createFromFormat('d M Y', $date)->format('Y-m-d');
                $totalDMP = 0;
                
                foreach ($powerPlants as $plant) {
                    foreach ($plant->machines as $machine) {
                        // Ambil data operasi terakhir sebelum atau pada tanggal tersebut
                        $lastOperation = MachineOperation::where('machine_id', $machine->id)
                            ->whereDate('recorded_at', '<=', $dateFormatted)
                            ->orderBy('recorded_at', 'desc')
                            ->first();
                            
                        if ($lastOperation) {
                            $totalDMP += floatval($lastOperation->dmp);
                        }
                    }
                }
                $totalPowerPlantCapacity[$dateFormatted] = $totalDMP;
            }

            
            $chartData['totalCapacity'] = $totalPowerPlantCapacity;
            
            // Hitung status mesin untuk hari ini
            $machineStatus = [
                'Operasi' => 0,
                'Standby' => 0,
                'Gangguan' => 0,
                'Pemeliharaan' => 0,
                'Mothballed' => 0,
                'Overhaul' => 0
            ];

            $totalMachines = 0;
            
            foreach ($powerPlants as $plant) {
                foreach ($plant->machines as $machine) {
                    $totalMachines++;
                    
                    // Ambil status terakhir mesin hari ini
                    $latestStatus = $machine->statusLogs()
                        ->whereDate('tanggal', now())
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($latestStatus) {
                        $machineStatus[$latestStatus->status]++;
                    }
                }
            }

            // Hitung persentase kesiapan (Operasi + Standby)
            $readyMachines = $machineStatus['Operasi'] + $machineStatus['Standby'];
            $machineReadiness = $totalMachines > 0 ? 
                round(($readyMachines / $totalMachines) * 100, 1) : 0;

            // Detail status untuk ditampilkan
            $statusDetails = [
                'ready' => [
                    'count' => $readyMachines,
                    'percentage' => $machineReadiness
                ],
                'notReady' => [
                    'count' => $totalMachines - $readyMachines,
                    'percentage' => 100 - $machineReadiness
                ],
                'breakdown' => [
                    'Operasi' => $machineStatus['Operasi'],
                    'Standby' => $machineStatus['Standby'],
                    'Gangguan' => $machineStatus['Gangguan'],
                    'Pemeliharaan' => $machineStatus['Pemeliharaan'],
                    'Mothballed' => $machineStatus['Mothballed'],
                    'Overhaul' => $machineStatus['Overhaul']
                ]
            ];

            $chartData['machineReadiness'] = $machineReadiness;
            $chartData['statusDetails'] = $statusDetails;

            // Hitung detail beban tersalur
            $totalCapacity = 0;
            
            $totalUnserved = 0;
            
            // Ambil data hari ini
            $today = now()->format('Y-m-d');
            
            foreach ($powerPlants as $plant) {
                foreach ($plant->machines as $machine) {
                    // Ambil DMP terakhir dari MachineOperation
                    $lastOperation = MachineOperation::where('machine_id', $machine->id)
                        ->whereDate('recorded_at', '<=', $today)
                        ->orderBy('recorded_at', 'desc')
                        ->first();

                    if ($lastOperation) {
                        $totalCapacity += floatval($lastOperation->dmp);
                        
                        // Cek apakah ada status gangguan
                        $statusLog = MachineStatusLog::where('machine_id', $machine->id)
                            ->whereDate('tanggal', $today)
                            ->whereIn('status', ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'])
                            ->first();
                            
                        if ($statusLog) {
                            $totalUnserved += floatval($lastOperation->dmp);
                        }
                    }
                }
            }

            $delivered = $totalCapacity - $totalUnserved;
            $deliveryPercentage = $totalCapacity > 0 ? 
                round(($delivered / $totalCapacity) * 100, 1) : 0;

            // Tambahkan ke chartData
            $chartData['powerDeliveryDetails'] = [
                'total' => $totalCapacity,
                'delivered' => $delivered,
                'undelivered' => $totalUnserved,
                'percentage' => $deliveryPercentage
            ];
            
            $chartData['powerDeliveryPercentage'] = $deliveryPercentage;

            return view('homepage', compact(
                'statusLogs',
                'powerPlants',
                'total_capacity_data',
                'total_units_data',
                'active_units_data',
                'dmn_data',
                'dmp_data',
                'load_value_data',
                'capacity_data',
                'dates',
                'total_capacity',
                'total_units',
                'active_units',
                'chartData'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in HomeController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return view('homepage', [
                'statusLogs' => collect([]),
                'powerPlants' => collect([]),
                'total_capacity_data' => [],
                'total_units_data' => [],
                'active_units_data' => [],
                'dmn_data' => [],
                'dmp_data' => [],
                'load_value_data' => [],
                'capacity_data' => [],
                'dates' => [],
                'total_capacity' => 0,
                'total_units' => 0,
                'active_units' => 0,
                'chartData' => [
                    'dates' => [],
                    'datasets' => [],
                    'machineReadiness' => 0,
                    'statusDetails' => [
                        'ready' => ['count' => 0, 'percentage' => 0],
                        'notReady' => ['count' => 0, 'percentage' => 0],
                        'breakdown' => [
                            'Operasi' => 0, 'Standby' => 0, 'Gangguan' => 0,
                            'Pemeliharaan' => 0, 'Mothballed' => 0, 'Overhaul' => 0
                        ]
                    ],
                    'powerDeliveryDetails' => [
                        'total' => 0,
                        'delivered' => 0,
                        'undelivered' => 0,
                        'percentage' => 0
                    ],
                    'powerDeliveryPercentage' => 0
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