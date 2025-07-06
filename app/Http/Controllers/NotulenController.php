<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
        $notulen = Notulen::create([
            'nomor_urut' => $request->nomor_urut,
            'unit' => $request->unit,
            'bidang' => $request->bidang,
            'sub_bidang' => $request->sub_bidang,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'format_nomor' => Notulen::generateFormatNomor(
                $request->nomor_urut,
                $request->unit,
                $request->bidang,
                $request->sub_bidang,
                $request->bulan,
                $request->tahun
            ),
            'pembahasan' => '', // Default empty string
            'tindak_lanjut' => '', // Default empty string
            'agenda' => '', // Default empty string
            'peserta' => '', // Default empty string
            'tempat' => '', // Default empty string
            'tanggal' => now(), // Default current date
            'waktu_mulai' => now()->format('H:i:s'), // Default current time
            'waktu_selesai' => now()->format('H:i:s'), // Default current time
            'pimpinan_rapat_nama' => '', // Default empty string
            'notulis_nama' => '' // Default empty string
        ]);

        return view('notulen.create', [
            'notulen' => $notulen,
            'notulenId' => $notulen->id,
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
                'tanggal_tanda_tangan' => 'required|date'
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

            // Redirect to show view with success message
            return redirect()
                ->route('notulen.show', $notulen->id)
                ->with('success', 'Notulen berhasil disimpan');

        } catch (\Exception $e) {
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

    public function generateAttendanceQR(Notulen $notulen)
    {
        try {
            // Generate token
            $token = 'NOT-' . strtoupper(Str::random(8));

            // Update notulen with token
            $notulen->update([
                'attendance_token' => $token,
                'attendance_token_expires_at' => now()->addHours(24)
            ]);

            // URL untuk QR
            $qrUrl = url("/notulen/{$notulen->id}/attendance/scan/{$token}");

            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code'
            ], 500);
        }
    }

    public function scanAttendance(Notulen $notulen, $token)
    {
        try {
            if ($notulen->attendance_token !== $token ||
                $notulen->attendance_token_expires_at < now()) {
                return redirect()->back()
                    ->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
            }

            return view('notulen.scan-attendance', compact('notulen', 'token'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses QR Code');
        }
    }

    public function storeAttendance(Request $request, Notulen $notulen)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'token' => 'required|string',
                'signature' => 'required|string'
            ]);

            if ($notulen->attendance_token !== $validated['token'] ||
                $notulen->attendance_token_expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah kadaluarsa'
                ], 422);
            }

            // Store attendance
            DB::table('notulen_attendances')->insert([
                'notulen_id' => $notulen->id,
                'name' => $validated['name'],
                'position' => $validated['position'],
                'division' => $validated['division'],
                'signature' => $validated['signature'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendances(Notulen $notulen)
    {
        $attendances = DB::table('notulen_attendances')
            ->where('notulen_id', $notulen->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
}
