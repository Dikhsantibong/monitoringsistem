<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Activity;
use Carbon\Carbon;
use App\Models\Machine;
use App\Models\ScoreCardDaily;
use App\Models\ServiceRequest;
use App\Models\WorkOrder;
use App\Models\WoBacklog;
use App\Models\OtherDiscussion;
use App\Models\Commitment;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;


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
                    // Hitung total score menggunakan helper method
                    $totalScore = $this->calculateTotalScore($scoreCard);
                    
                    // Konversi ke persentase (maksimum score 2000 = 100%)
                    $percentageScore = min(($totalScore / 2000) * 100, 100);

                    return [
                        'date' => $scoreCard->tanggal->format('Y-m-d'),
                        'total_score' => round($percentageScore, 2)
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

        // Debug log untuk memeriksa data komitmen
        $openCommitments = Commitment::where('status', 'Open')->count();
        $closedCommitments = Commitment::where('status', 'Closed')->count();
        
        \Log::info('Commitment Data:', [
            'open' => $openCommitments,
            'closed' => $closedCommitments
        ]);

        // Ambil data kehadiran untuk satu bulan (jumlah peserta hadir per hari) dari seluruh unit
        $unitConnections = [
            'mysql',
            'mysql_bau_bau',
            'mysql_kolaka',
            'mysql_poasia',
            'mysql_wua_wua',
            // tambahkan koneksi lain jika ada
        ];
        $attendanceCounts = collect();
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $totalCount = 0;
            foreach ($unitConnections as $conn) {
                try {
                    $totalCount += \App\Models\Attendance::on($conn)
                        ->whereDate('time', $dateStr)
                        ->count();
                } catch (\Exception $e) {
                    \Log::warning("Gagal mengambil data attendance dari $conn: " . $e->getMessage());
                }
            }
            $attendanceCounts->push([
                'date' => $dateStr,
                'count' => $totalCount
            ]);
        }

        // Format data untuk chart
        $chartData = [
            'scoreCardData' => [
                'dates' => $attendanceCounts->pluck('date')->toArray(),
                'counts' => $attendanceCounts->pluck('count')->toArray(),
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
            ],
            'woBacklogData' => [
                'counts' => [
                    WoBacklog::where('status', 'Open')->count()
                ]
            ],
            'otherDiscussionData' => [
                'counts' => [
                    OtherDiscussion::where('status', 'Open')->count(),
                    OtherDiscussion::where('status', 'Closed')->count()
                ]
            ],
            'commitmentData' => [
                'counts' => [
                    Commitment::where('status', 'Open')->count(),
                    Commitment::where('status', 'Closed')->count()
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

        // Debug: Log data kehadiran
        \Log::info('Attendance Data:', [
            'data' => $attendanceData->toArray()
        ]);

        // Summary info untuk grafik
        $activityTotal = $attendanceCounts->sum('count');
        $activityAvg = $attendanceCounts->count() > 0 ? round($attendanceCounts->avg('count'), 2) : 0;
        $meetingAvg = $formattedAttendance->count() > 0 ? round($formattedAttendance->avg(), 2) : 0;
        $srOpen = $chartData['srData']['counts'][0] ?? 0;
        $srClosed = $chartData['srData']['counts'][1] ?? 0;
        $srTotal = $srOpen + $srClosed;
        $srClosedPct = $srTotal > 0 ? round($srClosed / $srTotal * 100, 1) : 0;
        $woOpen = $chartData['woData']['counts'][0] ?? 0;
        $woClosed = $chartData['woData']['counts'][1] ?? 0;
        $woTotal = $woOpen + $woClosed;
        $woClosedPct = $woTotal > 0 ? round($woClosed / $woTotal * 100, 1) : 0;
        $backlogOpen = $chartData['woBacklogData']['counts'][0] ?? 0;
        $commitOpen = $chartData['commitmentData']['counts'][0] ?? 0;
        $commitClosed = $chartData['commitmentData']['counts'][1] ?? 0;
        $commitTotal = $commitOpen + $commitClosed;
        $commitClosedPct = $commitTotal > 0 ? round($commitClosed / $commitTotal * 100, 1) : 0;
        $otherOpen = $chartData['otherDiscussionData']['counts'][0] ?? 0;
        $otherClosed = $chartData['otherDiscussionData']['counts'][1] ?? 0;
        $otherTotal = $otherOpen + $otherClosed;
        $otherClosedPct = $otherTotal > 0 ? round($otherClosed / $otherTotal * 100, 1) : 0;
        $chartSummary = [
            'activity' => [
                'total' => $activityTotal,
                'avg' => $activityAvg,
            ],
            'meeting' => [
                'avg' => $meetingAvg,
            ],
            'sr' => [
                'open' => $srOpen,
                'closed' => $srClosed,
                'total' => $srTotal,
                'closed_pct' => $srClosedPct,
            ],
            'wo' => [
                'open' => $woOpen,
                'closed' => $woClosed,
                'total' => $woTotal,
                'closed_pct' => $woClosedPct,
            ],
            'backlog' => [
                'open' => $backlogOpen,
            ],
            'commitment' => [
                'open' => $commitOpen,
                'closed' => $commitClosed,
                'total' => $commitTotal,
                'closed_pct' => $commitClosedPct,
            ],
            'other_discussion' => [
                'open' => $otherOpen,
                'closed' => $otherClosed,
                'total' => $otherTotal,
                'closed_pct' => $otherClosedPct,
            ],
        ];

        // Tambahan: List koneksi unit dan label
        $unitConnections = [
            'mysql' => 'UP KENDARI',
            'mysql_bau_bau' => 'ULPLTD BAU-BAU',
            'mysql_kolaka' => 'ULPLTD KOLAKA',
            'mysql_poasia' => 'ULPLTD POASIA',
            'mysql_wua_wua' => 'ULPLTD WUA-WUA',
        ];
        // 1. Tren Kehadiran per Unit (multi-line)
        $unitAttendanceTrends = [];
        foreach ($unitConnections as $conn => $unitLabel) {
            $counts = [];
            for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                try {
                    $counts[$dateStr] = \App\Models\Attendance::on($conn)
                        ->where('unit_source', $conn)
                        ->whereDate('time', $dateStr)
                        ->count();
                } catch (\Exception $e) {
                    $counts[$dateStr] = 0;
                }
            }
            $unitAttendanceTrends[$unitLabel] = $counts;
        }
        // 2. Distribusi Status Mesin (pie/bar)
        $machineStatusDist = [];
        $statusLabels = [];
        foreach ($unitConnections as $conn => $unitLabel) {
            try {
                $statusCounts = \App\Models\MachineStatusLog::on($conn)
                    ->select('status', \DB::raw('COUNT(*) as total'))
                    ->groupBy('status')->pluck('total','status')->toArray();
                foreach ($statusCounts as $status => $total) {
                    $machineStatusDist[$status] = ($machineStatusDist[$status] ?? 0) + $total;
                    if (!in_array($status, $statusLabels)) $statusLabels[] = $status;
                }
            } catch (\Exception $e) {}
        }
        // 3. Jumlah WO/SR/Pengajuan Material per Bulan (6 bulan terakhir)
        $monthlyCounts = [];
        $months = collect(range(5,0))->map(function($i) { return now()->subMonths($i)->format('Y-m'); });
        foreach ($months as $month) {
            $wo = $sr = $mat = 0;
            foreach ($unitConnections as $conn => $unitLabel) {
                try {
                    $wo += \App\Models\WorkOrder::on($conn)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->count();
                } catch (\Exception $e) {}
                try {
                    $sr += \App\Models\ServiceRequest::on($conn)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->count();
                } catch (\Exception $e) {}
                try {
                    $mat += \App\Models\PengajuanMaterialFile::on($conn)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$month])->count();
                } catch (\Exception $e) {}
            }
            $monthlyCounts[] = [ 'month' => $month, 'wo' => $wo, 'sr' => $sr, 'material' => $mat ];
        }
        // 4. Penyelesaian WO/SR per Unit (stacked bar)
        $woSrCompletion = [];
        foreach ($unitConnections as $conn => $unitLabel) {
            if ($conn === 'mysql') continue; // HILANGKAN UP KENDARI dari grafik per unit
            $woOpen = $woClosed = $srOpen = $srClosed = 0;
            try {
                $woOpen = \App\Models\WorkOrder::on($conn)->where('unit_source', $conn)->where('status','Open')->count();
                $woClosed = \App\Models\WorkOrder::on($conn)->where('unit_source', $conn)->where('status','Closed')->count();
            } catch (\Exception $e) {}
            try {
                $srOpen = \App\Models\ServiceRequest::on($conn)->where('unit_source', $conn)->where('status','Open')->count();
                $srClosed = \App\Models\ServiceRequest::on($conn)->where('unit_source', $conn)->where('status','Closed')->count();
            } catch (\Exception $e) {}
            $woSrCompletion[$unitLabel] = [ 'wo_open' => $woOpen, 'wo_closed' => $woClosed, 'sr_open' => $srOpen, 'sr_closed' => $srClosed ];
        }
        // 5. Top 5 Material Paling Sering Diajukan
        $materialCounts = [];
        foreach ($unitConnections as $conn => $unitLabel) {
            try {
                $rows = \App\Models\PengajuanMaterialFile::on($conn)
                    ->select('nama_material', \DB::raw('COUNT(*) as total'))
                    ->groupBy('nama_material')->pluck('total','nama_material')->toArray();
                foreach ($rows as $mat => $total) {
                    if (!$mat) continue;
                    $materialCounts[$mat] = ($materialCounts[$mat] ?? 0) + $total;
                }
            } catch (\Exception $e) {}
        }
        arsort($materialCounts);
        $topMaterials = array_slice($materialCounts, 0, 5, true);
        // 6. Komitmen & Pembahasan lain-lain per status
        $commitmentStatus = ['Open' => 0, 'Closed' => 0];
        $discussionStatus = ['Open' => 0, 'Closed' => 0];
        foreach ($unitConnections as $conn => $unitLabel) {
            try {
                $commitmentStatus['Open'] += \App\Models\Commitment::on($conn)->where('status','Open')->count();
                $commitmentStatus['Closed'] += \App\Models\Commitment::on($conn)->where('status','Closed')->count();
            } catch (\Exception $e) {}
            try {
                $discussionStatus['Open'] += \App\Models\OtherDiscussion::on($conn)->where('status','Open')->count();
                $discussionStatus['Closed'] += \App\Models\OtherDiscussion::on($conn)->where('status','Closed')->count();
            } catch (\Exception $e) {}
        }
        $commitmentDiscussionStatus = [
            'commitment' => $commitmentStatus,
            'discussion' => $discussionStatus
        ];

        // Mapping unit_source ke nama unit
        $unitSourceMap = [
            'mysql' => 'UP KENDARI',
            'mysql_bau_bau' => 'ULPLTD BAU-BAU',
            'mysql_kolaka' => 'ULPLTD KOLAKA',
            'mysql_poasia' => 'ULPLTD POASIA',
            'mysql_wua_wua' => 'ULPLTD WUA-WUA',
        ];
        // Data kehadiran harian per unit_source
        $attendancePerUnit = [];
        $attendanceUnitLabels = [];
        foreach ($unitSourceMap as $conn => $unitLabel) {
            $counts = [];
            for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                try {
                    $counts[$dateStr] = \App\Models\Attendance::on($conn)
                        ->where('unit_source', $conn)
                        ->whereDate('time', $dateStr)
                        ->count();
                } catch (\Exception $e) {
                    $counts[$dateStr] = 0;
                }
            }
            $attendancePerUnit[$conn] = $counts;
            $attendanceUnitLabels[$conn] = $unitLabel;
        }

        return view('admin.dashboard', compact(
            'chartData',
            'chartSummary',
            'totalUsers',
            'totalClosedSRWO',
            'recentActivities',
            'unitAttendanceTrends',
            'machineStatusDist',
            'monthlyCounts',
            'woSrCompletion',
            'topMaterials',
            'commitmentDiscussionStatus',
            'attendancePerUnit',
            'attendanceUnitLabels',
            'unitSourceMap'
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