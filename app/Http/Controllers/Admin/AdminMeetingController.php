<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Department; // Pastikan model Department di-import
use App\Models\ScoreCardDaily; // Ambil model ScoreCardDaily
use App\Models\MachineStatusLog; // Ambil model MachineStatusLog
use App\Models\Attendance; // Ambil model Attendance
use App\Models\ServiceRequest; // Ambil model ServiceRequest
use App\Models\WoBacklog; // Ambil model WoBacklog
use App\Models\OtherDiscussion;
use App\Models\ClosedDiscussion;
use App\Models\OverdueDiscussion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\WorkOrder;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\DB;


class AdminMeetingController extends Controller
{
    public function index()
    {
        try {
            $selectedDate = request('tanggal', now()->format('Y-m-d'));
            $unitSource = request('unit_source');
            
            \Log::info('Request parameters:', [
                'date' => $selectedDate,
                'unit' => $unitSource
            ]);
            
            $query = ScoreCardDaily::orderBy('tanggal', 'desc');
            
            // Filter berdasarkan unit jika ada
            if ($unitSource && session('unit') === 'mysql') {
                $query->where('unit_source', $unitSource);
                \Log::info('Applying unit filter:', ['unit' => $unitSource]);
            }
            
            // Query untuk data score card dengan tanggal yang dipilih
            $scoreCards = $query->whereDate('tanggal', $selectedDate)
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info('Query results:', ['count' => $scoreCards->count()]);
            
            $scoreCards = $scoreCards->map(function ($scoreCard) {
                $peserta = json_decode($scoreCard->peserta, true) ?? [];
                $formattedPeserta = [];
                
                foreach ($peserta as $jabatan => $data) {
                    $formattedPeserta[] = [
                        
                        'jabatan' => ucwords(str_replace('_', ' ', $jabatan)),
                        'awal' => $data['awal'] ?? '0',
                        'akhir' => $data['akhir'] ?? '0',
                        'skor' => $data['skor'] ?? '0',
                        'keterangan' => $data['keterangan'] ?? null
                    ];
                }

                return [
                    'id' => $scoreCard->id,
                    'tanggal' => $scoreCard->tanggal,
                    'lokasi' => $scoreCard->lokasi,
                    'peserta' => $formattedPeserta,
                    'waktu_mulai' => $scoreCard->waktu_mulai,
                    'waktu_selesai' => $scoreCard->waktu_selesai,
                    'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
                    'kesiapan_bahan' => $scoreCard->kesiapan_bahan,
                    'aktivitas_luar' => $scoreCard->aktivitas_luar,
                    'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
                    'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
                    'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
                    'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
                    'kelengkapan_sr' => $scoreCard->kelengkapan_sr,
                    'keterangan' => $scoreCard->keterangan
                ];
            });

            if (request()->ajax()) {
                return view('admin.meetings._table', [
                    'scoreCards' => $scoreCards
                ])->render();
            }

            // Ambil tanggal yang tersedia setelah filter unit
            $availableDates = $query->pluck('tanggal')
                ->unique()
                ->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->values()
                ->toArray();

            return view('admin.meetings.index', [
                'scoreCards' => $scoreCards,
                'selectedDate' => $selectedDate,
                'availableDates' => $availableDates
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in AdminMeetingController@index: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['error' => 'Terjadi kesalahan saat memuat data.'], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function create()
    {
        
        return view('admin.meetings.create');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['department', 'participants']);
        return response()->json($meeting);
    }

    public function export()
    {
        // Implementasi export meetings
        return response()->download('path/to/exported/file.xlsx');
    }

    public function dailyMeeting()
    {
        // Ambil semua pertemuan yang dijadwalkan untuk hari ini
        $meetings = Meeting::whereDate('scheduled_at', today())
            ->with(['department', 'participants'])
            ->get();

        return view('user.daily-meeting', compact('meetings'));
    }

    public function getScoreCardData(Request $request)
    {
        try {
            $tanggal = $request->tanggal ?? now()->format('Y-m-d');
            
            \Log::info('Mencari data untuk tanggal: ' . $tanggal);
            
            $scoreCard = ScoreCardDaily::whereDate('tanggal', $tanggal)
                ->latest()
                ->first();

            \Log::info('Data ScoreCard:', ['data' => $scoreCard]);

            if (!$scoreCard) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data untuk tanggal yang dipilih'
                ]);
            }

            // Format data sesuai dengan struktur tabel
            $data = [
                'success' => true,
                'data' => [
                    'tanggal' => $scoreCard->tanggal,
                    'lokasi' => $scoreCard->lokasi,
                    'awal' => $scoreCard->awal,
                    'akhir' => $scoreCard->akhir,
                    'skor' => $scoreCard->skor,
                    'waktu_mulai' => $scoreCard->waktu_mulai,
                    'waktu_selesai' => $scoreCard->waktu_selesai,
                    'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
                    'kesiapan_bahan' => $scoreCard->kesiapan_bahan,
                    'aktivitas_luar' => $scoreCard->aktivitas_luar,
                    'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
                    'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
                    'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
                    'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
                    'kelengkapan_sr' => $scoreCard->kelengkapan_sr
                    
                ]
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error dalam getScoreCardData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data'
            ], 500);
        }
    }

    public function downloadScoreCard(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
        
        $scoreCard = ScoreCardDaily::whereDate('tanggal', $tanggal)
            ->latest()
            ->first();

        // Buat logic untuk generate PDF atau Excel disini
        // Contoh sederhana menggunakan CSV
        $filename = "scorecard_" . $tanggal . ".csv";
        $handle = fopen('php://temp', 'w+');
        
        // Header
        fputcsv($handle, ['No', 'Peserta', 'Awal', 'Akhir', 'Skor', 'Keterangan']);
        
        // Data peserta
        if ($scoreCard) {
            $peserta = json_decode($scoreCard->peserta, true);
            $no = 1;
            foreach ($peserta as $jabatan => $data) {
                fputcsv($handle, [
                    $no++,
                    $jabatan,
                    $data['skor'] == 50 ? '0' : '',
                    $data['skor'] == 100 ? '1' : '',
                    $data['skor'],
                    ''
                ]);
            }
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    public function printView(Request $request)
    {
        try {
            $date = $request->date ?? now()->format('Y-m-d');
            $allScoreCards = [];
            
            // Daftar koneksi database yang akan diambil datanya
            $connections = [
                'mysql_bau_bau' => 'Bau-Bau',
                'mysql_kolaka' => 'Kolaka',
                'mysql_poasia' => 'Poasia',
                'mysql_wua_wua' => 'Wua-Wua',

                
            ];
            
            foreach ($connections as $connection => $unitName) {
                try {
                    // Ganti koneksi database
                    $scoreCards = DB::connection($connection)
                        ->table('score_card_dailies')
                        ->whereDate('tanggal', $date)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    if ($scoreCards->isNotEmpty()) {
                        $allScoreCards[$unitName] = $this->formatScoreCardData($scoreCards->first(), $unitName);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error accessing {$connection}: " . $e->getMessage());
                    continue;
                }
            }

            return view('admin.meetings.print', [
                'allScoreCards' => $allScoreCards,
                'date' => $date
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data print.');
        }
    }

    private function formatScoreCardData($scoreCard, $unitName)
    {
        return [
            'lokasi' => $unitName,
            'waktu_mulai' => $scoreCard->waktu_mulai,
            'waktu_selesai' => $scoreCard->waktu_selesai,
            'peserta' => json_decode($scoreCard->peserta, true),
            'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
            'kesiapan_bahan' => $scoreCard->kesiapan_bahan, 
            'aktivitas_luar' => $scoreCard->aktivitas_luar,
            'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
            'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
            'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
            'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
            'kelengkapan_sr' => $scoreCard->kelengkapan_sr,
            'skor_waktu_mulai' => $scoreCard->skor_waktu_mulai
        ];
    }

    public function print(Request $request)
    {
        try {
            $date = $request->get('date');
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
            $allScoreCards = [];
            $currentSession = session('unit');

            // Mapping koneksi database dengan nama unit yang sesuai
            $connections = [
                'mysql' => [
                    'u478221055_up_kendari' => 'UP Kendari',
                    'mysql_bau_bau' => 'Bau-Bau',
                    'mysql_kolaka' => 'Kolaka', 
                    'mysql_poasia' => 'Poasia',
                    'mysql_wua_wua' => 'Wua-Wua',
                ],
                'mysql_bau_bau' => ['mysql_bau_bau' => 'Bau-Bau'],
                'mysql_kolaka' => ['mysql_kolaka' => 'Kolaka'],
                'mysql_poasia' => ['mysql_poasia' => 'Poasia'],
                'mysql_wua_wua' => ['mysql_wua_wua' => 'Wua-Wua']
            ];

            // Pilih koneksi berdasarkan session
            $activeConnections = $connections[$currentSession] ?? [];
            
            \Log::info('Current session and connections:', [
                'session' => $currentSession,
                'activeConnections' => $activeConnections
            ]);

            // Data untuk semua unit
            $attendances = Attendance::whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            $powerPlants = PowerPlant::with(['machines' => function($query) use ($date) {
                $query->whereHas('statusLogs', function($q) use ($date) {
                    $q->whereDate('created_at', $date);
                });
            }])->get();

            $logs = MachineStatusLog::whereDate('created_at', $date)
                ->with('machine.powerPlant')
                ->get();

            // Modifikasi query untuk data bulanan
            $serviceRequests = ServiceRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('priority', 'desc')
                ->get();

            $workOrders = WorkOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at')
                ->get();

            $woBacklogs = WoBacklog::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at')
                ->get();

            // Modifikasi query untuk Other Discussions - hanya ambil yang masih open
            $otherDiscussions = OtherDiscussion::where('status', 'open')
                ->where(function($query) use ($date) {
                    $query->whereDate('created_at', '<=', $date)
                          ->whereNull('closed_at');
                })
                ->with(['commitments' => function($query) {
                    $query->where('status', 'open');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Tambahkan logging untuk memantau data
            \Log::info('Open Other Discussions data for print:', [
                'count' => $otherDiscussions->count(),
                'date' => $date,
                'discussions' => $otherDiscussions->map(function($discussion) {
                    return [
                        'id' => $discussion->id,
                        'topic' => $discussion->topic,
                        'created_at' => $discussion->created_at,
                        'open_commitments_count' => $discussion->commitments->count()
                    ];
                })
            ]);
            
            foreach ($activeConnections as $connection => $unitName) {
                try {
                    \Log::info("Trying to fetch data for connection: {$connection}");
                    
                    // Score Card Data - Menggunakan nama tabel yang benar
                    $scoreCard = DB::connection($connection)
                        ->table('score_card_daily') // Menggunakan nama tabel yang benar
                        ->whereDate('tanggal', $date)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($scoreCard) {
                        // Hitung skor waktu mulai
                        $waktuMulaiTarget = "07:30:00";
                        $waktuMulaiActual = $scoreCard->waktu_mulai;
                        $selisihMenit = round((strtotime($waktuMulaiActual) - strtotime($waktuMulaiTarget)) / 60);
                        $skorWaktuMulai = max(0, 100 - (floor($selisihMenit / 3) * 10));

                        $allScoreCards[$unitName] = [
                            'lokasi' => $scoreCard->lokasi,
                            'waktu_mulai' => $scoreCard->waktu_mulai,
                            'waktu_selesai' => $scoreCard->waktu_selesai,
                            'peserta' => json_decode($scoreCard->peserta, true),
                            'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
                            'kesiapan_bahan' => $scoreCard->kesiapan_bahan,
                            'aktivitas_luar' => $scoreCard->aktivitas_luar,
                            'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
                            'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
                            'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
                            'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
                            'kelengkapan_sr' => $scoreCard->kelengkapan_sr,
                            'skor_waktu_mulai' => $skorWaktuMulai
                        ];

                        \Log::info("Successfully fetched score card for {$unitName}", [
                            'data' => $allScoreCards[$unitName]
                        ]);
                    } else {
                        \Log::info("No score card found for {$unitName} on date {$date}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error accessing {$connection}: " . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            \Log::info('Final score cards data:', [
                'count' => count($allScoreCards),
                'units' => array_keys($allScoreCards)
            ]);

            return view('admin.meetings.print', [
                'allScoreCards' => $allScoreCards,
                'date' => $date,
                'attendances' => $attendances,
                'powerPlants' => $powerPlants,
                'logs' => $logs,
                'serviceRequests' => $serviceRequests,
                'workOrders' => $workOrders,
                'woBacklogs' => $woBacklogs,
                'otherDiscussions' => $otherDiscussions
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data print.');
        }
    }

    private function hitungSkorWaktuMulai($waktuMulai)
    {
        $waktuTarget = "07:30:00";
        $selisihMenit = round((strtotime($waktuMulai) - strtotime($waktuTarget)) / 60);
        return max(0, 100 - (floor($selisihMenit / 3) * 10));
    }

    public function downloadPDF(Request $request)
    {
        try {
            $date = $request->get('tanggal');
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
            $allScoreCards = [];
            $currentSession = session('unit');

            // Mapping koneksi database dengan nama unit yang sesuai
            $connections = [
                'mysql' => [
                    'mysql_bau_bau' => 'Bau-Bau',
                    'mysql_kolaka' => 'Kolaka', 
                    'mysql_poasia' => 'Poasia',
                    'mysql_wua_wua' => 'Wua-Wua',
                ],
                'mysql_bau_bau' => ['mysql_bau_bau' => 'Bau-Bau'],
                'mysql_kolaka' => ['mysql_kolaka' => 'Kolaka'],
                'mysql_poasia' => ['mysql_poasia' => 'Poasia'],
                'mysql_wua_wua' => ['mysql_wua_wua' => 'Wua-Wua']
            ];

            // Pilih koneksi berdasarkan session
            $activeConnections = $connections[$currentSession] ?? [];
            
            \Log::info('PDF Generation Debug:', [
                'session' => $currentSession,
                'date' => $date,
                'activeConnections' => $activeConnections
            ]);

            // Data untuk semua unit
            $attendances = Attendance::whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            $powerPlants = PowerPlant::with(['machines' => function($query) use ($date) {
                $query->whereHas('statusLogs', function($q) use ($date) {
                    $q->whereDate('created_at', $date);
                });
            }])->get();

            $logs = MachineStatusLog::whereDate('created_at', $date)
                ->with('machine.powerPlant')
                ->get();

            // Modifikasi query untuk data bulanan
            $serviceRequests = ServiceRequest::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('priority', 'desc')
                ->get();

            $workOrders = WorkOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at')
                ->get();

            $woBacklogs = WoBacklog::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at')
                ->get();

            // Modifikasi query untuk Other Discussions - hanya ambil yang masih open
            $otherDiscussions = OtherDiscussion::where('status', 'open')
                ->where(function($query) use ($date) {
                    $query->whereDate('created_at', '<=', $date)
                          ->whereNull('closed_at');
                })
                ->with(['commitments' => function($query) {
                    $query->where('status', 'open');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // Tambahkan logging untuk memantau data
            \Log::info('Open Other Discussions data:', [
                'count' => $otherDiscussions->count(),
                'date' => $date,
                'discussions' => $otherDiscussions->map(function($discussion) {
                    return [
                        'id' => $discussion->id,
                        'topic' => $discussion->topic,
                        'created_at' => $discussion->created_at,
                        'open_commitments_count' => $discussion->commitments->count()
                    ];
                })
            ]);
            
            foreach ($activeConnections as $connection => $unitName) {
                try {
                    \Log::info("Trying to fetch data for connection: {$connection}");
                    
                    // Score Card Data - Menggunakan nama tabel yang benar
                    $scoreCard = DB::connection($connection)
                        ->table('score_card_daily') // Menggunakan nama tabel yang benar
                        ->whereDate('tanggal', $date)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($scoreCard) {
                        \Log::info("Found scorecard data for {$unitName}", [
                            'scoreCard' => $scoreCard,
                            'peserta' => json_decode($scoreCard->peserta, true)
                        ]);
                        
                        // Hitung skor waktu mulai
                        $waktuMulaiTarget = "07:30:00";
                        $waktuMulaiActual = $scoreCard->waktu_mulai;
                        $selisihMenit = round((strtotime($waktuMulaiActual) - strtotime($waktuMulaiTarget)) / 60);
                        $skorWaktuMulai = max(0, 100 - (floor($selisihMenit / 3) * 10));

                        $allScoreCards[$unitName] = [
                            'lokasi' => $scoreCard->lokasi,
                            'waktu_mulai' => $scoreCard->waktu_mulai,
                            'waktu_selesai' => $scoreCard->waktu_selesai,
                            'peserta' => json_decode($scoreCard->peserta, true),
                            'kesiapan_panitia' => $scoreCard->kesiapan_panitia,
                            'kesiapan_bahan' => $scoreCard->kesiapan_bahan,
                            'aktivitas_luar' => $scoreCard->aktivitas_luar,
                            'gangguan_diskusi' => $scoreCard->gangguan_diskusi,
                            'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk,
                            'gangguan_interupsi' => $scoreCard->gangguan_interupsi,
                            'ketegasan_moderator' => $scoreCard->ketegasan_moderator,
                            'kelengkapan_sr' => $scoreCard->kelengkapan_sr,
                            'skor_waktu_mulai' => $skorWaktuMulai
                        ];
                    }

                    // Machine Status Data
                    $statuses = DB::connection($connection)
                        ->table('machine_status_logs AS msl')
                        ->join('machines AS m', 'msl.machine_id', '=', 'm.id')
                        ->join('power_plants AS pp', 'm.power_plant_id', '=', 'pp.id')
                        ->whereDate('msl.created_at', $date)
                        ->select(
                            'msl.id',
                            'msl.status',
                            'msl.created_at',
                            'msl.dmp',
                            'msl.dmn',
                            'msl.load_value',
                            'msl.component',
                            'm.name as machine_name',
                            'pp.name as power_plant_name'
                        )
                        ->orderBy('m.name')
                        ->orderBy('msl.created_at', 'desc')
                        ->get();

                    if ($statuses->isNotEmpty()) {
                        $groupedStatuses = $statuses->groupBy('machine_name')
                            ->map(function ($machineStatuses) {
                                return $machineStatuses->first();
                            });
                        $allMachineStatuses[$unitName] = $groupedStatuses;
                    }

                } catch (\Exception $e) {
                    \Log::error("Error processing {$unitName} data:", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            \Log::info('Data collected for PDF:', [
                'scoreCards' => count($allScoreCards),
                'machineStatuses' => count($allMachineStatuses),
                'attendances' => $attendances->count(),
                'serviceRequests' => $serviceRequests->count(),
                'workOrders' => $workOrders->count(),
                'woBacklogs' => $woBacklogs->count()
            ]);

            // Sebelum generate PDF, log semua data yang akan dikirim ke view
            \Log::info('Data being sent to PDF view:', [
                'scoreCardsCount' => count($allScoreCards),
                'attendancesCount' => $attendances->count(),
                'powerPlantsCount' => $powerPlants->count(),
                'logsCount' => $logs->count(),
                'serviceRequestsCount' => $serviceRequests->count(),
                'workOrdersCount' => $workOrders->count()
            ]);

            $pdf = PDF::loadView('admin.meetings.score-card-pdf', [
                'allScoreCards' => $allScoreCards,
                'date' => $date,
                'attendances' => $attendances,
                'powerPlants' => $powerPlants,
                'logs' => $logs,
                'serviceRequests' => $serviceRequests,
                'workOrders' => $workOrders,
                'woBacklogs' => $woBacklogs,
                'otherDiscussions' => $otherDiscussions,
                'logoSrc' => public_path('logo/navlog1.png')
            ]);

            return $pdf->stream('score-card.pdf');

        } catch (\Exception $e) {
            \Log::error('PDF generation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal mengunduh PDF. Error: ' . $e->getMessage());
        }
    }
}
