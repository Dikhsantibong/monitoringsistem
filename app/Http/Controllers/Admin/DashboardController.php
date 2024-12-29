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
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data 7 hari terakhir
        $endDate = now();
        $startDate = now()->subDays(6);
        
        // Buat array tanggal untuk 7 hari terakhir
        $dates = collect();
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        // Data ScoreCardDaily
        $scoreCardData = ScoreCardDaily::whereBetween('tanggal', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // Data Attendance
        $attendanceData = Attendance::whereBetween('time', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('time')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->time)->format('Y-m-d');
            });

        // Siapkan data untuk chart dengan nilai default 0 untuk tanggal yang tidak memiliki data
        $formattedScoreCard = $dates->mapWithKeys(function($date) use ($scoreCardData) {
            return [
                $date => isset($scoreCardData[$date]) 
                    ? round($scoreCardData[$date]->avg('skor'), 2) 
                    : 0
            ];
        })->sortKeys();

        $formattedAttendance = $dates->mapWithKeys(function($date) use ($attendanceData) {
            return [
                $date => isset($attendanceData[$date]) 
                    ? $attendanceData[$date]->count() 
                    : 0
            ];
        })->sortKeys();

        // Debug: Tampilkan data di log
        \Log::info('Dates:', ['dates' => $dates->toArray()]);
        \Log::info('ScoreCard Data:', ['data' => $scoreCardData->toArray()]);
        \Log::info('Attendance Data:', ['data' => $attendanceData->toArray()]);
        \Log::info('Formatted ScoreCard:', ['data' => $formattedScoreCard->toArray()]);
        \Log::info('Formatted Attendance:', ['data' => $formattedAttendance->toArray()]);

        // Format data untuk charts
        $chartData = [
            'scoreCardData' => [
                'dates' => $formattedScoreCard->keys()->toArray(),
                'scores' => $formattedScoreCard->values()->toArray(),
            ],
            'attendanceData' => [
                'dates' => $formattedAttendance->keys()->toArray(),
                'scores' => $formattedAttendance->values()->toArray(),
            ]
        ];

        // Tambahkan data untuk statistik
        $totalUsers = User::count();
        
        // Menggabungkan total SR dan WO yang closed
        $totalClosedSRWO = ServiceRequest::where('status', 'Closed')->count() +
                           WorkOrder::where('status', 'Closed')->count();
                           
        $recentActivities = Activity::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'chartData',
            'totalUsers',
            'totalClosedSRWO',
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