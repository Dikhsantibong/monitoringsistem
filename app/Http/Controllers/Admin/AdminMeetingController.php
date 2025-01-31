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
            $date = $request->date ?? now()->format('Y-m-d');
            $allScoreCards = [];
            
            \Log::info('Print function called with date:', ['date' => $date]);
            
            $connections = [
                'mysql_bau_bau' => 'Bau-Bau',
                'mysql_kolaka' => 'Kolaka', 
                'mysql_poasia' => 'Poasia',
                'mysql_wua_wua' => 'Wua-Wua',
                'u478221055_up_kendari' => 'UP Kendari'
            ];
            
            foreach ($connections as $connection => $unitName) {
                try {
                    \Log::info("Trying to access database connection: {$connection}");
                    
                    $query = DB::connection($connection)
                        ->table('score_card_daily')
                        ->whereDate('tanggal', $date);
                    
                    \Log::info("Query for {$connection}:", [
                        'sql' => $query->toSql(),
                        'bindings' => $query->getBindings()
                    ]);
                    
                    $scoreCard = $query->orderBy('created_at', 'desc')->first();
                    
                    \Log::info("Score card data for {$connection}:", [
                        'found' => !is_null($scoreCard),
                        'data' => $scoreCard
                    ]);
                    
                    if ($scoreCard) {
                        // Hitung skor waktu mulai berdasarkan waktu yang ada
                        $waktuMulaiTarget = "07:30:00"; // Waktu target mulai rapat
                        $waktuMulaiActual = $scoreCard->waktu_mulai;
                        
                        // Hitung selisih dalam menit
                        $selisihMenit = round((strtotime($waktuMulaiActual) - strtotime($waktuMulaiTarget)) / 60);
                        
                        // Hitung skor (-10 per 3 menit keterlambatan)
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
                            'skor_waktu_mulai' => $skorWaktuMulai // Menggunakan skor yang dihitung
                        ];
                        \Log::info("Successfully added score card for {$unitName}", [
                            'waktu_mulai_actual' => $waktuMulaiActual,
                            'selisih_menit' => $selisihMenit,
                            'skor_waktu_mulai' => $skorWaktuMulai
                        ]);
                    } else {
                        \Log::info("No score card found for {$unitName} on date {$date}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error accessing {$connection}: " . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            \Log::info('Final AllScoreCards data:', [
                'count' => count($allScoreCards),
                'data' => $allScoreCards
            ]); 

            return view('admin.meetings.print', [
                'allScoreCards' => $allScoreCards,
                'date' => $date,
                'attendances' => [], 
                'powerPlants' => [], 
                'logs' => [], 
                'serviceRequests' => [], 
                'workOrders' => [], 
                'woBacklogs' => [], 
                'signatures' => [] 
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memuat data print.');
        }
    }

    public function downloadPDF(Request $request)
    {
        try {
            $date = $request->get('tanggal');
            \Log::info('Attempting to download PDF for date: ' . $date);
            
            // Get logo path
            $logoPath = public_path('logo/navlog1.png');
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;

            // 1. Score Card Daily
            $scoreCard = ScoreCardDaily::whereDate('tanggal', $date)
                ->latest()
                ->first();

            if (!$scoreCard) {
                return back()->with('error', 'Data tidak ditemukan untuk tanggal tersebut');
            }

            // Format data peserta
            $peserta = json_decode($scoreCard->peserta, true) ?? [];
            $formattedPeserta = [];
            foreach ($peserta as $jabatan => $data) {
                $formattedPeserta[] = [
                    'jabatan' => ucwords(str_replace('_', ' ', $jabatan)),
                    'awal' => $data['awal'] ?? '0',
                    'akhir' => $data['akhir'] ?? '0',
                    'skor' => $data['skor'] ?? '0',
                    'keterangan' => $data['keterangan'] ?? ''
                ];
            }

            // 2. Daftar Hadir
            $attendances = Attendance::whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            // 3. Status Pembangkit
            $machineStatuses = MachineStatusLog::whereDate('created_at', $date)
                ->with(['machine.powerPlant'])
                ->orderBy('machine_id')
                ->orderBy('created_at')
                ->get();

            // 4. Service Requests
            $serviceRequests = ServiceRequest::whereDate('created_at', $date)
                ->orderBy('priority', 'desc')
                ->orderBy('created_at')
                ->get();

            // 5. Work Orders
            $workOrders = WorkOrder::whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            // 6. Work Order Backlog
            $woBacklogs = WoBacklog::whereDate('created_at', $date)
                ->orderBy('created_at')
                ->get();

            // 7. Other Discussions (Pembahasan Lain)
            $otherDiscussions = OtherDiscussion::whereDate('created_at', $date)->get();
            $closedDiscussions = ClosedDiscussion::whereDate('created_at', $date)->get();
            $overdueDiscussions = OverdueDiscussion::whereDate('created_at', $date)->get();

            // Gabungkan semua diskusi
            $discussions = collect()
                ->concat($otherDiscussions)
                ->concat($closedDiscussions)
                ->concat($overdueDiscussions)
                ->sortBy('created_at');

            // Data untuk Score Card
            $data = [
                'lokasi' => $scoreCard->lokasi,
                'waktu_mulai' => $scoreCard->waktu_mulai,
                'waktu_selesai' => $scoreCard->waktu_selesai,
                'peserta' => $formattedPeserta,
                'kesiapan_panitia' => $scoreCard->kesiapan_panitia ?? 100,
                'kesiapan_bahan' => $scoreCard->kesiapan_bahan ?? 100,
                'aktivitas_luar' => $scoreCard->aktivitas_luar ?? 100,
                'gangguan_diskusi' => $scoreCard->gangguan_diskusi ?? 100,
                'gangguan_keluar_masuk' => $scoreCard->gangguan_keluar_masuk ?? 100,
                'gangguan_interupsi' => $scoreCard->gangguan_interupsi ?? 100,
                'ketegasan_moderator' => $scoreCard->ketegasan_moderator ?? 100,
                'kelengkapan_sr' => $scoreCard->kelengkapan_sr ?? 100,
                'skor_waktu_mulai' => $scoreCard->skor_waktu_mulai ?? 100
            ];

            // 8. Signatures (jika ada)
            $signatures = [
                'Operasi' => null,
                'Pemeliharaan' => null,
                'Enjiniring' => null,
                'Manajer' => null
            ];

            \Log::info('Loading PDF view with data...');

            // Generate PDF dengan semua data yang diperlukan
            $pdf = PDF::loadView('admin.meetings.score-card-pdf', compact(
                'date',
                'data',
                'attendances',
                'machineStatuses',
                'serviceRequests',
                'workOrders',
                'woBacklogs',
                'discussions',
                'signatures',
                'logoSrc'
            ));
            
            $pdf->setPaper('a4', 'portrait');
            
            \Log::info('PDF generated successfully, initiating download...');

            return $pdf->download('score_card_' . $date . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error in downloadPDF: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Gagal mengunduh PDF. Error: ' . $e->getMessage());
        }
    }
}
