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

class DashboardController extends Controller
{
    public function index()
    {
        // Data sementara untuk 7 hari terakhir
        $sampleData = collect([
            [
                'tanggal' => Carbon::now()->subDays(6)->format('Y-m-d'),
                'skor' => 85,
                'peserta' => 12
            ],
            [
                'tanggal' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'skor' => 90,
                'peserta' => 15
            ],
            [
                'tanggal' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'skor' => 88,
                'peserta' => 13
            ],
            [
                'tanggal' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'skor' => 92,
                'peserta' => 14
            ],
            [
                'tanggal' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'skor' => 87,
                'peserta' => 16
            ],
            [
                'tanggal' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'skor' => 91,
                'peserta' => 15
            ],
            [
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'skor' => 89,
                'peserta' => 14
            ]
        ]);

        $data = [
            'totalUsers' => User::count(),
            'todayMeetings' => Meeting::whereDate('start_time', Carbon::today())->count(),
            'activeUsers' => User::count(),
            'recentActivities' => Activity::with('user')->latest()->take(10)->get(),
            'scoreCards' => $sampleData, // Menggunakan data sementara
        ];

        return view('admin.dashboard', $data);
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