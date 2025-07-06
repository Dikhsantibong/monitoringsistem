<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use App\Models\NotulenAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'nomor_urut' => 'required',
                'unit' => 'required',
                'bidang' => 'required',
                'sub_bidang' => 'required',
                'bulan' => 'required',
                'tahun' => 'required',
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
                'tanggal_tanda_tangan' => 'required|date',
                'temp_notulen_id' => 'nullable|string'
            ]);

            // Sanitize HTML content but preserve basic formatting
            $validated['pembahasan'] = strip_tags($validated['pembahasan'], '<p><br><ul><ol><li><strong><em><u><s>');
            $validated['tindak_lanjut'] = strip_tags($validated['tindak_lanjut'], '<p><br><ul><ol><li><strong><em><u><s>');

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
                'format_nomor' => $formatNomor,
                'pimpinan_rapat' => $validated['pimpinan_rapat_nama']
            ]);

            // Get attendance data from session if exists
            if (isset($validated['temp_notulen_id'])) {
                $attendances = Session::get("temp_attendances_{$validated['temp_notulen_id']}", []);
                foreach ($attendances as $attendance) {
                    NotulenAttendance::create([
                        'notulen_id' => $notulen->id,
                        'name' => $attendance['name'],
                        'position' => $attendance['position'],
                        'signature' => $attendance['signature']
                    ]);
                }

                // Clear temporary data
                Session::forget("temp_attendances_{$validated['temp_notulen_id']}");
                Cache::forget("notulen_attendances_{$validated['temp_notulen_id']}");
            }

            DB::commit();

            // Redirect to show view with success message
            return redirect()
                ->route('homepage')
                ->with('success', 'Notulen berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan notulen. ' . $e->getMessage()]);
        }
    }

    public function show(Notulen $notulen)
    {
        return view('notulen.show', compact('notulen'));
    }

    public function printPdf(Notulen $notulen)
    {
        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('notulen.print-pdf', compact('notulen'));

        // Set paper size to A4
        $pdf->setPaper('A4');

        // Return the PDF for download with a meaningful filename
        return $pdf->stream("notulen-{$notulen->format_nomor}.pdf");
    }
}
