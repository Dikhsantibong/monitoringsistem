<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MaterialMaster;
use Carbon\Carbon;

class LaborSayaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $workOrderPage = $request->input('page', 1);

        // Get Work Orders dari Maximo (Oracle)
        $workOrders = collect();
        $workOrdersPaginator = null;
        try {
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
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');

            // Search filter
            if ($search !== '') {
                $workOrdersQuery->where(function ($q) use ($search) {
                    $like = "%{$search}%";
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like)
                      ->orWhere('STATUS', 'LIKE', $like)
                      ->orWhere('WORKTYPE', 'LIKE', $like)
                      ->orWhere('WOPRIORITY', 'LIKE', $like)
                      ->orWhere('LOCATION', 'LIKE', $like)
                      ->orWhere('ASSETNUM', 'LIKE', $like);
                });
            }

            $workOrdersQuery->orderBy('STATUSDATE', 'desc');

            // Paginate query
            $workOrdersPaginator = $workOrdersQuery->paginate(10, ['*'], 'page', $workOrderPage);

            // Format data untuk view
            $workOrders = collect($workOrdersPaginator->items())->map(function ($wo) {
                return [
                    'id' => $wo->wonum ?? '-',
                    'wonum' => $wo->wonum ?? '-',
                    'parent' => $wo->parent ?? '-',
                    'description' => $wo->description ?? '-',
                    'status' => $wo->status ?? '-',
                    'statusdate' => isset($wo->statusdate) && $wo->statusdate
                        ? Carbon::parse($wo->statusdate)->format('Y-m-d H:i:s')
                        : null,
                    'worktype' => $wo->worktype ?? '-',
                    'type' => $wo->worktype ?? '-',
                    'wopriority' => $wo->wopriority ?? '-',
                    'priority' => $wo->wopriority ?? '-',
                    'assetnum' => $wo->assetnum ?? '-',
                    'location' => $wo->location ?? '-',
                    'siteid' => $wo->siteid ?? '-',
                    'downtime' => $wo->downtime ?? '-',
                    'schedstart' => isset($wo->schedstart) && $wo->schedstart
                        ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s')
                        : null,
                    'schedule_start' => isset($wo->schedstart) && $wo->schedstart
                        ? Carbon::parse($wo->schedstart)->format('Y-m-d H:i:s')
                        : null,
                    'schedfinish' => isset($wo->schedfinish) && $wo->schedfinish
                        ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s')
                        : null,
                    'schedule_finish' => isset($wo->schedfinish) && $wo->schedfinish
                        ? Carbon::parse($wo->schedfinish)->format('Y-m-d H:i:s')
                        : null,
                    'reportdate' => isset($wo->reportdate) && $wo->reportdate
                        ? Carbon::parse($wo->reportdate)->format('Y-m-d H:i:s')
                        : null,
                    'created_at' => isset($wo->reportdate) && $wo->reportdate
                        ? Carbon::parse($wo->reportdate)->format('Y-m-d H:i:s')
                        : Carbon::now()->format('Y-m-d H:i:s'),
                    'power_plant_name' => $wo->location ?? ($wo->siteid ?? 'KD'),
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in LaborSayaController: ' . $e->getMessage());
            $workOrders = collect([]);
            $workOrdersPaginator = null;
        }

        return view('pemeliharaan.labor-saya', [
            'workOrders' => $workOrders,
            'workOrdersPaginator' => $workOrdersPaginator,
            'q' => $search,
        ]);
    }

    public function edit($id)
    {
        // Ambil data dari Maximo berdasarkan WONUM
        try {
            $workOrderRaw = DB::connection('oracle')
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
                ->where('WONUM', $id)
                ->first();

            if (!$workOrderRaw) {
                return redirect()->route('pemeliharaan.labor-saya')
                    ->with('error', 'Work Order tidak ditemukan di Maximo.');
            }

            // Cek apakah jobcard sudah di-generate (ada di storage)
            $wonum = trim($workOrderRaw->wonum ?? '');
            $jobcardExists = false;
            $jobcardPath = null;
            $jobcardUrl = null;
            
            if ($wonum && $wonum !== '' && $wonum !== '-') {
                $directory = 'jobcards';
                $filename = 'JOBCARD_' . $wonum . '.pdf';
                $filePath = $directory . '/' . $filename;
                
                if (Storage::disk('public')->exists($filePath)) {
                    $jobcardExists = true;
                    $jobcardPath = $filePath;
                    $jobcardUrl = asset('storage/' . $filePath);
                }
            }

            // Format data untuk view
            $workOrder = (object) [
                'id' => $workOrderRaw->wonum ?? '-',
                'wonum' => $workOrderRaw->wonum ?? '-',
                'parent' => $workOrderRaw->parent ?? '-',
                'description' => $workOrderRaw->description ?? '-',
                'status' => $workOrderRaw->status ?? '-',
                'statusdate' => isset($workOrderRaw->statusdate) && $workOrderRaw->statusdate
                    ? Carbon::parse($workOrderRaw->statusdate)->format('Y-m-d H:i:s')
                    : null,
                'worktype' => $workOrderRaw->worktype ?? '-',
                'type' => $workOrderRaw->worktype ?? '-',
                'wopriority' => $workOrderRaw->wopriority ?? '-',
                'priority' => $workOrderRaw->wopriority ?? '-',
                'assetnum' => $workOrderRaw->assetnum ?? '-',
                'location' => $workOrderRaw->location ?? '-',
                'siteid' => $workOrderRaw->siteid ?? '-',
                'downtime' => $workOrderRaw->downtime ?? '-',
                'schedstart' => isset($workOrderRaw->schedstart) && $workOrderRaw->schedstart
                    ? Carbon::parse($workOrderRaw->schedstart)->format('Y-m-d H:i:s')
                    : null,
                'schedule_start' => isset($workOrderRaw->schedstart) && $workOrderRaw->schedstart
                    ? Carbon::parse($workOrderRaw->schedstart)->format('Y-m-d H:i:s')
                    : null,
                'schedfinish' => isset($workOrderRaw->schedfinish) && $workOrderRaw->schedfinish
                    ? Carbon::parse($workOrderRaw->schedfinish)->format('Y-m-d H:i:s')
                    : null,
                'schedule_finish' => isset($workOrderRaw->schedfinish) && $workOrderRaw->schedfinish
                    ? Carbon::parse($workOrderRaw->schedfinish)->format('Y-m-d H:i:s')
                    : null,
                'reportdate' => isset($workOrderRaw->reportdate) && $workOrderRaw->reportdate
                    ? Carbon::parse($workOrderRaw->reportdate)->format('Y-m-d H:i:s')
                    : null,
                'power_plant_name' => $workOrderRaw->location ?? ($workOrderRaw->siteid ?? 'KD'),
                // Tambahkan info jobcard
                'jobcard_exists' => $jobcardExists,
                'jobcard_path' => $jobcardPath,
                'jobcard_url' => $jobcardUrl,
            ];

        } catch (\Exception $e) {
            Log::error('Error getting Work Order from Maximo in LaborSayaController::edit: ' . $e->getMessage());
            return redirect()->route('pemeliharaan.labor-saya')
                ->with('error', 'Gagal mengambil data Work Order dari Maximo.');
        }

        return view('pemeliharaan.labor-edit', compact('workOrder'));
    }

    /**
     * Halaman Edit PDF Jobcard (halaman penuh, bukan modal)
     */
    public function editJobcard($wonum)
    {
        // Cek apakah jobcard ada di storage
        $wonum = trim($wonum);
        $directory = 'jobcards';
        $filename = 'JOBCARD_' . $wonum . '.pdf';
        $filePath = $directory . '/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('pemeliharaan.labor-saya')
                ->with('error', 'Jobcard untuk WO ' . $wonum . ' belum di-generate. Silakan generate terlebih dahulu di halaman Admin Maximo.');
        }
        
        $jobcardUrl = asset('storage/' . $filePath);
        $jobcardPath = $filePath;
        
        return view('pemeliharaan.jobcard-edit', compact('wonum', 'jobcardUrl', 'jobcardPath'));
    }

    /**
     * Update PDF Jobcard dari hasil edit
     */
    public function updateJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            Log::info('updateJobcard called', [
                'path' => $filePath,
                'hasFile' => $request->hasFile('document'),
                'allInputs' => $request->except(['document', '_token']),
            ]);
            
            if (!$filePath) {
                return response()->json(['success' => false, 'message' => 'Path tidak valid.']);
            }

            if (!$request->hasFile('document')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan dalam request.']);
            }

            $file = $request->file('document');
            
            Log::info('File received', [
                'originalName' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            
            // Validasi file PDF (lebih fleksibel)
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType();
            if ($ext !== 'pdf' && $mime !== 'application/pdf') {
                return response()->json(['success' => false, 'message' => 'File harus berformat PDF. Received: ' . $ext . ' / ' . $mime]);
            }

            // Simpan file yang sudah di-edit (overwrite)
            $content = file_get_contents($file->getRealPath());
            
            if (empty($content)) {
                return response()->json(['success' => false, 'message' => 'File content kosong.']);
            }
            
            Log::info('Saving file', [
                'path' => $filePath,
                'contentSize' => strlen($content),
            ]);
            
            $saved = Storage::disk('public')->put($filePath, $content);
            
            if (!$saved) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan file ke storage.']);
            }

            Log::info('Jobcard updated successfully', ['path' => $filePath, 'size' => strlen($content)]);

            return response()->json([
                'success' => true,
                'message' => 'Jobcard berhasil diupdate!'
            ]);

        } catch (\Throwable $e) {
            Log::error('ERROR UPDATE JOBCARD (LaborSaya)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal update jobcard: ' . $e->getMessage()]);
        }
    }

    /**
     * Download Jobcard PDF
     */
    public function downloadJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                return redirect()->route('pemeliharaan.labor-saya')->with('error', 'File jobcard tidak ditemukan.');
            }

            return Storage::disk('public')->download($filePath);
        } catch (\Throwable $e) {
            Log::error('ERROR DOWNLOAD JOBCARD (LaborSaya)', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('pemeliharaan.labor-saya')->with('error', 'Gagal download jobcard.');
        }
    }
}