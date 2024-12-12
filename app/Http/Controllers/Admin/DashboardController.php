<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Activity;
use Carbon\Carbon;
use App\Models\Machine;
use App\Models\MachineIssue;
use App\Models\ScoreCardDaily;
use App\Models\SRWO;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total users
        $totalUsers = User::count();
        
        // Hitung total SR dan WO yang closed
        $totalClosedSR = ServiceRequest::where('status', 'Closed')->count();
        $totalClosedWO = WorkOrder::where('status', 'Closed')->count();
        
        $totalClosedSRWO = $totalClosedSR + $totalClosedWO;
        
        // Hitung meetings hari ini
        $todayMeetings = Meeting::whereDate('created_at', Carbon::today())->count();
        
        // Ambil aktivitas terbaru
        $recentActivities = Activity::with('user')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalClosedSRWO',
            'totalUsers',
            'todayMeetings',
            'recentActivities'
        ));
    }

    public function refresh()
    {
        return response()->json([
            'stats' => [
                'totalUsers' => User::count(),
                'todayMeetings' => Meeting::whereDate('start_time', Carbon::today())->count(),
                'activeUsers' => User::count(),
            ],
            'activityChartData' => $this->getActivityChartData(),
            'meetingChartData' => $this->getMeetingChartData(),
        ]);
    }

    private function getActivityChartData()
    {
        $days = collect(range(6, 0))->map(function($day) {
            $date = Carbon::now()->subDays($day);
            return [
                'label' => $date->format('D'),
                'count' => Activity::whereDate('created_at', $date)->count()
            ];
        });

        return [
            'labels' => $days->pluck('label'),
            'data' => $days->pluck('count'),
        ];
    }

    private function getMeetingChartData()
    {
        $meetings = Meeting::selectRaw('DATE(start_time) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->limit(7)
            ->get();

        return [
            'labels' => $meetings->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('D, M d');
            }),
            'data' => $meetings->pluck('count'),
        ];
    }
} 