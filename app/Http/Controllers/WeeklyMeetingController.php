<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\WoBacklog;
use App\Models\WorkOrder;
use App\Models\ServiceRequest;
use App\Models\MachineStatusLog;

class WeeklyMeetingController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $startOfNextWeek = $endOfWeek->copy()->addDay();
        $endOfNextWeek = $startOfNextWeek->copy()->endOfWeek(Carbon::SUNDAY);

        // Minggu ini
        $woBacklogsThisWeek = WoBacklog::whereBetween('schedule_start', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('schedule_finish', [$startOfWeek, $endOfWeek])->get();
        $workOrdersThisWeek = WorkOrder::whereBetween('schedule_start', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('schedule_finish', [$startOfWeek, $endOfWeek])->get();
        $serviceRequestsThisWeek = ServiceRequest::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('updated_at', [$startOfWeek, $endOfWeek])->get();
        $machineIssuesThisWeek = MachineStatusLog::whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('target_selesai', [$startOfWeek, $endOfWeek])->get();

        // Minggu depan
        $woBacklogsNextWeek = WoBacklog::whereBetween('schedule_start', [$startOfNextWeek, $endOfNextWeek])
            ->orWhereBetween('schedule_finish', [$startOfNextWeek, $endOfNextWeek])->get();
        $workOrdersNextWeek = WorkOrder::whereBetween('schedule_start', [$startOfNextWeek, $endOfNextWeek])
            ->orWhereBetween('schedule_finish', [$startOfNextWeek, $endOfNextWeek])->get();
        $serviceRequestsNextWeek = ServiceRequest::whereBetween('created_at', [$startOfNextWeek, $endOfNextWeek])
            ->orWhereBetween('updated_at', [$startOfNextWeek, $endOfNextWeek])->get();
        $machineIssuesNextWeek = MachineStatusLog::whereBetween('tanggal_mulai', [$startOfNextWeek, $endOfNextWeek])
            ->orWhereBetween('target_selesai', [$startOfNextWeek, $endOfNextWeek])->get();

        // Mapping koneksi database ke nama unit friendly
        $unitMap = [
            'mysql' => 'UP KENDARI',
            'mysql_bau_bau' => 'ULPLTD BAU BAU',
            'mysql_kolaka' => 'ULPLTD KOLAKA',
            'mysql_poasia' => 'ULPLTD POASIA',
            'mysql_wua_wua' => 'ULPLTD WUA WUA',
        ];

        return view('weekly-meeting.index', [
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'startOfNextWeek' => $startOfNextWeek,
            'endOfNextWeek' => $endOfNextWeek,
            'woBacklogsThisWeek' => $woBacklogsThisWeek,
            'workOrdersThisWeek' => $workOrdersThisWeek,
            'serviceRequestsThisWeek' => $serviceRequestsThisWeek,
            'machineIssuesThisWeek' => $machineIssuesThisWeek,
            'woBacklogsNextWeek' => $woBacklogsNextWeek,
            'workOrdersNextWeek' => $workOrdersNextWeek,
            'serviceRequestsNextWeek' => $serviceRequestsNextWeek,
            'machineIssuesNextWeek' => $machineIssuesNextWeek,
            'unitMap' => $unitMap,
        ]);
    }
}
