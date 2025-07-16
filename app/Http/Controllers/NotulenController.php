<?php

namespace App\Http\Controllers;

use App\Models\Notulen;
use App\Models\NotulenAttendance;
use App\Models\NotulenDocumentation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\DraftNotulen;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class NotulenController extends Controller
{
    use AuthorizesRequests;

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
        $data = [
            'nomor_urut' => $request->nomor_urut,
            'unit' => $request->unit,
            'bidang' => $request->bidang,
            'sub_bidang' => $request->sub_bidang,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun
        ];

        // If temp_notulen_id exists, load draft data
        if ($request->has('temp_notulen_id')) {
            $draft = DraftNotulen::where('temp_notulen_id', $request->temp_notulen_id)->first();
            if ($draft) {
                $data = array_merge($data, $draft->toArray());
            }
        }

        return view('notulen.create', $data);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get the next ID that will be used
            $nextId = DB::table('notulens')->max('id') + 1;

            // If temp_notulen_id exists, get the draft data
            $draftData = [];
            if ($request->filled('temp_notulen_id')) {
                $draft = DraftNotulen::where('temp_notulen_id', $request->temp_notulen_id)->first();
                if ($draft) {
                    $draftData = $draft->toArray();
                    // Remove any null values from draft data
                    $draftData = array_filter($draftData, function($value) {
                        return !is_null($value);
                    });
                }
            }

            // Create request data array
            $requestData = $request->all();

            // Remove empty strings from request data
            $requestData = array_filter($requestData, function($value) {
                return $value !== '';
            });

            // Merge request data with draft data, prioritizing request data
            $mergedData = array_merge($draftData, $requestData);

            // Validate the merged data
            $validated = validator($mergedData, [
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
            ])->validate();

            // Set nomor_urut to match the next ID
            $validated['nomor_urut'] = $nextId;

            // Sanitize HTML content but preserve basic formatting
            $validated['pembahasan'] = strip_tags($validated['pembahasan'], '<p><br><ul><ol><li><strong><em><u><s>');
            $validated['tindak_lanjut'] = strip_tags($validated['tindak_lanjut'], '<p><br><ul><ol><li><strong><em><u><s>');

            // Generate format nomor using the next ID as nomor_urut
            $formatNomor = Notulen::generateFormatNomor(
                $nextId,
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
                'created_by' => Auth::id()
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

                // Get cached file data
                $cachedFiles = Cache::get("notulen_files_{$validated['temp_notulen_id']}", []);
                if (!empty($cachedFiles)) {
                    $fileSessionIds = collect($cachedFiles)->pluck('session_id')->toArray();
                    \App\Models\NotulenFile::whereIn('session_id', $fileSessionIds)
                        ->update(['notulen_id' => $notulen->id]);
                }

                // Clear the temporary data from cache
                Cache::forget("notulen_attendances_{$validated['temp_notulen_id']}");
                Cache::forget("notulen_documentations_{$validated['temp_notulen_id']}");
                Cache::forget("notulen_files_{$validated['temp_notulen_id']}");

                // Delete the draft after successful creation
                if ($draft = DraftNotulen::where('temp_notulen_id', $validated['temp_notulen_id'])->first()) {
                    $draft->delete();
                }
            }

            DB::commit();

            // Check if request wants JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notulen berhasil disimpan',
                    'redirect_url' => route('notulen.show', $notulen->id)
                ]);
            }

            // Regular response for non-AJAX requests
            return redirect()
                ->route('notulen.show', $notulen->id)
                ->with('success', 'Notulen berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating notulen: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan notulen: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan notulen. ' . $e->getMessage()]);
        }
    }
    public function show(Notulen $notulen)
    {
        // Load the documentations, files, and attendances relationship
        $notulen->load(['documentations', 'attendances', 'files']);
        return view('notulen.show', compact('notulen'));
    }
    /**
     * Print the notulen as PDF
     */
    public function printPdf(Notulen $notulen)
    {
        $notulen->load(['attendances', 'documentations', 'files']);
        
        return view('notulen.print-pdf', compact('notulen'))
            ->with([
                'title' => 'Notulen Rapat - ' . $notulen->agenda,
                'print_mode' => true
            ]);
    }

    public function downloadZip(Notulen $notulen)
    {
        // Generate PDF notulen
        $notulen->load(['documentations', 'attendances', 'files']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('notulen.print-pdf', compact('notulen'));
        $pdfPath = storage_path('app/public/notulen-pdf/notulen-' . $notulen->id . '.pdf');
        if (!file_exists(dirname($pdfPath))) {
            mkdir(dirname($pdfPath), 0777, true);
        }
        $pdf->save($pdfPath);

        // Siapkan file lampiran
        $files = [];
        foreach ($notulen->files as $file) {
            $realPath = Storage::disk('public')->path($file->file_path);
            if (file_exists($realPath)) {
                $files[] = [
                    'path' => $realPath,
                    'name' => 'lampiran/' . $file->file_name
                ];
            }
        }

        // Buat ZIP
        $zipName = 'notulen-' . $notulen->id . '-with-lampiran.zip';
        $zipPath = storage_path('app/public/notulen-pdf/' . $zipName);
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Tambahkan PDF utama
            $zip->addFile($pdfPath, 'notulen.pdf');
            // Tambahkan file lampiran
            foreach ($files as $f) {
                $zip->addFile($f['path'], $f['name']);
            }
            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat ZIP');
        }

        // Kirim ZIP ke user
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Show the form for editing the specified notulen.
     */
    public function edit(Notulen $notulen)
    {
        // Load the attendances relationship
        $notulen->load(['attendances' => function($query) {
            $query->orderBy('attended_at', 'asc');
        }]);
        
        return view('notulen.edit', compact('notulen'));
    }

    /**
     * Update the specified notulen in storage.
     */
    public function update(Request $request, Notulen $notulen)
    {
        try {
            DB::beginTransaction();

            // Validate the request
            $validated = $request->validate([
                'tempat' => 'required|string',
                'agenda' => 'required|string',
                'peserta' => 'required|string',
                'tanggal' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'pembahasan' => 'required|string',
                'tindak_lanjut' => 'required|string',
                'pimpinan_rapat_nama' => 'required|string',
                'notulis_nama' => 'required|string',
                'tanggal_tanda_tangan' => 'required|date',
                'revision_reason' => 'required|string|max:255'
            ]);

            // Get the old values before update
            $oldValues = $notulen->toArray();

            // Update the notulen
            $notulen->tempat = $validated['tempat'];
            $notulen->agenda = $validated['agenda'];
            $notulen->peserta = $validated['peserta'];
            $notulen->tanggal = $validated['tanggal'];
            $notulen->waktu_mulai = $validated['waktu_mulai'] . ':00';
            $notulen->waktu_selesai = $validated['waktu_selesai'] . ':00';
            $notulen->pembahasan = strip_tags($validated['pembahasan'], '<p><br><ul><ol><li><strong><em><u><s>');
            $notulen->tindak_lanjut = strip_tags($validated['tindak_lanjut'], '<p><br><ul><ol><li><strong><em><u><s>');
            $notulen->pimpinan_rapat = $validated['pimpinan_rapat_nama'];
            $notulen->pimpinan_rapat_nama = $validated['pimpinan_rapat_nama'];
            $notulen->notulis_nama = $validated['notulis_nama'];
            $notulen->tanggal_tanda_tangan = $validated['tanggal_tanda_tangan'];

            // Save the changes
            $notulen->save();

            // Track changes for revision history
            $changes = [];
            foreach ($validated as $field => $newValue) {
                if ($field !== 'revision_reason' && isset($oldValues[$field])) {
                    // Format time fields for comparison
                    if (in_array($field, ['waktu_mulai', 'waktu_selesai'])) {
                        $oldValue = Carbon::parse($oldValues[$field])->format('H:i');
                        $newValue = $newValue;
                    } else {
                        $oldValue = $oldValues[$field];
                    }

                    if ($oldValue !== $newValue) {
                        $changes[$field] = [
                            'old' => $oldValue,
                            'new' => $newValue
                        ];
                    }
                }
            }

            // Record the revision if there are changes
            if (!empty($changes)) {
                $notulen->trackRevision(
                    Auth::id() ?? 1,
                    $changes,
                    $validated['revision_reason']
                );

                // Increment revision count
                $notulen->increment('revision_count');
            }

            DB::commit();

            return redirect()
                ->route('notulen.show', $notulen->id)
                ->with('success', 'Notulen berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating notulen: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui notulen: ' . $e->getMessage());
        }
    }
}
