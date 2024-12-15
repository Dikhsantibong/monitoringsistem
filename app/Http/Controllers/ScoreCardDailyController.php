<?php

namespace App\Http\Controllers;

use App\Models\ScoreCardDaily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScoreCardDailyController extends Controller
{
    private $accountId;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->accountId = env('ZOOM_ACCOUNT_ID');
        $this->clientId = env('ZOOM_CLIENT_ID');
        $this->clientSecret = env('ZOOM_CLIENT_SECRET');
    }

    public function index()
    {
        $scoreCards = ScoreCardDaily::latest()->get();

        // Menghitung total skor peserta
        $totalScore = $scoreCards->sum(function ($scoreCard) {
            $pesertas = json_decode($scoreCard->peserta, true);
            return array_sum(array_column($pesertas, 'skor'));
        });

        // Menggabungkan semua ketentuan rapat dari setiap scoreCard
        $ketentuanRapat = [];
        foreach ($scoreCards as $card) {
            $ketentuan = json_decode($card->ketentuan_rapat, true);
            if (!empty($ketentuan)) {
                $ketentuanRapat[] = $ketentuan; // Simpan ketentuan rapat
            }
        }

        // Menggabungkan semua ketentuan rapat menjadi satu array
        $ketentuanRapat = array_merge(...$ketentuanRapat); // Menggabungkan semua ketentuan rapat

        return view('admin.score-card.index', compact('scoreCards', 'totalScore', 'ketentuanRapat'));
    }

    public function create()
    {
        // Ambil data kehadiran hari ini
        $today = now()->format('Y-m-d');
        $attendances = \App\Models\Attendance::whereDate('time', $today)
            ->select('name', 'division', 'time')
            ->get();

        // Kelompokkan peserta berdasarkan divisi
        $peserta = [
            'manager_up' => $attendances->where('division', 'MANAGER UP')->count(),
            'asman_operasi' => $attendances->where('division', 'ASMAN OPERASI')->count(),
            'asman_pemeliharaan' => $attendances->where('division', 'ASMAN PEMELIHARAAN')->count(),
            'asman_enjiniring' => $attendances->where('division', 'ASMAN ENJINIRING')->count(),
            'tl_rendal_har' => $attendances->where('division', 'TL RENDAL HAR')->count(),
            'tl_icc' => $attendances->where('division', 'TL ICC')->count(),
            'tl_outage' => $attendances->where('division', 'TL OUTAGE MANAGEMENT')->count(),
            'tl_k3' => $attendances->where('division', 'TL K3 DAN KAM')->count(),
            'tl_lingkungan' => $attendances->where('division', 'TL LINGKUNGAN')->count(),
        ];

        // Ambil waktu awal dan akhir kehadiran
        $waktuMulai = $attendances->min('time');
        $waktuSelesai = $attendances->max('time');

        return view('admin.score-card.create', compact('peserta', 'waktuMulai', 'waktuSelesai'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'peserta' => 'required|array',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'kesiapan_panitia' => 'nullable|integer|max:100',
            'kesiapan_bahan' => 'nullable|integer|max:100',
            'kontribusi_pemikiran' => 'nullable|integer|max:100',
            'aktivitas_luar' => 'nullable|integer|max:100',
            'gangguan_diskusi' => 'nullable|integer|max:100',
            'gangguan_keluar_masuk' => 'nullable|integer|max:100',
            'gangguan_interupsi' => 'nullable|integer|max:100',
            'ketegasan_moderator' => 'nullable|integer|max:100',
            'kelengkapan_sr' => 'nullable|integer|max:100',
        ]);

        // Ambil data kehadiran untuk validasi
        $attendances = \App\Models\Attendance::whereDate('time', $validated['tanggal'])->get();
        
        // Hitung jumlah kehadiran awal dan akhir
        $awal = $attendances->where('time', $attendances->min('time'))->count();
        $akhir = $attendances->where('time', $attendances->max('time'))->count();
        
        // Hitung skor berdasarkan kehadiran
        $skor = 0;
        if ($awal > 0 && $akhir > 0) {
            $skor = 100;
        } elseif ($awal > 0 || $akhir > 0) {
            $skor = 50;
        }

        // Tambahkan ini di bagian store() setelah validasi
        $ketentuanRapat = [
            'aktifitas_meeting' => $validated['aktifitas_meeting'] ?? 100,
            'gangguan_diskusi' => $validated['gangguan_diskusi'] ?? 100,
            'gangguan_keluar_masuk' => $validated['gangguan_keluar_masuk'] ?? 100,
            'gangguan_interupsi' => $validated['gangguan_interupsi'] ?? 100,
            'ketegasan_moderator' => $validated['ketegasan_moderator'] ?? 100,
            'kelengkapan_sr' => $validated['kelengkapan_sr'] ?? 100,
        ];

        // Buat record baru
        ScoreCardDaily::create([
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'],
            'peserta' => json_encode($validated['peserta']), // Simpan sebagai JSON
            'awal' => $awal,
            'akhir' => $akhir,
            'skor' => $skor,
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kesiapan_panitia' => $validated['kesiapan_panitia'] ?? 100,
            'kesiapan_bahan' => $validated['kesiapan_bahan'] ?? 100,
            'kontribusi_pemikiran' => $validated['kontribusi_pemikiran'] ?? 100,
            'aktivitas_luar' => $validated['aktivitas_luar'] ?? 100,
            'gangguan_diskusi' => $validated['gangguan_diskusi'] ?? 100,
            'gangguan_keluar_masuk' => $validated['gangguan_keluar_masuk'] ?? 100,
            'gangguan_interupsi' => $validated['gangguan_interupsi'] ?? 100,
            'ketegasan_moderator' => $validated['ketegasan_moderator'] ?? 100,
            'kelengkapan_sr' => $validated['kelengkapan_sr'] ?? 100,
            'ketentuan_rapat' => json_encode($ketentuanRapat), // Simpan sebagai JSON
        ]);

        return redirect()->route('admin.score-card.index')
            ->with('success', 'Score Card berhasil dibuat')
            ->with('ketentuanRapat', $ketentuanRapat);
    }

    public function createZoomMeeting()
    {
        try {
            // Gunakan kredensial yang sudah diberikan
            $clientId = 'BbC6afj3TNmAr5gthKqnA';
            $clientSecret = '2CVS9oG5yhzULiGNbzjVjVPi7Z2AEVRx';
            $accountId = '6lDJP-NMTr2tz_sr_0Yskw';

            // Request token dengan Server-to-Server OAuth
            $tokenUrl = 'https://zoom.us/oauth/token';
            
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
            ])->asForm()->post($tokenUrl, [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId
            ]);

            // Log untuk debugging
            Log::info('Token Request URL: ' . $tokenUrl);
            Log::info('Token Request Headers:', [
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret)
            ]);
            Log::info('Token Request Body:', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId
            ]);
            Log::info('Token Response:', $tokenResponse->json());

            if (!$tokenResponse->successful()) {
                Log::error('Zoom Token Error:', [
                    'status' => $tokenResponse->status(),
                    'response' => $tokenResponse->json()
                ]);
                return response()->json([
                    'error' => 'Gagal mendapatkan token',
                    'details' => $tokenResponse->json()
                ], 400);
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Buat meeting dengan token yang didapat
            $meetingResponse = Http::withToken($accessToken)
                ->post('https://api.zoom.us/v2/users/me/meetings', [
                    'topic' => 'Daily Meeting ' . now()->format('d F Y'),
                    'type' => 2,
                    'start_time' => now()->format('Y-m-d\TH:i:s'),
                    'duration' => 60,
                    'timezone' => 'Asia/Jakarta',
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => true,
                        'mute_upon_entry' => false,
                        'watermark' => false,
                        'use_pmi' => false,
                        'approval_type' => 0,
                        'audio' => 'both',
                        'auto_recording' => 'none'
                    ]
                ]);

            if (!$meetingResponse->successful()) {
                Log::error('Zoom Meeting Creation Error:', [
                    'status' => $meetingResponse->status(),
                    'response' => $meetingResponse->json()
                ]);
                return response()->json([
                    'error' => 'Gagal membuat meeting',
                    'details' => $meetingResponse->json()
                ], 400);
            }

            $meetingData = $meetingResponse->json();
            return response()->json([
                'success' => true,
                'message' => 'Meeting berhasil dibuat',
                'data' => [
                    'join_url' => $meetingData['join_url'],
                    'start_url' => $meetingData['start_url'],
                    'password' => $meetingData['password'] ?? null,
                    'meeting_id' => $meetingData['id'],
                    'start_time' => $meetingData['start_time']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Zoom Meeting Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
        
}