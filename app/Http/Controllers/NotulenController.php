<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;

class NotulenController extends Controller
{
    public function create()
    {
        // Get the last nomor urut and increment it
        $lastNotulen = Notulen::orderBy('id', 'desc')->first();
        $nextNomorUrut = $lastNotulen ? (int)$lastNotulen->nomor_urut + 1 : 1;

        return view('notulen.form', [
            'nextNomorUrut' => str_pad($nextNomorUrut, 4, '0', STR_PAD_LEFT)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_urut' => 'required|string',
            'unit' => 'required|string',
            'bidang' => 'required|string',
            'sub_bidang' => 'required|string',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'pembahasan' => 'required|string',
            'tindak_lanjut' => 'required|string',
        ]);

        // Generate the formatted number
        $format_nomor = Notulen::generateFormatNomor(
            $validated['nomor_urut'],
            $validated['unit'],
            $validated['bidang'],
            $validated['sub_bidang'],
            $validated['bulan'],
            $validated['tahun']
        );

        // Check if the format number already exists
        if (Notulen::where('format_nomor', $format_nomor)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['format_nomor' => 'Nomor format ini sudah ada dalam sistem.']);
        }

        // Create the notulen
        $notulen = Notulen::create([
            ...$validated,
            'format_nomor' => $format_nomor
        ]);

        return redirect()->route('notulen.form')
            ->with('success', 'Notulen berhasil disimpan dengan nomor: ' . $format_nomor);
    }
}
