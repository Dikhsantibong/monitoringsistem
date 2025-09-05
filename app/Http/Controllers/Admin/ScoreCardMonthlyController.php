<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ScoreCardMonthly;
use App\Models\Peserta;
use App\Models\Attendance;

class ScoreCardMonthlyController extends Controller
{
    public function create()
    {
        $defaultPeserta = Peserta::select('id', 'jabatan')->get()->toArray();
        $today = now()->format('Y-m-d');
        $attendances = Attendance::whereDate('time', $today)
            ->select('name', 'division', 'time')
            ->get();
        $waktuMulai = $attendances->min('time');
        $waktuSelesai = $attendances->max('time');
        return view('admin.score-card.create-monthly', compact('defaultPeserta', 'waktuMulai', 'waktuSelesai'));
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
        $attendances = Attendance::whereDate('time', $validated['tanggal'])->get();
        $pesertaDb = Peserta::all()->keyBy('id');
        $formattedPeserta = [];
        foreach ($validated['peserta'] as $id => $data) {
            if (isset($pesertaDb[$id])) {
                $formattedPeserta[$pesertaDb[$id]->jabatan] = [
                    'awal' => $data['awal'] ?? '0',
                    'akhir' => $data['akhir'] ?? '0',
                    'skor' => $data['skor'] ?? '0',
                    'keterangan' => $data['keterangan'] ?? '',
                    'jabatan' => $pesertaDb[$id]->jabatan
                ];
            }
        }
        ScoreCardMonthly::create([
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'],
            'peserta' => json_encode($formattedPeserta),
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
        ]);
        return redirect()->route('admin.score-card.index')->with('success', 'Score Card Monthly berhasil dibuat');
    }
}
