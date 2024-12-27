<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Department; // Pastikan model Department di-import
use App\Models\ScoreCardDaily; // Ambil model ScoreCardDaily
use Illuminate\Http\Request;

class AdminMeetingController extends Controller
{
    public function index()
    {
        try {
            $selectedDate = request('tanggal', now()->format('Y-m-d'));
            
            $scoreCards = ScoreCardDaily::whereDate('tanggal', $selectedDate)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($scoreCard) {
                    $peserta = json_decode($scoreCard->peserta, true) ?? [];
                    $formattedPeserta = [];
                    
                    foreach ($peserta as $jabatan => $data) {
                        $formattedPeserta[] = [
                            'jabatan' => ucwords(str_replace('_', ' ', $jabatan)),
                            'awal' => $data['awal'] ?? '0',
                            'akhir' => $data['akhir'] ?? '0',
                            'skor' => $data['skor'] ?? '0'
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
                        'aktivitas_luar' => $scoreCard->aktivitas_luar
                    ];
                });

            $availableDates = ScoreCardDaily::orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->unique()
                ->values();

            if (request()->ajax()) {
                return view('admin.meetings._table', [
                    'scoreCards' => $scoreCards
                ])->render();
            }

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
            $date = $request->date;
            \Log::info('Print view requested for date: ' . $date);

            $scoreCards = ScoreCardDaily::whereDate('tanggal', $date)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($scoreCard) {
                    $peserta = json_decode($scoreCard->peserta, true) ?? [];
                    $formattedPeserta = [];
                    
                    foreach ($peserta as $jabatan => $data) {
                        $formattedPeserta[] = [
                            'jabatan' => ucwords(str_replace('_', ' ', $jabatan)),
                            'awal' => $data['awal'] ?? '0',
                            'akhir' => $data['akhir'] ?? '0',
                            'skor' => $data['skor'] ?? '0'
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
                        'aktivitas_luar' => $scoreCard->aktivitas_luar
                    ];
                });

            if ($scoreCards->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk tanggal ini.');
            }

            return view('admin.meetings.print', [
                'scoreCards' => $scoreCards,
                'date' => $date
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in printView: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data print.');
        }
    }
}