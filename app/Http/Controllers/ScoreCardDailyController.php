<?php

namespace App\Http\Controllers;

use App\Models\ScoreCardDaily;
use App\Models\Peserta;
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
        // Ambil data scorecard terbaru
        $scoreCards = ScoreCardDaily::latest()->get();
        $scorePercentage = 0; // Default value

        // Cek apakah ada data
        if ($scoreCards->isEmpty()) {
            return view('admin.score-card.index', [
                'scoreCards' => collect([]),
                'scorePercentage' => $scorePercentage
            ]);
        }

        // Ambil scoreCard terbaru
        $latestScoreCard = $scoreCards->first();
        
        try {
            // Pastikan data peserta valid
            $pesertaData = [];
            if ($latestScoreCard && $latestScoreCard->peserta) {
                $pesertaData = json_decode($latestScoreCard->peserta, true) ?? [];
            }
            
            // 1. Hitung total skor peserta
            $totalPesertaScore = array_sum(array_column($pesertaData, 'skor'));
            $jumlahPeserta = count($pesertaData);
            
            // 2. Hitung total skor ketentuan rapat dengan null coalescing
            $totalKetentuanScore = 
                ($latestScoreCard->skor_waktu_mulai ?? 0) +
                ($latestScoreCard->skor_waktu_selesai ?? 0) +
                ($latestScoreCard->kesiapan_panitia ?? 0) +
                ($latestScoreCard->kesiapan_bahan ?? 0) +
                ($latestScoreCard->aktivitas_luar ?? 0) +
                ($latestScoreCard->gangguan_diskusi ?? 0) +
                ($latestScoreCard->gangguan_keluar_masuk ?? 0) +
                ($latestScoreCard->gangguan_interupsi ?? 0) +
                ($latestScoreCard->ketegasan_moderator ?? 0) +
                ($latestScoreCard->kelengkapan_sr ?? 0);

            // 3. Hitung total keseluruhan
            $totalActualScore = $totalPesertaScore + $totalKetentuanScore;
            
            // 4. Hitung skor maksimum yang mungkin
            $maxPesertaScore = $jumlahPeserta * 100; // Maksimum 100 per peserta
            $maxKetentuanScore = 10 * 100; // 10 ketentuan rapat, masing-masing maksimum 100
            $totalMaxScore = $maxPesertaScore + $maxKetentuanScore;
            
            // 5. Hitung persentase (dibulatkan ke 2 desimal)
            $scorePercentage = $totalMaxScore > 0 ? 
                round(($totalActualScore / $totalMaxScore) * 100, 2) : 0;

        } catch (\Exception $e) {
            \Log::error('Error calculating score: ' . $e->getMessage());
            $scorePercentage = 0;
        }

        return view('admin.score-card.index', [
            'scoreCards' => $scoreCards,
            'scorePercentage' => $scorePercentage
        ]);
    }

    public function create()
    {
        // Ambil data peserta dari database
        $defaultPeserta = Peserta::select('id', 'jabatan')->get()->toArray();

        // Data lainnya tetap sama
        $today = now()->format('Y-m-d');
        $attendances = \App\Models\Attendance::whereDate('time', $today)
            ->select('name', 'division', 'time')
            ->get();

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

        $waktuMulai = $attendances->min('time');
        $waktuSelesai = $attendances->max('time');

        return view('admin.score-card.create', compact('peserta', 'waktuMulai', 'waktuSelesai', 'defaultPeserta'));
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
            'keterangan' => 'nullable|string'
        ]);

        // Ambil data kehadiran untuk validasi
        $attendances = \App\Models\Attendance::whereDate('time', $validated['tanggal'])->get();


        // Ambil data peserta dari database untuk referensi jabatan
        $pesertaDb = Peserta::all()->keyBy('id');
        
        // Format ulang data peserta yang diterima
        $formattedPeserta = [];
        foreach ($validated['peserta'] as $id => $data) {
            // Pastikan peserta ada di database
            if (isset($pesertaDb[$id])) {
                $formattedPeserta[$pesertaDb[$id]->jabatan] = [
                    'awal' => $data['awal'] ?? '0',
                    'akhir' => $data['akhir'] ?? '0',
                    'skor' => $data['skor'] ?? '0',
                    'keterangan' => $data['keterangan'] ?? '',
                    'jabatan' => $pesertaDb[$id]->jabatan // Tambahkan jabatan
                ];
            }
        }

        // Buat record baru dengan data peserta yang sudah diformat
        ScoreCardDaily::create([
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'],
            'peserta' => json_encode($formattedPeserta), // Simpan data peserta yang sudah diformat
            'awal' => $attendances->where('time', $attendances->min('time'))->count(),
            'akhir' => $attendances->where('time', $attendances->max('time'))->count(),
            'skor' => 0,
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
            'ketentuan_rapat' => json_encode([
                'aktifitas_meeting' => $validated['aktifitas_meeting'] ?? 100,
                'gangguan_diskusi' => $validated['gangguan_diskusi'] ?? 100,
                'gangguan_keluar_masuk' => $validated['gangguan_keluar_masuk'] ?? 100,
                'gangguan_interupsi' => $validated['gangguan_interupsi'] ?? 100,
                'ketegasan_moderator' => $validated['ketegasan_moderator'] ?? 100,
                'kelengkapan_sr' => $validated['kelengkapan_sr'] ?? 100,
            ]),
        ]);

        return redirect()->route('admin.score-card.index')
            ->with('success', 'Score Card berhasil dibuat');
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

    // Tambahkan method untuk update peserta
    public function updatePeserta(Request $request)
    {
        try {
            $pesertaData = $request->input('peserta', []);
            
            foreach ($pesertaData as $data) {
                Peserta::updateOrCreate(
                    ['id' => $data['id']],
                    ['jabatan' => $data['jabatan']]
                );
            }

            return response()->json(['message' => 'Peserta berhasil diupdate'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating peserta: ' . $e->getMessage()], 500);
        }
    }
}