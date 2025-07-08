<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use App\Models\NotulenAttendance;
use App\Models\NotulenDocumentation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NotulenController extends Controller
{
    public function form()
    {
        $nextNomorUrut = Notulen::max('nomor_urut') + 1;

        // Get initial notulen data for the search tab
        $notulen = Notulen::latest()->paginate(10);

        return view('notulen.form', compact('nextNomorUrut', 'notulen'));
    }

    public function search(Request $request)
    {
        $query = Notulen::query();

        // Text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('format_nomor', 'like', "%{$search}%")
                  ->orWhere('agenda', 'like', "%{$search}%")
                  ->orWhere('tempat', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%")
                  ->orWhere('bidang', 'like', "%{$search}%")
                  ->orWhere('sub_bidang', 'like', "%{$search}%");
            });
        }

        // Only apply filters if they have non-empty values
        if ($request->filled('unit') && $request->unit !== '') {
            $query->where('unit', $request->unit);
        }

        if ($request->filled('bidang') && $request->bidang !== '') {
            $query->where('bidang', $request->bidang);
        }

        if ($request->filled('tahun') && $request->tahun !== '') {
            $query->where('tahun', $request->tahun);
        }

        $notulen = $query->latest()->paginate(10);

        if ($request->ajax()) {
            $view = view('notulen._search_results', compact('notulen'))->render();
            return response()->json([
                'success' => true,
                'html' => $view,
                'hasMorePages' => $notulen->hasMorePages()
            ]);
        }

        return view('notulen._search_results', compact('notulen'));
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

            // Update notulen_id for attendances and documentations if temp_notulen_id exists
            if (isset($validated['temp_notulen_id'])) {
                // Get cached attendance data
                $cachedAttendances = Cache::get("notulen_attendances_{$validated['temp_notulen_id']}", []);
                if (!empty($cachedAttendances)) {
                    $attendanceSessionIds = collect($cachedAttendances)->pluck('session_id')->toArray();
                    NotulenAttendance::whereIn('session_id', $attendanceSessionIds)
                        ->update(['notulen_id' => $notulen->id]);
                }

                // Get cached documentation data
                $cachedDocumentations = Cache::get("notulen_documentations_{$validated['temp_notulen_id']}", []);
                if (!empty($cachedDocumentations)) {
                    $documentationSessionIds = collect($cachedDocumentations)->pluck('session_id')->toArray();
                    NotulenDocumentation::whereIn('session_id', $documentationSessionIds)
                        ->update(['notulen_id' => $notulen->id]);
                }

                // Clear the temporary data from cache
                Cache::forget("notulen_attendances_{$validated['temp_notulen_id']}");
                Cache::forget("notulen_documentations_{$validated['temp_notulen_id']}");
            }

            DB::commit();

            // Redirect to show view with success message
            return redirect()
                ->route('notulen.show', $notulen->id)
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
        // Load the documentations relationship
        $notulen->load(['documentations', 'attendances']);
        return view('notulen.show', compact('notulen'));
    }

    public function printPdf(Notulen $notulen)
    {
        // Load the documentations relationship
        $notulen->load(['documentations', 'attendances']);

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('notulen.print-pdf', compact('notulen'));

        // Set paper size to A4
        $pdf->setPaper('A4');

        // Return the PDF for download with a meaningful filename
        return $pdf->stream("notulen-{$notulen->format_nomor}.pdf");
    }
}
