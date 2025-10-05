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
        // Initialize data array with request parameters
        $data = [
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
                // Merge draft data, prioritizing non-null draft values over request values
                foreach ($data as $key => $value) {
                    if (!is_null($draft->$key)) {
                        $data[$key] = $draft->$key;
                    }
                }
                // Merge other draft data
                $data = array_merge($data, array_filter($draft->toArray(), function($value) {
                    return !is_null($value);
                }));
            }
        }

        // Ensure all required fields are present
        if (empty($data['unit']) || empty($data['bidang']) || empty($data['sub_bidang']) || 
            empty($data['bulan']) || empty($data['tahun'])) {
            return redirect()->route('notulen.form')
                ->with('error', 'Data notulen tidak lengkap. Silakan isi form kembali.');
        }

        return view('notulen.create', $data);
    }

    public function uploadPastedImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|string',
                'temp_notulen_id' => 'required|string'
            ]);

            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
            
            // Generate unique filename
            $filename = 'pasted-' . uniqid() . '.png';
            
            // Create directory if it doesn't exist
            $directory = 'notulen-images/' . $request->temp_notulen_id;
            $fullPath = storage_path('app/public/' . $directory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
            
            // Store the image in public directory
            file_put_contents($fullPath . '/' . $filename, $imageData);

            // Return the image URL using asset helper
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $directory . '/' . $filename)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
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
                    $draftData = array_filter($draft->toArray(), function($value) {
                        return !is_null($value);
                    });
                }
            }

            // Create request data array, filtering out empty strings
            $requestData = array_filter($request->all(), function($value) {
                return $value !== '' && $value !== null;
            });

            // Merge request data with draft data, prioritizing request data
            $mergedData = array_merge($draftData, $requestData);

            // Ensure required fields are present
            if (empty($mergedData['unit']) || empty($mergedData['bidang']) || 
                empty($mergedData['sub_bidang']) || empty($mergedData['bulan']) || 
                empty($mergedData['tahun'])) {
                throw new \Exception('Data notulen tidak lengkap. Pastikan unit, bidang, sub_bidang, bulan, dan tahun terisi.');
            }

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

            // Sanitize HTML content but preserve basic formatting and images
            $allowedTags = '<p><br><ul><ol><li><strong><em><u><s><img>';
            $validated['pembahasan'] = strip_tags($validated['pembahasan'], $allowedTags);
            $validated['tindak_lanjut'] = strip_tags($validated['tindak_lanjut'], $allowedTags);

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

            // Move pasted images from temp folder to permanent folder
            if (isset($validated['temp_notulen_id'])) {
                $tempPath = storage_path('app/public/notulen-images/' . $validated['temp_notulen_id']);
                $permanentPath = storage_path('app/public/notulen-images/' . $notulen->id);
                
                if (file_exists($tempPath)) {
                    // Create permanent directory if it doesn't exist
                    if (!file_exists($permanentPath)) {
                        mkdir($permanentPath, 0755, true);
                    }

                    // Move all files from temp to permanent directory
                    $files = glob($tempPath . '/*');
                    foreach ($files as $file) {
                        $newPath = str_replace($tempPath, $permanentPath, $file);
                        rename($file, $newPath);
                        
                        // Update image URLs in content
                        $oldUrl = asset('storage/notulen-images/' . $validated['temp_notulen_id'] . '/' . basename($file));
                        $newUrl = asset('storage/notulen-images/' . $notulen->id . '/' . basename($file));
                        $validated['pembahasan'] = str_replace($oldUrl, $newUrl, $validated['pembahasan']);
                        $validated['tindak_lanjut'] = str_replace($oldUrl, $newUrl, $validated['tindak_lanjut']);
                    }

                    // Remove temp directory
                    rmdir($tempPath);
                }
                
                // Update notulen with new image URLs
                $notulen->update([
                    'pembahasan' => $validated['pembahasan'],
                    'tindak_lanjut' => $validated['tindak_lanjut']
                ]);
            }

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
        try {
            $notulen->load(['attendances', 'documentations', 'files']);

            // Generate main notulen PDF
            $mainPdf = PDF::loadView('notulen.print-pdf', [
                'notulen' => $notulen,
                'title' => 'Notulen Rapat - ' . $notulen->format_nomor,
                'print_mode' => true
            ]);

            // Save main PDF temporarily
            $mainPdfPath = storage_path('app/temp/notulen-' . $notulen->id . '.pdf');
            if (!file_exists(dirname($mainPdfPath))) {
                mkdir(dirname($mainPdfPath), 0777, true);
            }
            $mainPdf->save($mainPdfPath);

            // Initialize PDF merger
            $merger = new \iio\libmergepdf\Merger();
            $merger->addFile($mainPdfPath);

            // Add uploaded PDF files
            foreach ($notulen->files as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                
                // Skip if file doesn't exist
                if (!file_exists($filePath)) {
                    continue;
                }

                // Handle different file types
                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                
                if ($extension === 'pdf') {
                    // Add PDF directly
                    $merger->addFile($filePath);
                } else if (in_array($extension, ['doc', 'docx'])) {
                    // Convert Word to PDF using LibreOffice if available
                    $tempPdfPath = storage_path('app/temp/' . basename($filePath) . '.pdf');
                    
                    // Use soffice command to convert
                    $cmd = "soffice --headless --convert-to pdf --outdir " . 
                           escapeshellarg(dirname($tempPdfPath)) . " " . 
                           escapeshellarg($filePath);
                    
                    exec($cmd, $output, $returnVar);
                    
                    if ($returnVar === 0 && file_exists($tempPdfPath)) {
                        $merger->addFile($tempPdfPath);
                    }
                }
            }

            // Create merged PDF
            $mergedPdfContent = $merger->merge();

            // Clean up temporary files
            @unlink($mainPdfPath);
            $tempFiles = glob(storage_path('app/temp/*'));
            foreach ($tempFiles as $tempFile) {
                @unlink($tempFile);
            }
            @rmdir(storage_path('app/temp'));

            // Return the merged PDF
            return response($mergedPdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="notulen-' . $notulen->id . '.pdf"');

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage());
        }
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

            $oldValues = $notulen->toArray();

            // Allow <img> tags in HTML
            $allowedTags = '<p><br><ul><ol><li><strong><em><u><s><img>';
            $validated['pembahasan'] = strip_tags($validated['pembahasan'], $allowedTags);
            $validated['tindak_lanjut'] = strip_tags($validated['tindak_lanjut'], $allowedTags);

            // Move pasted images from temp folder to permanent folder (if any)
            $tempEditId = 'edit-' . $notulen->id;
            $tempPath = storage_path('app/public/notulen-images/' . $tempEditId);
            $permanentPath = storage_path('app/public/notulen-images/' . $notulen->id);
            $updatedPembahasan = $validated['pembahasan'];
            $updatedTindakLanjut = $validated['tindak_lanjut'];
            if (file_exists($tempPath)) {
                if (!file_exists($permanentPath)) {
                    mkdir($permanentPath, 0755, true);
                }
                $files = glob($tempPath . '/*');
                foreach ($files as $file) {
                    $newPath = str_replace($tempPath, $permanentPath, $file);
                    rename($file, $newPath);
                    $oldUrl = asset('storage/notulen-images/' . $tempEditId . '/' . basename($file));
                    $newUrl = asset('storage/notulen-images/' . $notulen->id . '/' . basename($file));
                    $updatedPembahasan = str_replace($oldUrl, $newUrl, $updatedPembahasan);
                    $updatedTindakLanjut = str_replace($oldUrl, $newUrl, $updatedTindakLanjut);
                }
                rmdir($tempPath);
            }

            // Update the notulen
            $notulen->tempat = $validated['tempat'];
            $notulen->agenda = $validated['agenda'];
            $notulen->peserta = $validated['peserta'];
            $notulen->tanggal = $validated['tanggal'];
            $notulen->waktu_mulai = $validated['waktu_mulai'] . ':00';
            $notulen->waktu_selesai = $validated['waktu_selesai'] . ':00';
            $notulen->pembahasan = $updatedPembahasan;
            $notulen->tindak_lanjut = $updatedTindakLanjut;
            $notulen->pimpinan_rapat = $validated['pimpinan_rapat_nama'];
            $notulen->pimpinan_rapat_nama = $validated['pimpinan_rapat_nama'];
            $notulen->notulis_nama = $validated['notulis_nama'];
            $notulen->tanggal_tanda_tangan = $validated['tanggal_tanda_tangan'];
            $notulen->save();

            // Track changes for revision history
            $changes = [];
            foreach ($validated as $field => $newValue) {
                if ($field !== 'revision_reason' && isset($oldValues[$field])) {
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
            if (!empty($changes)) {
                $notulen->trackRevision(
                    Auth::id() ?? 1,
                    $changes,
                    $validated['revision_reason']
                );
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