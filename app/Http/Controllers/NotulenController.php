<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotulenController extends Controller
{
    public function form()
    {
        $nextNomorUrut = Notulen::max('nomor_urut') + 1;
        return view('notulen.form', compact('nextNomorUrut'));
    }

    public function create(Request $request)
    {
        return view('notulen.create', [
            'nomor_urut' => $request->nomor_urut,
            'unit' => $request->unit,
            'bidang' => $request->bidang,
            'sub_bidang' => $request->sub_bidang,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_urut' => 'required',
            'unit' => 'required',
            'bidang' => 'required',
            'sub_bidang' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'pimpinan_rapat' => 'required',
            'tempat' => 'required',
            'agenda' => 'required',
            'peserta' => 'required',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'pembahasan' => 'required',
            'tindak_lanjut' => 'required',
            'pimpinan_rapat_nama' => 'required',
            'notulis_nama' => 'required',
            'tanggal_tanda_tangan' => 'required|date'
        ]);

        // Generate format nomor
        $formatNomor = Notulen::generateFormatNomor(
            $validated['nomor_urut'],
            $validated['unit'],
            $validated['bidang'],
            $validated['sub_bidang'],
            $validated['bulan'],
            $validated['tahun']
        );

        // Create the notulen
        $notulen = Notulen::create([
            ...$validated,
            'format_nomor' => $formatNomor
        ]);

        // Redirect to show view
        return redirect()->route('notulen.show', $notulen->id);
    }

    public function show(Notulen $notulen)
    {
        return view('notulen.show', compact('notulen'));
    }
}
