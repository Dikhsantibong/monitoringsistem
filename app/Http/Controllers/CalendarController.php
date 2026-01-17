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
        
        // Filter status dan worktype
        $statusFilter = $request->input('status');
        $workTypeFilter = $request->input('worktype');

        // Tanggal awal dan akhir bulan
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = (clone $firstDay)->endOfMonth();

        // Get work orders dari Maximo (Oracle) untuk bulan & tahun yang dipilih
        $workOrders = collect();
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
                ->where('SITEID', 'KD');
            
            // Filter Status
            if ($statusFilter) {
                $workOrdersQuery->where('STATUS', $statusFilter);
            }
            
            // Filter Work Type
            if ($workTypeFilter) {
                $workOrdersQuery->where('WORKTYPE', $workTypeFilter);
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
            $workOrders = $workOrdersRaw->map(function ($wo) use ($now) {
                // Gunakan REPORTDATE jika ada, fallback ke STATUSDATE
                $dateField = $wo->reportdate ?? $wo->statusdate;
                $date = $dateField ? Carbon::parse($dateField) : now();
                
                // Hitung backlog days
                $backlogDays = null;
                $isBacklog = false;
                $backlogStatus = null; // 'overdue', 'warning', 'normal'
                
                if (isset($wo->schedfinish) && $wo->schedfinish) {
                    $scheduleFinish = Carbon::parse($wo->schedfinish);
                    $diffDays = $now->diffInDays($scheduleFinish, false); // false = tidak absolute, negatif jika sudah lewat
                    
                    if ($diffDays < 0) {
                        // Sudah backlog (schedule_finish sudah lewat)
                        $isBacklog = true;
                        $backlogStatus = 'overdue';
                        $backlogDays = abs($diffDays); // Convert ke positif untuk display (sudah berapa hari backlog)
                    } elseif ($diffDays <= 3 && $diffDays >= 0) {
                        // Warning: akan backlog dalam 3 hari atau kurang
                        $backlogStatus = 'warning';
                        $backlogDays = $diffDays; // Jumlah hari tersisa (masih positif)
                    } else {
                        // Normal: masih ada waktu lebih dari 3 hari
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
            'statusOptions' => $statusOptions,
            'workTypeOptions' => $workTypeOptions,
        ]);
    }
}
