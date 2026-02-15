<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeeklyMeetingController extends Controller
{
    public function index(Request $request)
    {
        // View Mode
        $mode = $request->input('mode', 'list');
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $unitFilter = $request->input('unit');

        $unitMapping = [
            'KLKA' => 'PLTD KOLAKA',
            'LANI' => 'PLTD LANIPA NIPA',
            'SABI' => 'PLTM SABILAMBO',
            'MIKU' => 'PLTM MIKUASI',
            'BBAU' => 'PLTD BAU BAU',
            'WANG' => 'PLTD WANGI WANGI',
            'RAHA' => 'PLTD RAHA',
            'EREK' => 'PLTD EREKE',
            'RONG' => 'PLTM RONGI',
            'WINN' => 'PLTM WINNING',
            'POAS' => 'PLTD POASIA',
            'WUAW' => 'PLTD WUA WUA',
        ];

        // Prepare powerPlants for the filter dropdown
        $powerPlants = collect($unitMapping)->map(function($name, $prefix) {
            return (object)['id' => $prefix, 'name' => $name];
        });

        if ($mode === 'calendar') {
            $firstDay = Carbon::create($year, $month, 1);
            $lastDay = $firstDay->copy()->endOfMonth();

            // Fetch Work Orders for the month
            $workOrders = DB::connection('oracle')->table('WORKORDER')
                ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'SCHEDSTART', 'SCHEDFINISH', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM', 'LOCATION')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%')
                ->when($unitFilter, function($q) use ($unitFilter) {
                    return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
                })
                ->where(function($q) use ($firstDay, $lastDay) {
                    $q->whereBetween('REPORTDATE', [$firstDay, $lastDay])
                      ->orWhereBetween('SCHEDSTART', [$firstDay, $lastDay])
                      ->orWhereBetween('STATUSDATE', [$firstDay, $lastDay]);
                })
                ->get();

            // Fetch Service Requests for the month
            $serviceRequests = DB::connection('oracle')->table('SR')
                ->select('TICKETID', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'REPORTEDBY', 'LOCATION')
                ->where('SITEID', 'KD')
                ->when($unitFilter, function($q) use ($unitFilter) {
                    return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
                })
                ->whereBetween('REPORTDATE', [$firstDay, $lastDay])
                ->get();

            // Group data by date for the calendar
            $events = collect();
            
            foreach ($workOrders as $wo) {
                $date = Carbon::parse($wo->schedstart ?? $wo->reportdate)->format('Y-m-d');
                if (!$events->has($date)) $events->put($date, collect());
                $events->get($date)->push([
                    'id' => $wo->wonum,
                    'title' => $wo->wonum . ': ' . $wo->description,
                    'type' => 'WO',
                    'worktype' => $wo->worktype,
                    'status' => $wo->status,
                    'full_data' => $wo
                ]);
            }

            foreach ($serviceRequests as $sr) {
                $date = Carbon::parse($sr->reportdate)->format('Y-m-d');
                if (!$events->has($date)) $events->put($date, collect());
                $events->get($date)->push([
                    'id' => $sr->ticketid,
                    'title' => $sr->ticketid . ': ' . $sr->description,
                    'type' => 'SR',
                    'status' => $sr,
                    'full_data' => $sr
                ]);
            }

            return view('weekly-meeting.index', compact('mode', 'month', 'year', 'events', 'firstDay', 'lastDay', 'powerPlants', 'unitFilter'));
        }

        // --- EXISTING LIST LOGIC ---
        
        // Timing Logic        
        $now = Carbon::now();
        
        // Minggu Ini (Current Week) - Base reference
        $currentWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        $currentWeekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // Minggu Lalu (Last Week) - Untuk Review
        $lastWeekStart = $currentWeekStart->copy()->subWeek();
        $lastWeekEnd = $currentWeekStart->copy()->subDay(); // Sunday of last week

        // Minggu Depan (Next Week) - Untuk Planning
        $nextWeekStart = $currentWeekEnd->copy()->addDay(); // Monday of next week
        $nextWeekEnd = $nextWeekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // --- 1. REVIEW PHASE (MINGGU LALU) ---

        // A. Pekerjaan Completed (WO Closed/Comp) di Minggu Lalu
        $reviewCompletedWOs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'STATUSDATE', 'WORKTYPE', 'ASSETNUM', 'LOCATION', 'ACTFINISH')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->whereBetween('STATUSDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('STATUSDATE', 'desc')
            ->paginate(10, ['*'], 'review_completed_page');

        // B. WO Terbit (Created) di Minggu Lalu
        $reviewCreatedWOs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'LOCATION')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->whereBetween('REPORTDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('REPORTDATE', 'desc')
            ->paginate(10, ['*'], 'review_created_page');

        // B.2. SR Terbit (Created) di Minggu Lalu
        $reviewCreatedSRs = DB::connection('oracle')->table('SR')
            ->select('TICKETID', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'REPORTEDBY', 'LOCATION')
            ->where('SITEID', 'KD')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->whereBetween('REPORTDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('REPORTDATE', 'desc')
            ->paginate(10, ['*'], 'review_created_sr_page');

        // --- 2. PLANNING PHASE (MINGGU DEPAN) ---

        // A. Rencana Pekerjaan Rutin (PM)
        $planPMs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'SCHEDSTART', 'SCHEDFINISH', 'WORKTYPE', 'ASSETNUM', 'LOCATION')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->where('WORKTYPE', 'PM')
            ->whereNotIn('STATUS', ['COMP', 'CLOSE', 'CAN']) // Active PMs
            ->where(function($q) use ($nextWeekStart, $nextWeekEnd) {
                // Sched Start falls in next week
                $q->whereBetween('SCHEDSTART', [$nextWeekStart, $nextWeekEnd])
                  ->orWhereBetween('SCHEDFINISH', [$nextWeekStart, $nextWeekEnd]);
            })
            ->orderBy('SCHEDSTART', 'asc')
            ->paginate(10, ['*'], 'plan_pm_page');

        // B. Daftar WO Backlog
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
        
        $planBacklog = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM', 'LOCATION')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->where('WORKTYPE', '!=', 'PM') // Exclude routine PM
            ->whereIn('STATUS', $openStatuses)
            ->orderBy('WOPRIORITY', 'asc')
            ->orderBy('REPORTDATE', 'asc')
            ->paginate(10, ['*'], 'plan_backlog_page');

        // C. Urgent / Daily Focus
        $urgentWork = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM', 'LOCATION')
            ->where('SITEID', 'KD')
            ->where('WONUM', 'LIKE', 'WO%')
            ->when($unitFilter, function($q) use ($unitFilter) {
                return $q->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            })
            ->whereIn('STATUS', $openStatuses)
            ->where(function($q) {
                $q->where('WOPRIORITY', 1)
                  ->orWhere('WOPRIORITY', '1');
            })
            ->paginate(10, ['*'], 'plan_urgent_page');

        return view('weekly-meeting.index', compact(
            'mode', 'lastWeekStart', 'lastWeekEnd', 'nextWeekStart', 'nextWeekEnd',
            'reviewCompletedWOs', 'reviewCreatedWOs', 'reviewCreatedSRs',
            'planPMs', 'planBacklog', 'urgentWork', 'powerPlants', 'unitFilter'
        ));
    }
}
