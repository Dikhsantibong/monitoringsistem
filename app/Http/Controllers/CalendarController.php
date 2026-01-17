<?php

namespace App\Http\Controllers;

use App\Models\MachineStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari query string, default ke bulan & tahun sekarang
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        // Filter status, worktype, dan unit
        $statusFilter = $request->input('status');
        $workTypeFilter = $request->input('worktype');
        $unitFilter = $request->input('unit');
        
        // Parameter hari peringatan backlog (default 5 hari - H-5)
        $backlogWarningDays = $request->input('backlog_warning_days', 5);

        // Tanggal awal dan akhir bulan
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = (clone $firstDay)->endOfMonth();

        // Get work orders dari Maximo (Oracle) untuk bulan & tahun yang dipilih
        $workOrders = collect();
        $workOrdersRaw = collect(); // Inisialisasi untuk worktype stats
        try {
            $workOrdersQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%'); // Hanya ambil WO yang dimulai dengan "WO", bukan "WT"
            
            // Filter Status
            if ($statusFilter) {
                $workOrdersQuery->where('STATUS', $statusFilter);
            }
            
            // Filter Work Type
            if ($workTypeFilter) {
                $workOrdersQuery->where('WORKTYPE', $workTypeFilter);
            }
            
            // Filter Unit (berdasarkan prefix LOCATION)
            if ($unitFilter) {
                $workOrdersQuery->where('LOCATION', 'LIKE', $unitFilter . '%');
            }
            
            // Filter berdasarkan bulan dan tahun dari REPORTDATE atau STATUSDATE
            $workOrdersQuery->where(function ($q) use ($year, $month) {
                $q->where(function ($subQ) use ($year, $month) {
                    $subQ->whereNotNull('REPORTDATE')
                        ->whereRaw("TO_CHAR(REPORTDATE, 'YYYY') = ?", [$year])
                        ->whereRaw("TO_CHAR(REPORTDATE, 'MM') = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
                })->orWhere(function ($subQ) use ($year, $month) {
                    $subQ->whereNotNull('STATUSDATE')
                        ->whereNull('REPORTDATE')
                        ->whereRaw("TO_CHAR(STATUSDATE, 'YYYY') = ?", [$year])
                        ->whereRaw("TO_CHAR(STATUSDATE, 'MM') = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
                });
            });
            
            $workOrdersRaw = $workOrdersQuery->get();
            
            $now = Carbon::now();
            $workOrders = $workOrdersRaw->map(function ($wo) use ($now, $backlogWarningDays) {
                // Gunakan REPORTDATE jika ada, fallback ke STATUSDATE
                $dateField = $wo->reportdate ?? $wo->statusdate;
                $date = $dateField ? Carbon::parse($dateField) : now();
                
                // Status yang sudah selesai (tidak perlu dihitung backlog)
                $completedStatuses = ['COMP', 'CLOSE'];
                $currentStatus = strtoupper($wo->status ?? '');
                $isCompleted = in_array($currentStatus, $completedStatuses);
                
                // Hitung backlog days (hanya untuk status yang belum selesai)
                $backlogDays = null;
                $isBacklog = false;
                $backlogStatus = null; // 'overdue', 'warning', 'normal'
                
                if (!$isCompleted && isset($wo->schedfinish) && $wo->schedfinish) {
                    $scheduleFinish = Carbon::parse($wo->schedfinish)->startOfDay();
                    $nowStartOfDay = $now->copy()->startOfDay();
                    
                    // Hitung selisih hari dengan memastikan integer
                    $diffDays = (int) $nowStartOfDay->diffInDays($scheduleFinish, false); // false = tidak absolute, negatif jika sudah lewat
                    
                    if ($diffDays < 0) {
                        // Sudah backlog (schedule_finish sudah lewat)
                        $isBacklog = true;
                        $backlogStatus = 'overdue';
                        $backlogDays = abs($diffDays); // Convert ke positif untuk display (sudah berapa hari backlog)
                    } elseif ($diffDays <= $backlogWarningDays && $diffDays >= 0) {
                        // Warning: akan backlog dalam X hari atau kurang (sesuai parameter)
                        $backlogStatus = 'warning';
                        $backlogDays = $diffDays; // Jumlah hari tersisa (masih positif)
                    } else {
                        // Normal: masih ada waktu lebih dari X hari
                        $backlogStatus = 'normal';
                        $backlogDays = null; // Tidak perlu ditampilkan
                    }
                }
                
                return [
                    'id' => $wo->wonum ?? '-',
                    'type' => 'Work Order - ' . ($wo->worktype ?? 'N/A'),
                    'description' => $wo->description ?? '-',
                    'date' => $date->format('Y-m-d'),
                    'status' => $wo->status ?? 'Pending',
                    'priority' => $wo->wopriority ?? null,
                    'schedule_start' => isset($wo->schedstart) ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s') : null,
                    'schedule_finish' => isset($wo->schedfinish) ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s') : null,
                    'unit_source' => $wo->siteid ?? 'KD',
                    'power_plant_name' => $wo->location ?? ($wo->siteid ?? 'KD'), // Gunakan LOCATION atau SITEID sebagai power plant name
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'updated_at' => isset($wo->statusdate) ? Carbon::parse($wo->statusdate)->format('Y-m-d H:i:s') : $date->format('Y-m-d H:i:s'),
                    'labor' => null, // Labor tidak ada di Maximo
                    'is_backlog' => $isBacklog,
                    'backlog_days' => $backlogDays,
                    'backlog_status' => $backlogStatus,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in CalendarController: ' . $e->getMessage());
            $workOrders = collect([]);
            $workOrdersRaw = collect([]);
        }

        // Backlog tidak ada di Maximo, set empty collection
        $backlogs = collect([]);

        // Generate maintenance notifications when cumulative JSMO crosses thresholds
        $thresholds = [
            125 => 'P2',
            250 => 'P3',
            500 => 'P4',
            3000 => 'P5',
        ];
        // Ambil log hingga akhir bulan untuk menghitung kumulatif
        $logs = MachineStatusLog::with(['machine.powerPlant'])
            ->select('machine_id', 'tanggal', 'jsmo')
            ->whereDate('tanggal', '<=', $lastDay)
            ->orderBy('machine_id')
            ->orderBy('tanggal')
            ->get();
        $maintenanceEvents = collect();
        $logsByMachine = $logs->groupBy('machine_id');
        foreach ($logsByMachine as $machineId => $machineLogs) {
            $cumulative = 0.0;
            $triggered = [];
            foreach (array_keys($thresholds) as $t) { $triggered[$t] = false; }
            foreach ($machineLogs as $log) {
                $cumulative += (float) ($log->jsmo ?? 0);
                foreach ($thresholds as $limit => $label) {
                    if (!$triggered[$limit] && $cumulative >= $limit) {
                        $machine = optional($log->machine);
                        $powerPlant = optional($machine->powerPlant);
                        $maintenanceEvents->push([
                            'id' => 'M' . $machineId . '-' . $label,
                            'type' => 'Maintenance ' . $label,
                            'description' => sprintf('Mesin %s mencapai %d jam (%s)', (string) $machine->name, $limit, $label),
                            'date' => \Carbon\Carbon::parse($log->tanggal)->format('Y-m-d'),
                            'status' => 'Open',
                            'priority' => null,
                            'schedule_start' => null,
                            'schedule_finish' => null,
                            'unit_source' => null,
                            'power_plant_name' => (string) $powerPlant->name,
                            'created_at' => $log->tanggal,
                            'updated_at' => $log->tanggal,
                            'labor' => null,
                        ]);
                        $triggered[$limit] = true;
                    }
                }
            }
        }

        // Buat array tanggal satu bulan penuh
        $dates = [];
        $current = $firstDay->copy();
        while ($current->lte($lastDay)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Group terpisah: WO, Backlog, dan Maintenance
        $workOrdersByDate = $workOrders->groupBy('date');
        $backlogsByDate = $backlogs->groupBy('date');
        $maintenanceByDate = $maintenanceEvents->groupBy('date');

        // Buat peta tanggal -> events masing-masing
        $events = collect($dates)->mapWithKeys(function ($date) use ($workOrdersByDate) {
            return [$date => $workOrdersByDate->get($date, collect())];
        });
        $maintenanceEventsMap = collect($dates)->mapWithKeys(function ($date) use ($maintenanceByDate) {
            return [$date => $maintenanceByDate->get($date, collect())];
        });

        $backlogEventsMap = collect($dates)->mapWithKeys(function ($date) use ($backlogsByDate) {
            return [$date => $backlogsByDate->get($date, collect())];
        });

        // Opsi filter untuk dropdown
        $statusOptions = ['WAPPR', 'APPR', 'INPRG', 'COMP', 'CLOSE'];
        $workTypeOptions = ['CH', 'CM', 'CP', 'OH', 'OP', 'PAM', 'PDM', 'PM'];
        
        // Opsi filter Unit berdasarkan mapping LOCATION
        $unitOptions = [
            // ULPLTD KOLAKA
            'KLKA' => 'PLTD Kolaka',
            'LANI' => 'PLTD Lanipa-Nipa',
            'SABI' => 'PLTM Sabilambo',
            'MIKU' => 'PLTM Mikuasi',
            // ULPLTD BAU-BAU
            'BBAU' => 'PLTD Bau-Bau',
            'RAHA' => 'PLTD Raha',
            'WANG' => 'PLTD WANGI-WANGI',
            'EREK' => 'PLTD Ereke',
            'RONG' => 'PLTM Rongi',
            'WINN' => 'PLTM Winning',
            // ULPLTD POASIA
            'POAS' => 'PLTD Poasia',
            // ULPLTD WUA-WUA
            'WUAW' => 'PLTD Wua-Wua',
            'LANG' => 'PLTD Langara',
        ];

        // Hitung presentasi worktype berdasarkan total WO bulan ini
        // Gunakan data mentah untuk perhitungan yang lebih akurat
        $totalWO = $workOrdersRaw->count();
        $workTypeStats = [];
        
        if ($totalWO > 0) {
            // Hitung jumlah per worktype dari data mentah
            $workTypeCounts = $workOrdersRaw->groupBy('worktype')->map(function ($group) {
                return $group->count();
            });
            
            // Hitung persentase per worktype
            foreach ($workTypeCounts as $workType => $count) {
                $workTypeLabel = $workType ?? 'N/A';
                $percentage = round(($count / $totalWO) * 100, 1);
                $workTypeStats[$workTypeLabel] = [
                    'count' => $count,
                    'percentage' => $percentage,
                ];
            }
            
            // Sort by percentage descending
            uasort($workTypeStats, function ($a, $b) {
                return $b['percentage'] <=> $a['percentage'];
            });
        }

        // Hitung persentase WO Open vs WO Close
        $woOpenCount = 0;
        $woCloseCount = 0;
        $woOpenCloseStats = [];
        
        if ($totalWO > 0) {
            $completedStatuses = ['COMP', 'CLOSE'];
            
            foreach ($workOrdersRaw as $wo) {
                $status = strtoupper($wo->status ?? '');
                if (in_array($status, $completedStatuses)) {
                    $woCloseCount++;
                } else {
                    $woOpenCount++;
                }
            }
            
            $woOpenCloseStats = [
                'open' => [
                    'count' => $woOpenCount,
                    'percentage' => round(($woOpenCount / $totalWO) * 100, 1),
                ],
                'close' => [
                    'count' => $woCloseCount,
                    'percentage' => round(($woCloseCount / $totalWO) * 100, 1),
                ],
            ];
        }

        // Hitung persentase WO Terencana vs WO Tidak Terencana
        $woTerencanaCount = 0;
        $woTidakTerencanaCount = 0;
        $woTerencanaStats = [];
        
        if ($totalWO > 0) {
            $tidakTerencanaTypes = ['EM', 'CM'];
            
            foreach ($workOrdersRaw as $wo) {
                $workType = strtoupper($wo->worktype ?? '');
                if (in_array($workType, $tidakTerencanaTypes)) {
                    $woTidakTerencanaCount++;
                } else {
                    $woTerencanaCount++;
                }
            }
            
            $woTerencanaStats = [
                'terencana' => [
                    'count' => $woTerencanaCount,
                    'percentage' => round(($woTerencanaCount / $totalWO) * 100, 1),
                ],
                'tidak_terencana' => [
                    'count' => $woTidakTerencanaCount,
                    'percentage' => round(($woTidakTerencanaCount / $totalWO) * 100, 1),
                ],
            ];
        }

        // Untuk grid kalender, butuh info bulan & tahun
        return view('calendar.index', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'maintenanceEvents' => $maintenanceEventsMap,
            'backlogEvents' => $backlogEventsMap,
            'statusFilter' => $statusFilter,
            'workTypeFilter' => $workTypeFilter,
            'unitFilter' => $unitFilter,
            'statusOptions' => $statusOptions,
            'workTypeOptions' => $workTypeOptions,
            'unitOptions' => $unitOptions,
            'backlogWarningDays' => $backlogWarningDays,
            'workTypeStats' => $workTypeStats,
            'totalWO' => $totalWO,
            'woOpenCloseStats' => $woOpenCloseStats,
            'woTerencanaStats' => $woTerencanaStats,
        ]);
    }
}
