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
        
        // Filter status dan worktype
        $statusFilter = $request->input('status');
        $workTypeFilter = $request->input('worktype');
        
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

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
            
            $workOrders = $workOrdersRaw->map(function ($wo) {
                // Gunakan SCHEDSTART jika ada, fallback ke REPORTDATE atau STATUSDATE
                $dateField = $wo->schedstart ?? $wo->reportdate ?? $wo->statusdate;
                $date = $dateField ? Carbon::parse($dateField) : now();
                
                return [
                    'id' => $wo->wonum ?? '-',
                    'type' => $wo->worktype ?? 'N/A',
                    'description' => $wo->description ?? '-',
                    'status' => $wo->status ?? 'Pending',
                    'priority' => $wo->wopriority ?? null,
                    'schedule_start' => isset($wo->schedstart) ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s') : null,
                    'schedule_finish' => isset($wo->schedfinish) ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s') : null,
                    'power_plant_name' => $wo->location ?? ($wo->siteid ?? 'KD'),
                    'labor' => null, // Labor tidak ada di Maximo
                    'date' => $date->format('Y-m-d'), // Simpan date untuk grouping
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in KalenderPemeliharaanController: ' . $e->getMessage());
            $workOrders = collect([]);
        }

        // Backlog tidak ada di Maximo, set empty collection
        $backlogs = collect([]);

        // Kelompokkan event berdasarkan tanggal
        $events = [];
        foreach ($workOrders as $wo) {
            // Gunakan schedule_start jika ada, fallback ke date field
            $eventDate = $wo['schedule_start'] 
                ? Carbon::parse($wo['schedule_start'])->toDateString() 
                : ($wo['date'] ?? now()->toDateString());
            
            $events[$eventDate][] = [
                'id' => $wo['id'],
                'description' => $wo['description'],
                'type' => $wo['type'],
                'status' => $wo['status'],
                'priority' => $wo['priority'],
                'schedule_start' => $wo['schedule_start'],
                'schedule_finish' => $wo['schedule_finish'],
                'power_plant_name' => $wo['power_plant_name'],
                'labor' => $wo['labor'],
            ];
        }

        // Backlog events (kosong karena tidak ada di Maximo)
        $backlogEvents = [];

        // Opsi filter untuk dropdown
        $statusOptions = ['WAPPR', 'APPR', 'INPRG', 'COMP', 'CLOSE'];
        $workTypeOptions = ['CH', 'CM', 'CP', 'OH', 'OP', 'PAM', 'PDM', 'PM'];

        return view('kalender-pemeliharaan', [
            'month' => $month,
            'year' => $year,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'events' => $events,
            'backlogEvents' => $backlogEvents,
            'statusFilter' => $statusFilter,
            'workTypeFilter' => $workTypeFilter,
            'statusOptions' => $statusOptions,
            'workTypeOptions' => $workTypeOptions,
        ]);
    }
}
