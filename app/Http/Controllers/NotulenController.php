<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

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
            // Validasi token
            if (!$token) {
                return redirect()->route('notulen.attendance.error')->with('error', 'Token tidak valid');
            }

            // Ambil data dari session jika token sesuai
            $tempData = session('notulen_temp_data');
            $tempToken = session('notulen_temp_token');

            if (!$tempData || !$tempToken || $tempToken !== $token) {
                return redirect()->route('notulen.attendance.error')->with('error', 'Data tidak ditemukan atau token tidak valid');
            }

            return view('notulen.scan-attendance', compact('token', 'tempData'));
        } catch (\Exception $e) {
            \Log::error('Error in scanAttendance: ' . $e->getMessage());
            return redirect()->route('notulen.attendance.error')->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function storeAttendance(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required',
                'name' => 'required|string',
                'position' => 'required|string',
                'division' => 'required|string',
                'signature' => 'required|string'
            ]);

            // Simpan data kehadiran
            $attendance = new Attendance();
            $attendance->notulen_id = session('current_notulen_id');
            $attendance->name = $validated['name'];
            $attendance->position = $validated['position'];
            $attendance->division = $validated['division'];
            $attendance->signature = $validated['signature'];
            $attendance->time = now();
            $attendance->save();

            return redirect()->route('notulen.attendance.success');
        } catch (\Exception $e) {
            \Log::error('Error in storeAttendance: ' . $e->getMessage());
            return redirect()->route('notulen.attendance.error')->with('error', 'Terjadi kesalahan saat menyimpan data');
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
