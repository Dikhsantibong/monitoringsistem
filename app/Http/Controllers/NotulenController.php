<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'documentation_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
                'temp_token' => 'nullable|string'
            ]);

            // Handle documentation images
            $images = [];
            if ($request->hasFile('documentation_images')) {
                foreach ($request->file('documentation_images') as $image) {
                    $path = $image->store('notulen/documentation', 'public');
                    $images[] = $path;
                }
            }

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
                'pimpinan_rapat' => $validated['pimpinan_rapat_nama'],
                'documentation_images' => json_encode($images)
            ]);

            // Jika ada temporary token, pindahkan data absensi dari session ke database
            if (isset($validated['temp_token'])) {
                $tempAttendances = session()->get('temp_attendances', []);
                if (isset($tempAttendances[$validated['temp_token']])) {
                    foreach ($tempAttendances[$validated['temp_token']] as $attendance) {
                        DB::table('notulen_attendances')->insert([
                            'notulen_id' => $notulen->id,
                            'name' => $attendance['name'],
                            'position' => $attendance['position'],
                            'division' => $attendance['division'],
                            'signature' => $attendance['signature'],
                            'time' => $attendance['time'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    // Hapus data temporary dari session
                    unset($tempAttendances[$validated['temp_token']]);
                    session()->put('temp_attendances', $tempAttendances);
                }
            }

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

    public function generateQRCode($id)
    {
        try {
            $notulen = Notulen::findOrFail($id);

            // Generate token
            $token = 'NOT-' . strtoupper(Str::random(8));

            // Save token to notulen
            $notulen->update([
                'attendance_token' => $token,
                'attendance_token_expires_at' => now()->addHours(24)
            ]);

            // URL untuk QR
            $qrUrl = url("/notulen/attendance/scan/{$token}");

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

    public function scanAttendance($token)
    {
        try {
            // Cek apakah token temporary atau permanent
            $isTemp = str_starts_with($token, 'TEMP-');

            if ($isTemp) {
                // Untuk token temporary, tampilkan form absensi dengan data sementara
                return view('notulen.scan-attendance', [
                    'token' => $token,
                    'isTemporary' => true
                ]);
            }

            // Untuk token permanent, cari notulen terkait
            $notulen = Notulen::where('attendance_token', $token)
                ->where('attendance_token_expires_at', '>=', now())
                ->firstOrFail();

            return view('notulen.scan-attendance', [
                'token' => $token,
                'notulen' => $notulen,
                'isTemporary' => false
            ]);
        } catch (\Exception $e) {
            return redirect()->route('notulen.attendance.error')
                ->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
        }
    }

    public function storeAttendance(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'token' => 'required|string',
                'signature' => 'required|string'
            ]);

            // Cek apakah token temporary atau permanent
            $isTemp = str_starts_with($validated['token'], 'TEMP-');

            if ($isTemp) {
                // Simpan data absensi sementara ke session
                $tempAttendances = session()->get('temp_attendances', []);
                $tempAttendances[$validated['token']][] = [
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                    'division' => $validated['division'],
                    'signature' => $validated['signature'],
                    'time' => now()
                ];
                session()->put('temp_attendances', $tempAttendances);
            } else {
                // Gunakan logika yang sudah ada untuk token permanent
                $notulen = Notulen::where('attendance_token', $validated['token'])
                    ->where('attendance_token_expires_at', '>=', now())
                    ->firstOrFail();

                DB::table('notulen_attendances')->insert([
                    'notulen_id' => $notulen->id,
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                    'division' => $validated['division'],
                    'signature' => $validated['signature'],
                    'time' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

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

    public function attendanceSuccess()
    {
        return view('notulen.attendance-success');
    }

    public function attendanceError()
    {
        return view('notulen.attendance-error');
    }
}
