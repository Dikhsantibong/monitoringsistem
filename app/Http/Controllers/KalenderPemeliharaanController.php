<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KalenderPemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        
        // Filter status, worktype, dan unit
        $statusFilter = $request->input('status');
        $workTypeFilter = $request->input('worktype');
        $unitFilter = $request->input('unit');
        
        // Parameter hari peringatan backlog (default 5 hari - H-5)
        $backlogWarningDays = $request->input('backlog_warning_days', 5);
        
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

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
            
            // Filter Unit (berdasarkan UL - Unit Layanan)
            if ($unitFilter) {
                // Mapping UL ke kode-kode unit
                $ulMapping = [
                    'ULPLTD_KOLAKA' => ['KLKA', 'LANI', 'SABI', 'MIKU'],
                    'ULPLTD_BAU_BAU' => ['BBAU', 'RAHA', 'WANG', 'EREK', 'RONG', 'WINN'],
                    'ULPLTD_POASIA' => ['POAS'],
                    'ULPLTD_WUA_WUA' => ['WUAW', 'LANG'],
                ];
                
                if (isset($ulMapping[$unitFilter])) {
                    $unitCodes = $ulMapping[$unitFilter];
                    $workOrdersQuery->where(function ($q) use ($unitCodes) {
                        $firstCode = array_shift($unitCodes);
                        $q->where('LOCATION', 'LIKE', $firstCode . '%');
                        foreach ($unitCodes as $code) {
                            $q->orWhere('LOCATION', 'LIKE', $code . '%');
                        }
                    });
                }
            }
            
            // Filter berdasarkan bulan dan tahun dari SCHEDSTART
            $workOrdersQuery->where(function ($q) use ($year, $month) {
                $q->where(function ($subQ) use ($year, $month) {
                    $subQ->whereNotNull('SCHEDSTART')
                        ->whereRaw("TO_CHAR(SCHEDSTART, 'YYYY') = ?", [$year])
                        ->whereRaw("TO_CHAR(SCHEDSTART, 'MM') = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
                })->orWhere(function ($subQ) use ($year, $month) {
                    $subQ->whereNull('SCHEDSTART')
                        ->whereNotNull('REPORTDATE')
                        ->whereRaw("TO_CHAR(REPORTDATE, 'YYYY') = ?", [$year])
                        ->whereRaw("TO_CHAR(REPORTDATE, 'MM') = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
                })->orWhere(function ($subQ) use ($year, $month) {
                    $subQ->whereNull('SCHEDSTART')
                        ->whereNull('REPORTDATE')
                        ->whereNotNull('STATUSDATE')
                        ->whereRaw("TO_CHAR(STATUSDATE, 'YYYY') = ?", [$year])
                        ->whereRaw("TO_CHAR(STATUSDATE, 'MM') = ?", [str_pad($month, 2, '0', STR_PAD_LEFT)]);
                });
            });
            
            $workOrdersRaw = $workOrdersQuery->get();
            
            $now = Carbon::now();
            $workOrders = $workOrdersRaw->map(function ($wo) use ($now, $backlogWarningDays) {
                // Gunakan SCHEDSTART jika ada, fallback ke REPORTDATE atau STATUSDATE
                $dateField = $wo->schedstart ?? $wo->reportdate ?? $wo->statusdate;
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
                    'power_plant_name' => $wo->location ?? ($wo->siteid ?? 'KD'),
                    'created_at' => $date->format('Y-m-d H:i:s'),
                    'updated_at' => isset($wo->statusdate) ? Carbon::parse($wo->statusdate)->format('Y-m-d H:i:s') : $date->format('Y-m-d H:i:s'),
                    'labor' => null, // Labor tidak ada di Maximo
                    'is_backlog' => $isBacklog,
                    'backlog_days' => $backlogDays,
                    'backlog_status' => $backlogStatus,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in KalenderPemeliharaanController: ' . $e->getMessage());
            $workOrders = collect([]);
            $workOrdersRaw = collect([]);
        }

        // Backlog tidak ada di Maximo, set empty collection
        $backlogs = collect([]);

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

        // Buat peta tanggal -> events masing-masing
        $events = collect($dates)->mapWithKeys(function ($date) use ($workOrdersByDate) {
            return [$date => $workOrdersByDate->get($date, collect())];
        });

        $backlogEventsMap = collect($dates)->mapWithKeys(function ($date) use ($backlogsByDate) {
            return [$date => $backlogsByDate->get($date, collect())];
        });

        // Opsi filter untuk dropdown
        $statusOptions = ['WAPPR', 'APPR', 'INPRG', 'COMP', 'CLOSE'];
        $workTypeOptions = ['CH', 'CM', 'CP', 'OH', 'OP', 'PAM', 'PDM', 'PM'];
        
        // Opsi filter Unit berdasarkan UL (Unit Layanan)
        $unitOptions = [
            'ULPLTD_KOLAKA' => 'ULPLTD KOLAKA',
            'ULPLTD_BAU_BAU' => 'ULPLTD BAU-BAU',
            'ULPLTD_POASIA' => 'ULPLTD POASIA',
            'ULPLTD_WUA_WUA' => 'ULPLTD WUA-WUA',
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

        return view('kalender-pemeliharaan', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
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
