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
        try {
            $scoreCards = ScoreCardDaily::latest()->get();
            $latestScoreCard = $scoreCards->first();
            
            $totalScore = 0;
            $pesertaData = [];
            $ketentuanScores = [];
            
            if ($latestScoreCard) {
                // Ambil data peserta dari model (sudah ditangani oleh accessor)
                $pesertaData = $latestScoreCard->peserta;

                // Definisikan field-field ketentuan rapat
                $ketentuanFields = [
                    'kesiapan_panitia',
                    'kesiapan_bahan',
                    'aktivitas_luar',
                    'gangguan_diskusi',
                    'gangguan_keluar_masuk',
                    'gangguan_interupsi',
                    'ketegasan_moderator',
                    'kelengkapan_sr'
                ];

                // Hitung skor ketentuan
                $ketentuanScores = collect($ketentuanFields)->mapWithKeys(function ($field) use ($latestScoreCard) {
                    return [$field => intval($latestScoreCard->$field ?? 0)];
                })->toArray();

                // Hitung total skor
                $pesertaScore = collect($pesertaData)->sum('skor');
                $ketentuanScore = array_sum($ketentuanScores);
                $totalScore = $pesertaScore + $ketentuanScore;

                Log::info('Score calculation completed', [
                    'peserta_score' => $pesertaScore,
                    'ketentuan_score' => $ketentuanScore,
                    'total_score' => $totalScore
                ]);
            }

            return view('admin.score-card.index', compact(
                'scoreCards',
                'latestScoreCard',
                'pesertaData',
                'totalScore',
                'ketentuanScores'
            ));

        } catch (\Exception $e) {
            Log::error('Error in ScoreCardDaily index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin.score-card.index', [
                'scoreCards' => collect(),
                'latestScoreCard' => null,
                'pesertaData' => [],
                'totalScore' => 0,
                'ketentuanScores' => []
            ])->with('error', 'Terjadi kesalahan saat memuat data.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'lokasi' => 'required|string',
                'peserta' => 'required',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
                'kesiapan_panitia' => 'required|integer',
                'kesiapan_bahan' => 'required|integer',
                'kontribusi_pemikiran' => 'required|integer',
                'aktivitas_luar' => 'required|integer',
                'gangguan_diskusi' => 'required|integer',
                'gangguan_keluar_masuk' => 'required|integer',
                'gangguan_interupsi' => 'required|integer',
                'ketegasan_moderator' => 'required|integer',
                'kelengkapan_sr' => 'required|integer',
                'ketentuan_rapat' => 'nullable|array',
                'keterangan' => 'nullable|string'
            ]);

            // Pastikan data peserta dalam format yang benar
            $pesertaData = $validated['peserta'];
            if (is_string($pesertaData)) {
                $pesertaData = json_decode($pesertaData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON format for peserta data');
                }
            }

            // Buat instance baru ScoreCardDaily
            $scoreCard = new ScoreCardDaily();
            $scoreCard->fill($validated);
            $scoreCard->peserta = $pesertaData;
            $scoreCard->unit_source = session('unit');
            $scoreCard->save();

            Log::info('ScoreCard created successfully', [
                'id' => $scoreCard->id,
                'unit' => session('unit')
            ]);

            return redirect()
                ->route('admin.score-card.index')
                ->with('success', 'Score card berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error('Error storing ScoreCard:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan score card: ' . $e->getMessage());
        }
    }

    public function updatePeserta(Request $request)
    {
        try {
            $validated = $request->validate([
                'peserta' => 'required|array',
                'peserta.*.id' => 'required|integer',
                'peserta.*.jabatan' => 'required|string'
            ]);

            foreach ($validated['peserta'] as $data) {
                Peserta::updateOrCreate(
                    ['id' => $data['id']],
                    [
                        'jabatan' => $data['jabatan'],
                        'unit_source' => session('unit')
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Peserta berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating peserta:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating peserta: ' . $e->getMessage()
            ], 500);
        }
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