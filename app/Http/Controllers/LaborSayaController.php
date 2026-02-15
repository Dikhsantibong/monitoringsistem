<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;

class LaborSayaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));
        $workOrderPage = $request->input('wo_page', 1);
        
        // Filter inputs
        $statusFilter = $request->input('status');
        $unitFilter = $request->input('unit');

        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');

        // Mapping unit prefixes to labels as per USER_REQUEST
        $unitMapping = [
            'KLKA' => 'PLTD KOLAKA',
            'LANI' => 'PLTD LANIPA NIPA',
            'SABI' => 'PLTM SABILAMBO',
            'MIKU' => 'PLTM MIKUASI',
            'BBAU' => 'PLTD BAU BAU',
            'WANG' => 'PLTD WANGI WANGI',
            'RAHA' => 'PLTD RAHA',
            'EREK' => 'PLTD EREKE',
            'RONG' => 'PLTM RONGI',
            'WINN' => 'PLTM WINNING',
            'POAS' => 'PLTD POASIA',
            'WUAW' => 'PLTD WUA WUA',
        ];

        // Prepare powerPlants for the filter dropdown
        $powerPlants = collect($unitMapping)->map(function($name, $prefix) {
            return (object)['id' => $prefix, 'name' => $name];
        });

        // Define status groups
        $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
        $closedStatuses = ['COMP', 'CLOSE'];

        // Summary Statistics from Oracle (Always filter by SITEID='KD')
        $stats = [
            'total' => 0,
            'appr' => 0,
            'wmatl' => 0,
            'inprg' => 0,
            'closed' => 0,
            'new_today' => 0,
        ];

        try {
            $baseStatsQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');

            // Card Total WO calculated from data SITE "KD"
            $stats['total'] = (clone $baseStatsQuery)->count();
            
            // Status counts for specific cards
            $statusCountsRaw = (clone $baseStatsQuery)
                ->select('STATUS', DB::raw('count(*) as total'))
                ->groupBy('STATUS')
                ->get();

            foreach ($statusCountsRaw as $sc) {
                $sc = (object) array_change_key_case((array) $sc, CASE_LOWER);
                $status = strtoupper(trim($sc->status ?? ''));
                
                if ($status === 'APPR') $stats['appr'] += $sc->total;
                elseif ($status === 'WMATL') $stats['wmatl'] += $sc->total;
                elseif (in_array($status, ['INPRG', 'IN PROGRESS'])) $stats['inprg'] += $sc->total;
                elseif (in_array($status, $closedStatuses) || in_array($status, ['CLOSED'])) $stats['closed'] += $sc->total;
            }

            // New Today (REPORTDATE is today)
            $stats['new_today'] = (clone $baseStatsQuery)
                ->whereRaw("TRUNC(REPORTDATE) = TRUNC(SYSDATE)")
                ->count();

        } catch (\Exception $e) {
            Log::error('Error getting Stats from Maximo in LaborSayaController: ' . $e->getMessage());
        }

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
                    $like = "%" . strtoupper($search) . "%";
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like)
                      ->orWhere('STATUS', 'LIKE', $like)
                      ->orWhere('WORKTYPE', 'LIKE', $like)
                      ->orWhere('WOPRIORITY', 'LIKE', $like)
                      ->orWhere('LOCATION', 'LIKE', $like)
                      ->orWhere('ASSETNUM', 'LIKE', $like);
                });
            }

            // Status filter (handle groups and individual)
            if ($statusFilter) {
                if ($statusFilter === 'OPEN_GROUP') {
                    $workOrdersQuery->whereIn('STATUS', $openStatuses);
                } elseif ($statusFilter === 'CLOSED_GROUP') {
                    $workOrdersQuery->whereIn('STATUS', $closedStatuses);
                } else {
                    $workOrdersQuery->where('STATUS', strtoupper($statusFilter));
                }
            }

            // Unit/Location filter using prefix mapping
            if ($unitFilter) {
                $workOrdersQuery->where('LOCATION', 'LIKE', strtoupper($unitFilter) . '%');
            }

            $workOrdersQuery->orderBy('STATUSDATE', 'desc');

            // Paginate query
            $workOrdersPaginator = $workOrdersQuery->paginate(10, ['*'], 'wo_page', $workOrderPage);

            // Format data untuk view
            $workOrders = collect($workOrdersPaginator->items())->map(function ($wo) use ($unitMapping) {
                // Normalize result to lowercase property names
                $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
                
                // Determine readable unit name based on location prefix
                $location = strtoupper($wo->location ?? '');
                $readableUnit = '-';
                foreach ($unitMapping as $prefix => $name) {
                    if (strpos($location, $prefix) === 0) {
                        $readableUnit = $name;
                        break;
                    }
                }

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
                    'kendala' => null, // Tidak ada di Maximo
                    'tindak_lanjut' => null, // Tidak ada di Maximo
                    'labor' => null, // Tidak ada di Maximo
                    'labors' => [], // Tidak ada di Maximo
                    'document_path' => null, // Tidak ada di Maximo
                    'power_plant_name' => $readableUnit !== '-' ? $readableUnit : ($wo->location ?? '-'),
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error getting Work Orders from Maximo in LaborSayaController: ' . $e->getMessage());
            $workOrders = collect([]);
            $workOrdersPaginator = null;
        }

        // Since we are using exclusively Oracle data, local backlogs are removed.
        $laborBacklogs = collect([]);
        $laborBacklogsPaginator = null;

        return view('pemeliharaan.labor-saya', [
            'workOrders' => $workOrders,
            'workOrdersPaginator' => $workOrdersPaginator,
            'laborBacklogs' => $laborBacklogs,
            'laborBacklogsPaginator' => $laborBacklogsPaginator,
            'powerPlants' => $powerPlants,
            'stats' => $stats,
            'q' => $search,
            'statusFilter' => $statusFilter,
            'unitFilter' => $unitFilter,
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
                ->where('WONUM', 'LIKE', 'WO%')
                ->where('WONUM', $id)
                ->first();

            if (!$workOrderRaw) {
                return redirect()->route('pemeliharaan.labor-saya')
                    ->with('error', 'Work Order tidak ditemukan di Maximo.');
            }

            // Normalize result to lowercase property names
            $workOrderRaw = (object) array_change_key_case((array) $workOrderRaw, CASE_LOWER);

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
                'kendala' => null,
                'tindak_lanjut' => null,
                'labor' => null,
                'labors' => [],
                'document_path' => $jobcardPath, // Gunakan path jobcard jika ada
                'power_plant_id' => null,
                'power_plant_name' => $workOrderRaw->location ?? ($workOrderRaw->siteid ?? 'KD'),
                'materials' => [],
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

        // Fetch Units/Locations from Oracle for the select dropdown
        $powerPlants = collect();
        try {
            $locations = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%')
                ->whereNotNull('LOCATION')
                ->distinct()
                ->pluck('LOCATION');
            
            $powerPlants = $locations->map(function($loc) {
                return (object)['id' => $loc, 'name' => $loc];
            });
        } catch (\Exception $e) {
            Log::error('Error fetching locations from Oracle: ' . $e->getMessage());
        }

        // Local laborers and materials are removed as per Oracle-only requirement
        $masterLabors = collect([]);
        $materials = collect([]);

        return view('pemeliharaan.labor-edit', compact('workOrder', 'powerPlants', 'masterLabors', 'materials'));
    }

    /**
     * UPDATE and BACKLOG methods (local DB) have been removed 
     * to strictly use Oracle data.
     */

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

    /**
     * Generate Jobcard PDF (mirip MaximoController)
     */
    public function generateJobcard(Request $request)
    {
        try {
            $wonum = $request->input('wonum');
            
            if (!$wonum) {
                return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                    ->with('error', 'WONUM tidak valid.');
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
                ->where('WONUM', 'LIKE', 'WO%')
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                    ->with('error', 'Work Order tidak ditemukan.');
            }

            // Normalize result to lowercase property names
            $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);

            // Cek apakah status adalah APPR
            if (strtoupper($wo->status ?? '') !== 'APPR') {
                return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                    ->with('error', 'Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.');
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

            // Simpan PDF ke storage public dengan nama deterministik
            $directory = 'jobcards';
            $filename = 'JOBCARD_' . $wonum . '.pdf';
            $filePath = $directory . '/' . $filename;
            
            // Pastikan directory ada
            Storage::disk('public')->makeDirectory($directory);
            
            // Simpan / overwrite PDF
            Storage::disk('public')->put($filePath, $pdf->output());

            return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                ->with('success', 'Jobcard berhasil di-generate! (Tersimpan di server: ' . $filename . ')');

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (GENERATE JOBCARD - LaborSaya)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);
            return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $request->input('wonum')])
                ->with('error', 'Gagal mengambil data Work Order untuk generate jobcard.');
        } catch (\Throwable $e) {
            Log::error('ERROR GENERATE JOBCARD (LaborSaya)', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $request->input('wonum')])
                ->with('error', 'Gagal generate jobcard: ' . $e->getMessage());
        }
    }
}