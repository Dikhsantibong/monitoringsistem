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
            ->get();

        // B. SR/WO Terbit (Created) di Minggu Lalu
        $reviewCreatedWOs = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY')
            ->where('SITEID', 'KD')
            ->whereBetween('REPORTDATE', [$lastWeekStart, $lastWeekEnd])
            ->orderBy('REPORTDATE', 'desc')
            ->get();

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
            ->get();

        // B. Daftar WO Backlog
        // Definisi: WO Open (WAPPR, WSCH, WMATL, INPRG, APPR) yang Created Date-nya < Next Week Start?
        // User: "daftar WO backlog... dan WO/SR yang terbit pada minggu sebelumnya dengan prioritas normal"
        // Kita ambil semua Open WO Non-PM (Non-Routine) sebagai pool backlog.
        // Limit to reasonable number or filter slightly? Let's take Open Statuses.
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
        
        $planBacklog = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM', 'LOCATION')
            ->where('SITEID', 'KD')
            ->where('WORKTYPE', '!=', 'PM') // Exclude routine PM
            ->whereIn('STATUS', $openStatuses)
            // Filter backlog: Created before "Next Week" (which is true for any existing record now)
            // Maybe filter "Created at least 1 week ago" for true backlog? 
            // User says "WO/SR terbit minggu sebelumnya". Let's just Include ALL Open Non-PM.
            ->orderBy('WOPRIORITY', 'asc') // High priority first? Or Oldest first?
            ->orderBy('REPORTDATE', 'asc')
            ->limit(200) // Safety limit
            ->get();

        // C. Urgent / Daily Focus
        // WR/SR Priority Urgent (1) yang masih Open
        // Atau WO dengan Priority 1
        $urgentWork = DB::connection('oracle')->table('WORKORDER')
            ->select('WONUM', 'DESCRIPTION', 'STATUS', 'REPORTDATE', 'WORKTYPE', 'WOPRIORITY', 'ASSETNUM')
            ->where('SITEID', 'KD')
            ->whereIn('STATUS', $openStatuses)
            ->where(function($q) {
                $q->where('WOPRIORITY', 1)
                  ->orWhere('WOPRIORITY', '1'); // Just in case string
            })
            ->get();

        return view('weekly-meeting.index', compact(
            'lastWeekStart', 'lastWeekEnd', 'nextWeekStart', 'nextWeekEnd',
            'reviewCompletedWOs', 'reviewCreatedWOs',
            'planPMs', 'planBacklog', 'urgentWork'
        ));
    }
}
