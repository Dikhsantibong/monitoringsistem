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
        // Ambil tanggal awal dan akhir bulan ini
        $currentMonth = now()->startOfMonth();
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        
        // Debug tanggal
        \Log::info('Date Range:', [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d')
        ]);
        
        // Buat array tanggal untuk satu bulan
        $dates = collect();
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        // Data ScoreCardDaily untuk ketepatan waktu
        $scoreCardData = ScoreCardDaily::whereBetween('tanggal', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            });

        // Data ScoreCardDaily untuk total score peserta dan ketentuan rapat
        $attendanceData = ScoreCardDaily::whereBetween('tanggal', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get()
            ->map(function($scoreCard) {
                try {
                    // Hitung total score peserta
                    $pesertaScore = 0;
                    if ($scoreCard->peserta) {
                        $peserta = json_decode($scoreCard->peserta, true) ?? [];
                        $pesertaScore = collect($peserta)->sum('skor');
                    }

                    // Hitung total score ketentuan rapat
                    $ketentuanScore = 
                        ($scoreCard->kesiapan_panitia ?? 100) +
                        ($scoreCard->kesiapan_bahan ?? 100) +
                        ($scoreCard->aktivitas_luar ?? 100) +
                        ($scoreCard->gangguan_diskusi ?? 100) +
                        ($scoreCard->gangguan_keluar_masuk ?? 100) +
                        ($scoreCard->gangguan_interupsi ?? 100) +
                        ($scoreCard->ketegasan_moderator ?? 100) +
                        ($scoreCard->skor_waktu_mulai ?? 100) +
                        ($scoreCard->skor_waktu_selesai ?? 100) +
                        ($scoreCard->kelengkapan_sr ?? 100);

                    // Total keseluruhan
                    $totalScore = $pesertaScore + $ketentuanScore;

                    return [
                        'date' => $scoreCard->tanggal->format('Y-m-d'),
                        'total_score' => $totalScore
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error calculating score: ' . $e->getMessage());
                    return [
                        'date' => $scoreCard->tanggal->format('Y-m-d'),
                        'total_score' => 0
                    ];
                }
            })
            ->groupBy('date')
            ->map(function($group) {
                return round($group->avg('total_score'), 2);
            });

        // Siapkan data untuk chart ketepatan waktu
        $formattedScoreCard = $dates->mapWithKeys(function($date) use ($scoreCardData) {
            return [
                $date => isset($scoreCardData[$date]) 
                    ? round($scoreCardData[$date]->avg('skor'), 2) 
                    : 0
            ];
        })->sortKeys();

        // Siapkan data untuk chart total score peserta
        $formattedAttendance = $dates->mapWithKeys(function($date) use ($attendanceData) {
            return [
                $date => $attendanceData[$date] ?? 0
            ];
        })->sortKeys();

        // Debug: Tampilkan data di log
        \Log::info('Dates:', ['dates' => $dates->toArray()]);
        \Log::info('ScoreCard Data:', ['data' => $scoreCardData->toArray()]);
        \Log::info('Attendance Data:', ['data' => $attendanceData->toArray()]);
        \Log::info('Formatted ScoreCard:', ['data' => $formattedScoreCard->toArray()]);
        \Log::info('Formatted Attendance:', ['data' => $formattedAttendance->toArray()]);

        // Debug: Tampilkan jumlah WO di log
        \Log::info('Work Order Counts:', [
            'open' => WorkOrder::where('status', 'open')->count(),
            'closed' => WorkOrder::where('status', 'closed')->count()
        ]);

        // Format data untuk charts
        $chartData = [
            'scoreCardData' => [
                'dates' => $formattedScoreCard->keys()->toArray(),
                'scores' => $formattedScoreCard->values()->toArray(),
            ],
            'attendanceData' => [
                'dates' => $formattedAttendance->keys()->toArray(),
                'scores' => $formattedAttendance->values()->toArray(),
            ],
            'srData' => [
                'counts' => [
                    ServiceRequest::where('status', 'Open')->count(),
                    ServiceRequest::where('status', 'Closed')->count(),
                ]
            ],
            'woData' => [
                'counts' => [
                    WorkOrder::where('status', 'Open')->count(),
                    WorkOrder::where('status', 'Closed')->count(),
                ]
            ]
        ];

        // Debug: Tampilkan seluruh data chart
        \Log::info('Chart Data:', $chartData);

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

    // Tambahkan method helper untuk menghitung score
    private function calculateTotalScore($scoreCard)
    {
        try {
            // Hitung score peserta
            $pesertaScore = 0;
            if ($scoreCard->peserta) {
                $peserta = json_decode($scoreCard->peserta, true) ?? [];
                $pesertaScore = collect($peserta)->sum('skor');
            }

            // Hitung score ketentuan rapat
            $ketentuanScore = 
                ($scoreCard->kesiapan_panitia ?? 100) +
                ($scoreCard->kesiapan_bahan ?? 100) +
                ($scoreCard->aktivitas_luar ?? 100) +
                ($scoreCard->gangguan_diskusi ?? 100) +
                ($scoreCard->gangguan_keluar_masuk ?? 100) +
                ($scoreCard->gangguan_interupsi ?? 100) +
                ($scoreCard->ketegasan_moderator ?? 100) +
                ($scoreCard->skor_waktu_mulai ?? 100) +
                ($scoreCard->skor_waktu_selesai ?? 100) +
                ($scoreCard->kelengkapan_sr ?? 100);

            return $pesertaScore + $ketentuanScore;
        } catch (\Exception $e) {
            \Log::error('Error in calculateTotalScore: ' . $e->getMessage());
            return 0;
        }
    }
} 