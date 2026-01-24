<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WeeklyMeetingController extends Controller
{
    public function index(Request $request)
    {
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
            ->whereIn('STATUS', ['COMP', 'CLOSE'])
            ->whereBetween('STATUSDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('STATUSDATE', 'desc')
            ->paginate(10, ['*'], 'review_completed_page');

        // B. WO Terbit (Created) di Minggu Lalu
        $reviewCreatedWOs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY')
            ->where('SITEID', 'KD')
            ->whereBetween('REPORTDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('REPORTDATE', 'desc')
            ->paginate(10, ['*'], 'review_created_page');

        // B.2. SR Terbit (Created) di Minggu Lalu
        $reviewCreatedSRs = DB::connection('oracle')->table('SR')
            ->select('TICKETID', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'REPORTEDBY')
            ->where('SITEID', 'KD')
            ->whereBetween('REPORTDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('REPORTDATE', 'desc')
            ->paginate(10, ['*'], 'review_created_sr_page');

        // --- 2. PLANNING PHASE (MINGGU DEPAN) ---

        // A. Rencana Pekerjaan Rutin (PM)
        $planPMs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'SCHEDSTART', 'SCHEDFINISH', 'WORKTYPE', 'ASSETNUM', 'LOCATION')
            ->where('SITEID', 'KD')
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
            ->where('WORKTYPE', '!=', 'PM') // Exclude routine PM
            ->whereIn('STATUS', $openStatuses)
            ->orderBy('WOPRIORITY', 'asc')
            ->orderBy('REPORTDATE', 'asc')
            ->paginate(10, ['*'], 'plan_backlog_page');

        // C. Urgent / Daily Focus
        $urgentWork = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM')
            ->where('SITEID', 'KD')
            ->whereIn('STATUS', $openStatuses)
            ->where(function($q) {
                $q->where('WOPRIORITY', 1)
                  ->orWhere('WOPRIORITY', '1');
            })
            ->paginate(10, ['*'], 'plan_urgent_page');

        return view('weekly-meeting.index', compact(
            'lastWeekStart', 'lastWeekEnd', 'nextWeekStart', 'nextWeekEnd',
            'reviewCompletedWOs', 'reviewCreatedWOs', 'reviewCreatedSRs',
            'planPMs', 'planBacklog', 'urgentWork'
        ));
    }
}
