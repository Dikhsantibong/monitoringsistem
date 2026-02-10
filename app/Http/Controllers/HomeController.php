<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\Notulen;
use App\Models\PowerPlant;
use App\Models\MachineOperation;
use App\Models\MachineStatusLog;
use App\Models\Attendance;
use App\Models\ScoreCardDaily;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get notulen data
            $notulens = Notulen::orderBy('tanggal', 'desc')
                              ->orderBy('waktu_mulai', 'desc')
                              ->take(10)
                              ->get();

            // Get the latest status for each machine
            $latestStatusSubquery = MachineStatusLog::select('machine_id',
                DB::raw('MAX(created_at) as max_created_at'))
                ->groupBy('machine_id');

            // Ambil data power plants dengan eager loading yang tepat
            $powerPlants = PowerPlant::with(['machines.statusLogs' => function($query) use ($latestStatusSubquery) {
                $query->joinSub($latestStatusSubquery, 'latest_status', function($join) {
                    $join->on('machine_status_logs.machine_id', '=', 'latest_status.machine_id')
                        ->on('machine_status_logs.created_at', '=', 'latest_status.max_created_at');
                });
            }])->get();

            // Ambil status logs untuk menampilkan riwayat gangguan
            $statusLogs = MachineStatusLog::with(['machine.powerPlant'])
                ->joinSub($latestStatusSubquery, 'latest_status', function($join) {
                    $join->on('machine_status_logs.machine_id', '=', 'latest_status.machine_id')
                        ->on('machine_status_logs.created_at', '=', 'latest_status.max_created_at');
                })
                ->whereNotIn('status', ['Operasi', 'Standby'])
                ->orderBy('tanggal', 'desc')
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

                // Hitung active units berdasarkan status terbaru hari ini
                $active_units_data[] = $powerPlants->sum(function($plant) {
                    return $plant->machines->filter(function($machine) {
                        return $machine->statusLogs->first() &&
                               $machine->statusLogs->first()->status === 'Operasi' &&
                               $machine->statusLogs->first()->tanggal->isToday();
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
            Log::info('Chart Data:', [
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
            $machinesByStatus = [
                'Operasi' => [],
                'Standby' => [],
                'Gangguan' => [],
                'Pemeliharaan' => [],
                'Mothballed' => [],
                'Overhaul' => []
            ];

            foreach ($powerPlants as $plant) {
                foreach ($plant->machines as $machine) {
                    $totalMachines++;

                    // Get the latest status log for this machine
                    $latestStatus = MachineStatusLog::where('machine_id', $machine->id)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($latestStatus) {
                        $status = $latestStatus->status;
                        // Make sure the status exists in our arrays
                        if (!isset($machineStatus[$status])) {
                            $machineStatus[$status] = 0;
                            $machinesByStatus[$status] = [];
                        }

                        $machineStatus[$status]++;
                        $machineName = $machine->name . ' (' . $plant->name . ')';
                        $machinesByStatus[$status][] = $machineName;

                        // Debug log
                        Log::info("Machine status recorded", [
                            'machine' => $machineName,
                            'status' => $status,
                            'date' => $latestStatus->tanggal
                        ]);
                    } else {
                        // If no status found, log it
                        Log::warning("No status found for machine", [
                            'machine_id' => $machine->id,
                            'machine_name' => $machine->name,
                            'plant_name' => $plant->name
                        ]);
                    }
                }
            }

            // Debug log for final counts
            Log::info("Final machine status counts", [
                'status_counts' => $machineStatus,
                'machines_by_status' => array_map('count', $machinesByStatus)
            ]);

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
                ],
                'machineNames' => $machinesByStatus
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

            // Tambahkan: Data Kehadiran Harian & Score Daily Meeting (30 hari terakhir)
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            $dates = collect();
            for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                $dates->push($date->format('Y-m-d'));
            }

            // Dummy Data Kehadiran harian (Min 20)
            $attendanceCounts = collect();
            foreach ($dates as $dateStr) {
                $attendanceCounts->push(rand(20, 30));
            }
            $attendanceChartData = [
                'labels' => $dates->toArray(),
                'data' => $attendanceCounts->toArray(),
            ];

            // Dummy Data Score Daily Meeting (85-95)
            $scoreChartData = [
                'labels' => $dates->toArray(),
                'data' => $dates->map(fn($d) => rand(85, 95))->toArray(),
            ];

            // --- Grafik Presentasi Status SR/WO/Backlog (Maximo - Oracle) ---
            $srStatusData = ['counts' => [0, 0]];
            $woStatusData = ['counts' => [0, 0]];
            $woBacklogStatusData = [
                'Overdue' => 0,
                'Warning (H-5)' => 0,
                'Normal' => 0,
                'No Schedule' => 0,
            ];
            $maximoLastUpdate = [
                'sr' => null,
                'wo' => null,
                'backlog' => null,
            ];

            try {
                // SR Open/Closed dari Maximo
                $srOpen = DB::connection('oracle')
                    ->table('SR')
                    ->where('SITEID', 'KD')
                    ->whereIn('STATUS', ['NEW', 'WOCREATED', 'QUEVED'])
                    ->count();

                $srClosed = DB::connection('oracle')
                    ->table('SR')
                    ->where('SITEID', 'KD')
                    ->whereIn('STATUS', ['RESOLVED', 'CLOSED'])
                    ->count();

                $srStatusData = ['counts' => [$srOpen, $srClosed]];

                // WO Open/Closed dari Maximo (filter WONUM WO% agar tidak ikut WT)
                $woOpen = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('STATUS', ['WAPPR', 'APPR', 'INPRG'])
                    ->count();

                $woClosed = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereIn('STATUS', ['COMP', 'CLOSE'])
                    ->count();

                $woStatusData = ['counts' => [$woOpen, $woClosed]];

                // Last update (ambil max STATUSDATE)
                $srLast = DB::connection('oracle')
                    ->table('SR')
                    ->where('SITEID', 'KD')
                    ->max('STATUSDATE');
                $woLast = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->max('STATUSDATE');

                $maximoLastUpdate['sr'] = $srLast ? Carbon::parse($srLast)->format('d/m/Y H:i') : null;
                $maximoLastUpdate['wo'] = $woLast ? Carbon::parse($woLast)->format('d/m/Y H:i') : null;
                $maximoLastUpdate['backlog'] = $maximoLastUpdate['wo'];

                // Backlog (berdasarkan SCHEDFINISH vs hari ini, hanya WO belum selesai)
                $warningDays = 5;
                $overdue = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereNotIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereNotNull('SCHEDFINISH')
                    ->whereRaw('TRUNC(SCHEDFINISH) < TRUNC(SYSDATE)')
                    ->count();

                $warning = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereNotIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereNotNull('SCHEDFINISH')
                    ->whereRaw('TRUNC(SCHEDFINISH) >= TRUNC(SYSDATE)')
                    ->whereRaw('TRUNC(SCHEDFINISH) <= TRUNC(SYSDATE) + ?', [$warningDays])
                    ->count();

                $normal = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereNotIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereNotNull('SCHEDFINISH')
                    ->whereRaw('TRUNC(SCHEDFINISH) > TRUNC(SYSDATE) + ?', [$warningDays])
                    ->count();

                $noSchedule = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->whereNotIn('STATUS', ['COMP', 'CLOSE'])
                    ->whereNull('SCHEDFINISH')
                    ->count();

                $woBacklogStatusData = [
                    'Overdue' => $overdue,
                    'Warning (H-5)' => $warning,
                    'Normal' => $normal,
                    'No Schedule' => $noSchedule,
                ];
            } catch (\Throwable $e) {
                Log::warning('Failed getting Maximo SR/WO data for homepage charts', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Status update harian per unit (machine status, attendance, score card)
            $unitUpdateStatuses = $this->getUnitUpdateStatuses();

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
                'chartData',
                'notulens',
                'attendanceChartData',
                'scoreChartData',
                'srStatusData',
                'woStatusData',
                'woBacklogStatusData',
                'maximoLastUpdate',
                'unitUpdateStatuses'
            ));

        } catch (\Exception $e) {
            Log::error('Error in HomeController@index: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

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
                ],
                'notulens' => collect([]),
                'attendanceChartData' => [
                    'labels' => [],
                    'data' => []
                ],
                'scoreChartData' => [
                    'labels' => [],
                    'data' => []
                ],
                'srStatusData' => [
                    'counts' => [0, 0]
                ],
                'woStatusData' => [
                    'counts' => [0, 0]
                ],
                'woBacklogStatusData' => [],
                'maximoLastUpdate' => [
                    'sr' => null,
                    'wo' => null,
                    'backlog' => null,
                ],
                'unitUpdateStatuses' => []
            ]);
        }
    }

    /**
     * Menghitung status update harian (hari ini) untuk setiap unit/koneksi.
     * Mengecek keberadaan data di tiga tabel: machine_status_logs, attendance, score_card_daily.
     */
    private function getUnitUpdateStatuses(): array
    {
        $today = Carbon::today();
        $connections = [
            'mysql',
            'mysql_poasia',
            'mysql_kolaka',
            'mysql_wua_wua',
            'mysql_bau_bau',
        ];

        $statuses = [];

        foreach ($connections as $connection) {
            $statuses[$connection] = [
                'machine_status_logs' => $this->hasTodayData($connection, 'machine_status_logs', 'tanggal', $today),
                'attendance' => $this->hasTodayData($connection, 'attendance', 'time', $today),
                'score_card_daily' => $this->hasTodayData($connection, 'score_card_daily', 'tanggal', $today),
            ];
        }

        return $statuses;
    }

    /**
     * Mengecek apakah tabel memiliki data pada tanggal hari ini.
     */
    private function hasTodayData(string $connection, string $table, string $dateColumn, Carbon $today): bool
    {
        try {
            return DB::connection($connection)
                ->table($table)
                ->whereDate($dateColumn, $today)
                ->exists();
        } catch (\Throwable $th) {
            Log::warning('Failed checking daily update status', [
                'connection' => $connection,
                'table' => $table,
                'error' => $th->getMessage(),
            ]);
            return false;
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
            Log::info('Status Logs Data:', ['data' => $statusLogs]);

            if ($statusLogs->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada data gangguan untuk pembangkit ini',
                    'status' => 'empty'
                ]);
            }

            return response()->json($statusLogs);

        } catch (\Exception $e) {
            Log::error('Error in getAccumulationData: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function getPlantChartData($plantId)
    {
        try {
            Log::info('Starting getPlantChartData', ['plant_id' => $plantId]);

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

            Log::info('Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

            $chartData = $query->get();

            if ($chartData->isEmpty()) {
                Log::warning('No data found for plant', ['plant_id' => $plantId]);
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

            Log::info('Successfully retrieved chart data', ['data' => $formattedData]);

            return response()->json($formattedData);

        } catch (\Exception $e) {
            Log::error('Error in getPlantChartData', [
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

    public function getMonitoringData($period)
    {
        try {
            Log::info('getMonitoringData called', [
                'period' => $period,
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'headers' => request()->headers->all()
            ]);

            Log::info('Starting getMonitoringData', ['period' => $period]);

            // Set tanggal berdasarkan periode
            $endDate = now();
            $startDate = match($period) {
                'daily' => now()->subDays(7),
                'weekly' => now()->subWeeks(4),
                'monthly' => now()->subMonths(12),
                default => now()->subDays(7)
            };

            Log::info('Date range', [
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d')
            ]);

            // Ambil data sesuai periode
            $powerPlants = PowerPlant::with(['machines.statusLogs' => function($query) use ($startDate) {
                $query->where('tanggal', '>=', $startDate)
                      ->whereIn('status', ['Gangguan', 'Pemeliharaan', 'Mothballed', 'Overhaul'])
                      ->latest();
            }])->get();

            Log::info('Power Plants retrieved', ['count' => $powerPlants->count()]);

            // Siapkan data untuk response
            $dates = [];
            $datasets = [];

            // Format tanggal sesuai periode
            $dateFormat = match($period) {
                'daily' => 'd M',
                'weekly' => 'W/Y',
                'monthly' => 'M Y',
                default => 'd M'
            };

            // Generate dates
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format($dateFormat);
                $currentDate = match($period) {
                    'daily' => $currentDate->addDay(),
                    'weekly' => $currentDate->addWeek(),
                    'monthly' => $currentDate->addMonth(),
                    default => $currentDate->addDay()
                };
            }

            Log::info('Dates generated', ['dates' => $dates]);

            // Hitung data untuk setiap pembangkit
            foreach ($powerPlants as $plant) {
                $unservedLoadData = array_fill(0, count($dates), 0);

                foreach ($plant->machines as $machine) {
                    foreach ($machine->statusLogs as $log) {
                        $logDate = Carbon::parse($log->tanggal)->format($dateFormat);
                        $dateIndex = array_search($logDate, $dates);

                        if ($dateIndex !== false) {
                            $lastOperation = MachineOperation::where('machine_id', $machine->id)
                                ->whereDate('recorded_at', '<=', $log->tanggal)
                                ->orderBy('recorded_at', 'desc')
                                ->first();

                            if ($lastOperation) {
                                $unservedLoadData[$dateIndex] += floatval($lastOperation->dmp);
                            }
                        }
                    }
                }

                if (array_sum($unservedLoadData) > 0) {
                    $datasets[] = [
                        'name' => $plant->name,
                        'data' => array_map(function($value) {
                            return round($value, 2);
                        }, $unservedLoadData)
                    ];
                }
            }

            Log::info('Datasets prepared', ['datasets' => $datasets]);

            // Hitung statistik terkini
            $currentStats = $this->calculateCurrentStats($powerPlants);

            Log::info('Current stats calculated', $currentStats);

            $response = [
                'dates' => $dates,
                'datasets' => $datasets,
                'machineReadiness' => $currentStats['machineReadiness'] ?? 0,
                'statusDetails' => $currentStats['statusDetails'] ?? [
                    'ready' => ['count' => 0, 'percentage' => 0],
                    'notReady' => ['count' => 0, 'percentage' => 0],
                    'breakdown' => [
                        'Operasi' => 0,
                        'Standby' => 0,
                        'Gangguan' => 0,
                        'Pemeliharaan' => 0,
                        'Mothballed' => 0,
                        'Overhaul' => 0
                    ]
                ],
                'powerDeliveryDetails' => $currentStats['powerDeliveryDetails'] ?? [
                    'total' => 0,
                    'delivered' => 0,
                    'undelivered' => 0,
                    'percentage' => 0
                ],
                'powerDeliveryPercentage' => $currentStats['powerDeliveryPercentage'] ?? 0
            ];

            Log::info('Response prepared', ['response' => $response]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('getMonitoringData error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'period' => $period,
                'url' => request()->fullUrl()
            ]);

            $errorDetail = config('app.debug') ? [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'time' => now()->toDateTimeString()
            ] : ['message' => 'Terjadi kesalahan pada server'];

            return response()->json([
                'error' => true,
                'detail' => $errorDetail,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateCurrentStats($powerPlants)
    {
        try {
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
            $machinesByStatus = [
                'Operasi' => [],
                'Standby' => [],
                'Gangguan' => [],
                'Pemeliharaan' => [],
                'Mothballed' => [],
                'Overhaul' => []
            ];

            foreach ($powerPlants as $plant) {
                foreach ($plant->machines as $machine) {
                    $totalMachines++;

                    // Get the latest status log for this machine
                    $latestStatus = MachineStatusLog::where('machine_id', $machine->id)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($latestStatus) {
                        $status = $latestStatus->status;
                        // Make sure the status exists in our arrays
                        if (!isset($machineStatus[$status])) {
                            $machineStatus[$status] = 0;
                            $machinesByStatus[$status] = [];
                        }

                        $machineStatus[$status]++;
                        $machineName = $machine->name . ' (' . $plant->name . ')';
                        $machinesByStatus[$status][] = $machineName;
                    }
                }
            }

            // Hitung persentase kesiapan (Operasi + Standby)
            $readyMachines = $machineStatus['Operasi'] + $machineStatus['Standby'];
            $machineReadiness = $totalMachines > 0 ?
                round(($readyMachines / $totalMachines) * 100, 1) : 0;

            // Hitung total kapasitas dan beban tak tersalur
            $totalCapacity = 0;
            $totalUnserved = 0;
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

            // Hitung persentase daya tersalur
            $delivered = $totalCapacity - $totalUnserved;
            $powerDeliveryPercentage = $totalCapacity > 0 ?
                round(($delivered / $totalCapacity) * 100, 1) : 0;

            return [
                'machineReadiness' => $machineReadiness,
                'statusDetails' => [
                    'ready' => [
                        'count' => $readyMachines,
                        'percentage' => $machineReadiness
                    ],
                    'notReady' => [
                        'count' => $totalMachines - $readyMachines,
                        'percentage' => 100 - $machineReadiness
                    ],
                    'breakdown' => $machineStatus,
                    'machineNames' => $machinesByStatus
                ],
                'powerDeliveryDetails' => [
                    'total' => $totalCapacity,
                    'delivered' => $delivered,
                    'undelivered' => $totalUnserved,
                    'percentage' => $powerDeliveryPercentage
                ],
                'powerDeliveryPercentage' => $powerDeliveryPercentage
            ];
        } catch (\Exception $e) {
            Log::error('Error in calculateCurrentStats', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getEngineIssues($markerId)
    {
        try {
            // Get the power plant
            $powerPlant = PowerPlant::find($markerId);

            if (!$powerPlant) {
                return response()->json([
                    'message' => 'Power Plant tidak ditemukan',
                    'status' => 'error'
                ], 404);
            }

            // Get all machines from the power plant
            $machineIds = $powerPlant->machines()->pluck('id')->toArray();

            // Get all status logs with component and equipment issues
            // Remove any potential date filtering to show all records
            $engineIssues = MachineStatusLog::with(['machine', 'machine.powerPlant'])
                ->whereIn('machine_id', $machineIds)
                ->where('component', 'Ada')
                ->whereNotNull('equipment')
                ->orderBy('created_at', 'desc') // Changed from tanggal to created_at to ensure we get latest entries first
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'tanggal' => $log->tanggal,
                        'machine_name' => $log->machine->name,
                        'power_plant_name' => $log->machine->powerPlant->name,
                        'component' => $log->component,
                        'equipment' => $log->equipment,
                        'progres' => $log->progres,
                        'status' => $log->status
                    ];
                });

            // Debug log to check the count of records
            Log::info('Engine Issues Count:', ['count' => $engineIssues->count()]);

            if ($engineIssues->isEmpty()) {
                return response()->json([
                    'message' => 'Tidak ada issue engine untuk pembangkit ini',
                    'status' => 'empty'
                ]);
            }

            return response()->json($engineIssues);

        } catch (\Exception $e) {
            Log::error('Error in getEngineIssues: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
