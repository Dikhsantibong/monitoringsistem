<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class MaximoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $workOrderPage = $request->input('wo_page', 1);
            $serviceRequestPage = $request->input('sr_page', 1);
            $search = $request->input('search');
            
            // Filter untuk Work Order
            $woStatusFilter = $request->input('wo_status');
            $woWorkTypeFilter = $request->input('wo_worktype');

            /* ==========================
             * WORK ORDER (TETAP)
             * ========================== */
            $workOrdersQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                    'DOWNTIME',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD');

            if ($search) {
                $workOrdersQuery->where(function ($q) use ($search) {
                    $q->where('WONUM', 'LIKE', "%{$search}%")
                        ->orWhere('PARENT', 'LIKE', "%{$search}%")
                        ->orWhere('STATUS', 'LIKE', "%{$search}%")
                        ->orWhere('WORKTYPE', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter Status
            if ($woStatusFilter) {
                $workOrdersQuery->where('STATUS', $woStatusFilter);
            }
            
            // Filter Work Type
            if ($woWorkTypeFilter) {
                $workOrdersQuery->where('WORKTYPE', $woWorkTypeFilter);
            }

            $workOrdersQuery->orderBy('STATUSDATE', 'desc');

            $workOrders = $workOrdersQuery->paginate(10, ['*'], 'wo_page', $workOrderPage);

            /* ==========================
             * SERVICE REQUEST (BARU)
             * ========================== */
            $serviceRequestsQuery = DB::connection('oracle')
                ->table('SR')
                ->select([
                    'TICKETID',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'SITEID',
                    'LOCATION',
                    'ASSETNUM',
                    'REPORTEDBY',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD');

            if ($search) {
                $serviceRequestsQuery->where(function ($q) use ($search) {
                    $q->where('TICKETID', 'LIKE', "%{$search}%")
                        ->orWhere('DESCRIPTION', 'LIKE', "%{$search}%")
                        ->orWhere('STATUS', 'LIKE', "%{$search}%")
                        ->orWhere('SITEID', 'LIKE', "%{$search}%")
                        ->orWhere('LOCATION', 'LIKE', "%{$search}%")
                        ->orWhere('ASSETNUM', 'LIKE', "%{$search}%")
                        ->orWhere('REPORTEDBY', 'LIKE', "%{$search}%");
                });
            }

            $serviceRequestsQuery->orderBy('REPORTDATE', 'desc');

            $serviceRequests = $serviceRequestsQuery->paginate(10, ['*'], 'sr_page', $serviceRequestPage);

            return view('admin.maximo.index', [
                'workOrders'      => $this->formatWorkOrders($workOrders->items()),
                'workOrdersPaginator' => $workOrders,
                'serviceRequests' => $this->formatServiceRequests($serviceRequests->items()),
                'serviceRequestsPaginator' => $serviceRequests,
                'error'           => null,
                'errorDetail'     => null,
            ]);

        } catch (QueryException $e) {

            Log::error('ORACLE QUERY ERROR', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message'     => $e->getMessage(),
                'sql'         => $e->getSql(),
                'bindings'    => $e->getBindings(),
            ]);

            return view('admin.maximo.index', [
                'workOrders'       => collect([]),
                'workOrdersPaginator' => null,
                'serviceRequests' => collect([]),
                'serviceRequestsPaginator' => null,
                'error' => 'Gagal mengambil data dari Maximo (Query Error)',
                'errorDetail' => [
                    'oracle_code' => $e->errorInfo[1] ?? null,
                    'message' => $e->getMessage(),
                ],
            ]);

        } catch (\Throwable $e) {

            Log::error('ORACLE GENERAL ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return view('admin.maximo.index', [
                'workOrders'       => collect([]),
                'workOrdersPaginator' => null,
                'serviceRequests' => collect([]),
                'serviceRequestsPaginator' => null,
                'error' => 'Gagal mengambil data dari Maximo (General Error)',
                'errorDetail' => [
                    'message' => $e->getMessage(),
                ],
            ]);
        }
    }

    public function showWorkOrder(string $wonum)
    {
        try {
            $wonum = trim($wonum);
            if ($wonum === '') {
                return redirect()->route('admin.maximo.index')->with('error', 'WONUM tidak valid.');
            }

            $wo = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                    'DOWNTIME',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('admin.maximo.index')->with('error', 'Work Order tidak ditemukan.');
            }

            return view('admin.maximo.workorder-detail', [
                'wo' => [
                    'wonum' => $wo->wonum ?? '-',
                    'parent' => $wo->parent ?? '-',
                    'status' => $wo->status ?? '-',
                    'statusdate' => isset($wo->statusdate) && $wo->statusdate ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i') : '-',
                    'worktype' => $wo->worktype ?? '-',
                    'wopriority' => $wo->wopriority ?? '-',
                    'reportdate' => isset($wo->reportdate) && $wo->reportdate ? Carbon::parse($wo->reportdate)->format('d-m-Y H:i') : '-',
                    'assetnum' => $wo->assetnum ?? '-',
                    'location' => $wo->location ?? '-',
                    'siteid' => $wo->siteid ?? '-',
                    'downtime' => $wo->downtime ?? '-',
                    'schedstart' => isset($wo->schedstart) && $wo->schedstart ? Carbon::parse($wo->schedstart)->format('d-m-Y H:i') : '-',
                    'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish ? Carbon::parse($wo->schedfinish)->format('d-m-Y H:i') : '-',
                    'description' => $wo->description ?? '-',
                ],
            ]);

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (WO DETAIL)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message'     => $e->getMessage(),
                'sql'         => $e->getSql(),
                'bindings'    => $e->getBindings(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal mengambil detail Work Order (Query Error).');
        } catch (\Throwable $e) {
            Log::error('ORACLE GENERAL ERROR (WO DETAIL)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal mengambil detail Work Order.');
        }
    }

    public function showServiceRequest(string $ticketid)
    {
        try {
            $ticketid = trim($ticketid);
            if ($ticketid === '') {
                return redirect()->route('admin.maximo.index')->with('error', 'Ticket ID tidak valid.');
            }

            $sr = DB::connection('oracle')
                ->table('SR')
                ->select([
                    'TICKETID',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'SITEID',
                    'LOCATION',
                    'ASSETNUM',
                    'REPORTEDBY',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD')
                ->where('TICKETID', $ticketid)
                ->first();

            if (!$sr) {
                return redirect()->route('admin.maximo.index')->with('error', 'Service Request tidak ditemukan.');
            }

            return view('admin.maximo.service-request-detail', [
                'sr' => [
                    'ticketid' => $sr->ticketid ?? '-',
                    'status' => $sr->status ?? '-',
                    'statusdate' => isset($sr->statusdate) && $sr->statusdate ? Carbon::parse($sr->statusdate)->format('d-m-Y H:i') : '-',
                    'reportedby' => $sr->reportedby ?? '-',
                    'reportdate' => isset($sr->reportdate) && $sr->reportdate ? Carbon::parse($sr->reportdate)->format('d-m-Y H:i') : '-',
                    'siteid' => $sr->siteid ?? '-',
                    'location' => $sr->location ?? '-',
                    'assetnum' => $sr->assetnum ?? '-',
                    'description' => $sr->description ?? '-',
                ],
            ]);

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (SR DETAIL)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message'     => $e->getMessage(),
                'sql'         => $e->getSql(),
                'bindings'    => $e->getBindings(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal mengambil detail Service Request (Query Error).');
        } catch (\Throwable $e) {
            Log::error('ORACLE GENERAL ERROR (SR DETAIL)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal mengambil detail Service Request.');
        }
    }

    /* ==========================
     * FORMAT WORK ORDER
     * ========================== */
    private function formatWorkOrders($workOrders)
    {
        return collect($workOrders)->map(function ($wo) {
            // Pastikan WONUM di-trim untuk menghilangkan spasi
            $wonum = isset($wo->wonum) ? trim($wo->wonum) : null;
            
            // Cek apakah file jobcard ada di storage
            $jobcardExists = false;
            $jobcardPath = null;
            $jobcardUrl = null;
            
            // Pastikan WONUM valid dan tidak kosong
            if ($wonum && $wonum !== '' && $wonum !== '-') {
                // Format file path sama persis dengan saat generate
                $directory = 'jobcards';
                $filename = 'JOBCARD_' . $wonum . '.pdf';
                $filePath = $directory . '/' . $filename;
                
                // Cek apakah file ada di storage
                if (Storage::disk('public')->exists($filePath)) {
                    $jobcardExists = true;
                    $jobcardPath = $filePath;
                    $jobcardUrl = asset('storage/' . $filePath);
                }
            }
            
            return [
                'wonum'       => $wonum ?? '-',
                'parent'      => $wo->parent ?? '-',
                'status'      => $wo->status ?? '-',
                'statusdate'  => isset($wo->statusdate) && $wo->statusdate
                    ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i')
                    : '-',
                'worktype'    => $wo->worktype ?? '-',
                'description' => $wo->description ?? '-',
                'reportdate'  => isset($wo->reportdate) && $wo->reportdate
                    ? Carbon::parse($wo->reportdate)->format('d-m-Y H:i')
                    : '-',
                'assetnum'    => $wo->assetnum ?? '-',
                'wopriority'  => $wo->wopriority ?? '-',
                'location'    => $wo->location ?? '-',
                'siteid'      => $wo->siteid ?? '-',
                'downtime'    => $wo->downtime ?? '-',
                'schedstart'  => isset($wo->schedstart) && $wo->schedstart
                    ? Carbon::parse($wo->schedstart)->format('d-m-Y H:i')
                    : '-',
                'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish
                    ? Carbon::parse($wo->schedfinish)->format('d-m-Y H:i')
                    : '-',
                'jobcard_exists' => $jobcardExists,
                'jobcard_path' => $jobcardPath,
                'jobcard_url' => $jobcardUrl,
            ];
        });
    }

    /* ==========================
     * FORMAT SERVICE REQUEST
     * ========================== */
    private function formatServiceRequests($serviceRequests)
    {
        return collect($serviceRequests)->map(function ($sr) {
            return [
                'ticketid'    => $sr->ticketid ?? '-',
                'description' => $sr->description ?? '-',
                'status'      => $sr->status ?? '-',
                'statusdate'  => isset($sr->statusdate) && $sr->statusdate
                    ? Carbon::parse($sr->statusdate)->format('d-m-Y H:i')
                    : '-',
                'siteid'      => $sr->siteid ?? '-',
                'location'    => $sr->location ?? '-',
                'assetnum'    => $sr->assetnum ?? '-',
                'reportedby'  => $sr->reportedby ?? '-',
                'reportdate'  => isset($sr->reportdate) && $sr->reportdate
                    ? Carbon::parse($sr->reportdate)->format('d-m-Y H:i')
                    : '-',
            ];
        });
    }

    public function generateJobcard(Request $request)
    {
        try {
            // Pastikan WONUM di-trim untuk konsistensi
            $wonum = trim($request->input('wonum'));
            
            if (!$wonum || $wonum === '') {
                return redirect()->route('admin.maximo.index')->with('error', 'WONUM tidak valid.');
            }

            // Ambil data Work Order dari Maximo
            $wo = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'PARENT',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORITY',
                    'DESCRIPTION',
                    'ASSETNUM',
                    'LOCATION',
                    'SITEID',
                    'DOWNTIME',
                    'SCHEDSTART',
                    'SCHEDFINISH',
                    'REPORTDATE',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('maximo.index')->with('error', 'Work Order tidak ditemukan.');
            }

            // Cek apakah status adalah APPR
            if (strtoupper($wo->status ?? '') !== 'APPR') {
                return redirect()->route('maximo.index')->with('error', 'Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.');
            }

            // Format data untuk PDF
            $woData = [
                'wonum' => $wo->wonum ?? '-',
                'parent' => $wo->parent ?? '-',
                'status' => $wo->status ?? '-',
                'statusdate' => isset($wo->statusdate) && $wo->statusdate ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i') : '-',
                'worktype' => $wo->worktype ?? '-',
                'wopriority' => $wo->wopriority ?? '-',
                'reportdate' => isset($wo->reportdate) && $wo->reportdate ? Carbon::parse($wo->reportdate)->format('d-m-Y H:i') : '-',
                'assetnum' => $wo->assetnum ?? '-',
                'location' => $wo->location ?? '-',
                'siteid' => $wo->siteid ?? '-',
                'downtime' => $wo->downtime ?? '-',
                'schedstart' => isset($wo->schedstart) && $wo->schedstart ? Carbon::parse($wo->schedstart)->format('d-m-Y H:i') : '-',
                'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish ? Carbon::parse($wo->schedfinish)->format('d-m-Y H:i') : '-',
                'description' => $wo->description ?? '-',
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', ['wo' => $woData]);

            // Simpan PDF ke storage public dengan nama deterministik (tanpa DB tambahan)
            // 1 WO = 1 file jobcard yang selalu di-overwrite
            $directory = 'jobcards';
            $filename = 'JOBCARD_' . $wonum . '.pdf';
            $filePath = $directory . '/' . $filename;
            
            // Pastikan directory ada
            Storage::disk('public')->makeDirectory($directory);
            
            // Simpan / overwrite PDF
            Storage::disk('public')->put($filePath, $pdf->output());

            // Redirect dengan success message dan link untuk membuka PDF di PDF.js viewer
            $pdfUrl = asset('storage/' . $filePath);

            return redirect()->route('admin.maximo.index')
                ->with('success', 'Jobcard berhasil di-generate!')
                ->with('jobcard_url', $pdfUrl)
                ->with('jobcard_path', $filePath)
                ->with('jobcard_wonum', $wonum);

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (GENERATE JOBCARD)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal mengambil data Work Order untuk generate jobcard.');
        } catch (\Throwable $e) {
            Log::error('ERROR GENERATE JOBCARD', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal generate jobcard: ' . $e->getMessage());
        }
    }

    public function downloadJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                return redirect()->route('admin.maximo.index')->with('error', 'File jobcard tidak ditemukan.');
            }

            return Storage::disk('public')->download($filePath);
        } catch (\Throwable $e) {
            Log::error('ERROR DOWNLOAD JOBCARD', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal download jobcard.');
        }
    }

    public function updateJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                return response()->json(['success' => false, 'message' => 'Path tidak valid atau file tidak ditemukan.']);
            }

            $drawingBase64 = $request->input('drawing');
            
            if (!$drawingBase64) {
                return response()->json(['success' => false, 'message' => 'Tidak ada gambar drawing yang dikirim.']);
            }

            // Path file PDF asli
            $originalPdfPath = Storage::disk('public')->path($filePath);
            
            if (!file_exists($originalPdfPath)) {
                return response()->json(['success' => false, 'message' => 'File PDF asli tidak ditemukan.']);
            }
            
            // Buat temporary directory jika belum ada
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            // Decode base64 drawing image
            $tempImagePath = null;
            if (preg_match('/data:image\/(\w+);base64,(.+)/', $drawingBase64, $matches)) {
                $imageType = strtolower($matches[1]);
                $imageData = base64_decode($matches[2]);
                
                // Simpan temporary image
                $tempImagePath = $tempDir . '/drawing_' . time() . '.' . $imageType;
                file_put_contents($tempImagePath, $imageData);
            } else {
                return response()->json(['success' => false, 'message' => 'Format gambar drawing tidak valid.']);
            }
            
            // Buat PDF baru dengan FPDI
            $pdf = new Fpdi();
            
            // Import halaman dari PDF asli
            $pageCount = $pdf->setSourceFile($originalPdfPath);
            
            // Dapatkan informasi gambar drawing untuk scaling
            $drawingImageInfo = null;
            $firstPageSize = null;
            $croppedImagePath = null; // Deklarasi di scope yang benar untuk cleanup
            
            if (file_exists($tempImagePath) && in_array($imageType, ['png', 'jpg', 'jpeg'])) {
                // Dapatkan dimensi gambar drawing
                $drawingImageInfo = @getimagesize($tempImagePath);
            }
            
            // Loop setiap halaman dan tambahkan gambar drawing overlay HANYA di halaman pertama
            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                // Import halaman dari PDF asli
                $templateId = $pdf->importPage($pageNum);
                $size = $pdf->getTemplateSize($templateId);
                
                // Simpan ukuran halaman pertama untuk referensi
                if ($pageNum === 1) {
                    $firstPageSize = $size;
                }
                
                // Tambahkan halaman baru dengan orientasi yang sama
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                
                // Render halaman PDF asli TERLEBIH DAHULU (PDF tetap utuh)
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
                
                // HANYA tambahkan gambar drawing overlay pada HALAMAN PERTAMA saja
                // Halaman lainnya hanya render PDF asli tanpa overlay
                if ($pageNum === 1 && $drawingImageInfo && file_exists($tempImagePath) && $firstPageSize) {
                    // Dapatkan dimensi gambar drawing
                    $drawingWidth = $drawingImageInfo[0];
                    $drawingHeight = $drawingImageInfo[1];
                    
                    // Potong gambar drawing hanya untuk area halaman pertama
                    // Buat gambar baru yang hanya berisi area halaman pertama
                    $pageHeight = $firstPageSize['height'];
                    $pageWidth = $firstPageSize['width'];
                    
                    // Hitung rasio scaling antara canvas drawing dan halaman PDF
                    // Canvas drawing biasanya lebih tinggi karena mencakup semua halaman
                    // Kita perlu memotong hanya bagian halaman pertama
                    $scaleRatio = $pageWidth / $drawingWidth;
                    $scaledPageHeight = $drawingHeight * $scaleRatio;
                    
                    // SELALU potong gambar hanya untuk tinggi halaman pertama
                    // Hitung tinggi gambar yang perlu diambil (dalam pixel gambar asli)
                    $imageHeightToUse = ($pageHeight / $scaleRatio);
                    
                    // Pastikan tidak melebihi tinggi gambar asli
                    $imageHeightToUse = min((int)$imageHeightToUse, $drawingHeight);
                    
                    // Buat gambar baru yang dipotong hanya untuk halaman pertama
                    $croppedImagePath = $tempDir . '/drawing_cropped_' . time() . '.' . $imageType;
                    
                    if ($imageType === 'png') {
                        $sourceImage = imagecreatefrompng($tempImagePath);
                    } else {
                        $sourceImage = imagecreatefromjpeg($tempImagePath);
                    }
                    
                    if ($sourceImage) {
                        // Buat gambar baru dengan ukuran lebar penuh, tinggi hanya halaman pertama
                        $croppedImage = imagecreatetruecolor($drawingWidth, $imageHeightToUse);
                        
                        // Enable alpha blending untuk PNG (transparansi)
                        if ($imageType === 'png') {
                            imagealphablending($croppedImage, false);
                            imagesavealpha($croppedImage, true);
                            $transparent = imagecolorallocatealpha($croppedImage, 0, 0, 0, 127);
                            imagefill($croppedImage, 0, 0, $transparent);
                        }
                        
                        // Copy hanya bagian atas gambar (halaman pertama) dari gambar asli
                        imagecopyresampled(
                            $croppedImage, 
                            $sourceImage, 
                            0, 0,  // Destination x, y
                            0, 0,  // Source x, y (mulai dari atas kiri)
                            $drawingWidth, 
                            $imageHeightToUse, 
                            $drawingWidth, 
                            $imageHeightToUse
                        );
                        
                        // Simpan gambar yang sudah dipotong
                        if ($imageType === 'png') {
                            imagepng($croppedImage, $croppedImagePath, 9);
                        } else {
                            imagejpeg($croppedImage, $croppedImagePath, 100);
                        }
                        
                        imagedestroy($sourceImage);
                        imagedestroy($croppedImage);
                        
                        // Gunakan gambar yang sudah dipotong
                        $tempImagePath = $croppedImagePath;
                    }
                    
                    // Overlay gambar drawing ke halaman pertama dengan ukuran yang sesuai
                    $pdf->Image(
                        $tempImagePath, 
                        0, 0, 
                        $pageWidth, $pageHeight, 
                        strtoupper($imageType), 
                        '', '', 
                        false, // link
                        300, // dpi
                        '', // align
                        false, // resize
                        false, // palign
                        false, // ismask
                        false // imgmask
                    );
                }
            }
            
            // Simpan PDF yang sudah digabungkan
            $outputPdf = $pdf->Output('S');
            
            // Validasi output PDF tidak kosong
            if (empty($outputPdf)) {
                throw new \Exception('PDF output kosong setelah proses merge.');
            }
            
            // Simpan PDF ke storage
            $saved = Storage::disk('public')->put($filePath, $outputPdf);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan PDF ke storage.');
            }
            
            // Verifikasi file tersimpan dengan benar
            if (!Storage::disk('public')->exists($filePath)) {
                throw new \Exception('File PDF tidak ditemukan setelah disimpan.');
            }
            
            // Hapus temporary images
            if ($tempImagePath && file_exists($tempImagePath)) {
                @unlink($tempImagePath);
            }
            
            // Hapus gambar yang sudah dipotong jika ada
            if ($croppedImagePath && file_exists($croppedImagePath)) {
                @unlink($croppedImagePath);
            }
            
            // Cleanup temp directory
            $tempFiles = glob($tempDir . '/drawing_*');
            foreach ($tempFiles as $tempFile) {
                @unlink($tempFile);
            }

            return response()->json([
                'success' => true,
                'message' => 'Jobcard berhasil diupdate dengan gambar drawing!'
            ]);

        } catch (\Throwable $e) {
            Log::error('ERROR UPDATE JOBCARD', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal update jobcard: ' . $e->getMessage()]);
        }
    }
}