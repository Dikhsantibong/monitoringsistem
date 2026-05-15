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
use App\Models\UnitStatus;
use Illuminate\Support\Facades\Schema;
use App\Helpers\PemeliharaanLocationHelper;

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
            PemeliharaanLocationHelper::applyLocationFilter($baseStatsQuery);

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
                    'WOPRIORTEXT',
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
            PemeliharaanLocationHelper::applyLocationFilter($workOrdersQuery);

            // Search filter
            if ($search !== '') {
                $workOrdersQuery->where(function ($q) use ($search) {
                    $like = "%" . strtoupper($search) . "%";
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like)
                      ->orWhere('STATUS', 'LIKE', $like)
                      ->orWhere('WORKTYPE', 'LIKE', $like)
                      ->orWhere('WOPRIORTEXT', 'LIKE', $like)
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

            // Normalize items first to avoid case issues (Oracle keys can be uppercase/lowercase depending on driver)
            $normalizedItems = collect($workOrdersPaginator->items())->map(function($item) {
                return (object) array_change_key_case((array) $item, CASE_LOWER);
            });

            // Fetch Unit Status from MySQL for comparison (Resilient to failure)
            $unitComparison = collect();
            try {
                $wonums = $normalizedItems->pluck('wonum')->all();
                if (!empty($wonums)) {
                    $unitComparison = UnitStatus::whereIn('wonum', $wonums)->get()->keyBy('wonum');
                }
            } catch (\Exception $e) {
                Log::warning('Comparison DB fetch failed in LaborSayaController: ' . $e->getMessage());
            }

            // Format data untuk view
            $workOrders = $normalizedItems->map(function ($wo) use ($unitMapping, $unitComparison) {
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
                    'wopriority' => $wo->wopriortext ?? '-',
                    'priority' => $wo->wopriortext ?? '-',
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
                    'status_unit' => isset($unitComparison[$wo->wonum]) ? $unitComparison[$wo->wonum]->status_unit : '-',
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
        try {
            $wonum = trim($id);
            if ($wonum === '') {
                return redirect()->route('pemeliharaan.labor-saya')->with('error', 'WONUM tidak valid.');
            }

            $woData = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM', 'PARENT', 'STATUS', 'STATUSDATE', 'WORKTYPE', 'DESCRIPTION',
                    'ASSETNUM', 'LOCATION', 'JPNUM', 'FAILDATE', 'CHANGEBY', 'CHANGEDATE',
                    'ESTDUR', 'ESTLABHRS', 'ESTMATCOST', 'ESTLABCOST', 'ESTTOOLCOST',
                    'PMNUM', 'ACTLABHRS', 'ACTMATCOST', 'ACTLABCOST', 'ACTTOOLCOST',
                    'HASCHILDREN', 'OUTLABCOST', 'OUTMATCOST', 'OUTTOOLCOST', 'HISTORYFLAG',
                    'CONTRACT', 'WOPRIORITY', 'TARGCOMPDATE', 'TARGSTARTDATE',
                    'WOEQ1', 'WOEQ2', 'WOEQ3', 'WOEQ4', 'WOEQ5', 'WOEQ6',
                    'REPORTEDBY', 'REPORTDATE', 'PROBLEMCODE', 'DOWNTIME',
                    'ACTSTART', 'ACTFINISH', 'SCHEDSTART', 'SCHEDFINISH',
                    'REMDUR', 'CREWID', 'SUPERVISOR', 'FAILURECODE',
                    'ESTSERVCOST', 'ACTSERVCOST', 'ORGID', 'SITEID',
                    'WOCLASS', 'OWNER', 'OWNERGROUP', 'PERSONGROUP', 'LEAD',
                    'ORIGRECORDID', 'ORIGRECORDCLASS', 'GLACCOUNT',
                    'ANGGARAN', 'WONUMPLN',
                    'REMARKDESCC', 'REMARKDESCP', 'REMARKDESCPLN', 'REMARKDESCR',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum);

            // Filter lokasi
            PemeliharaanLocationHelper::applyLocationFilter($woData);
            $woRaw = $woData->first();

            if (!$woRaw) {
                return redirect()->route('pemeliharaan.labor-saya')->with('error', 'Work Order tidak ditemukan atau Anda tidak memiliki akses.');
            }

            // Normalize result to lowercase property names
            $woRaw = (object) array_change_key_case((array) $woRaw, CASE_LOWER);

            // Cek jobcard
            $jobcardExists = false;
            $jobcardPath = null;
            $jobcardUrl = null;
            $filename = 'JOBCARD_' . $wonum . '.pdf';
            $filePath = 'jobcards/' . $filename;
            
            if (Storage::disk('public')->exists($filePath)) {
                $jobcardExists = true;
                $jobcardPath = $filePath;
                $jobcardUrl = asset('storage/' . $filePath);
            }

            // Helper untuk format tanggal
            $fmtDate = function ($val) {
                return isset($val) && $val ? Carbon::parse($val)->format('d-m-Y H:i') : '-';
            };

            // Format data untuk view (Mirip standar detail)
            $workOrder = (object) [
                // Identifikasi
                'wonum' => $woRaw->wonum ?? '-',
                'parent' => $woRaw->parent ?? '-',
                'status' => $woRaw->status ?? '-',
                'statusdate' => $fmtDate($woRaw->statusdate ?? null),
                'worktype' => $woRaw->worktype ?? '-',
                'wopriority' => $woRaw->wopriority ?? '-',
                'woclass' => $woRaw->woclass ?? '-',
                'description' => $woRaw->description ?? '-',
                // Asset & Lokasi
                'assetnum' => $woRaw->assetnum ?? '-',
                'location' => $woRaw->location ?? '-',
                'siteid' => $woRaw->siteid ?? '-',
                'orgid' => $woRaw->orgid ?? '-',
                'downtime' => $woRaw->downtime ?? '-',
                // People
                'reportedby' => $woRaw->reportedby ?? '-',
                'supervisor' => $woRaw->supervisor ?? '-',
                'crewid' => $woRaw->crewid ?? '-',
                'lead' => $woRaw->lead ?? '-',
                'owner' => $woRaw->owner ?? '-',
                'ownergroup' => $woRaw->ownergroup ?? '-',
                'persongroup' => $woRaw->persongroup ?? '-',
                'changeby' => $woRaw->changeby ?? '-',
                // Tanggal
                'reportdate' => $fmtDate($woRaw->reportdate ?? null),
                'schedstart' => $fmtDate($woRaw->schedstart ?? null),
                'schedfinish' => $fmtDate($woRaw->schedfinish ?? null),
                'actstart' => $fmtDate($woRaw->actstart ?? null),
                'actfinish' => $fmtDate($woRaw->actfinish ?? null),
                'targstartdate' => $fmtDate($woRaw->targstartdate ?? null),
                'targcompdate' => $fmtDate($woRaw->targcompdate ?? null),
                'changedate' => $fmtDate($woRaw->changedate ?? null),
                'faildate' => $fmtDate($woRaw->faildate ?? null),
                // Estimasi
                'estdur' => $woRaw->estdur ?? 0,
                'estlabhrs' => $woRaw->estlabhrs ?? 0,
                'estmatcost' => $woRaw->estmatcost ?? 0,
                'estlabcost' => $woRaw->estlabcost ?? 0,
                'esttoolcost' => $woRaw->esttoolcost ?? 0,
                'estservcost' => $woRaw->estservcost ?? 0,
                // Aktual
                'actlabhrs' => $woRaw->actlabhrs ?? 0,
                'actmatcost' => $woRaw->actmatcost ?? 0,
                'actlabcost' => $woRaw->actlabcost ?? 0,
                'acttoolcost' => $woRaw->acttoolcost ?? 0,
                'actservcost' => $woRaw->actservcost ?? 0,
                // Outside Cost
                'outlabcost' => $woRaw->outlabcost ?? 0,
                'outmatcost' => $woRaw->outmatcost ?? 0,
                'outtoolcost' => $woRaw->outtoolcost ?? 0,
                // Codes
                'jpnum' => $woRaw->jpnum ?? '-',
                'pmnum' => $woRaw->pmnum ?? '-',
                'failurecode' => $woRaw->failurecode ?? '-',
                'problemcode' => $woRaw->problemcode ?? '-',
                'glaccount' => $woRaw->glaccount ?? '-',
                'contract' => $woRaw->contract ?? '-',
                // Flags
                'haschildren' => $woRaw->haschildren ?? 0,
                'historyflag' => $woRaw->historyflag ?? 0,
                'remdur' => $woRaw->remdur ?? 0,
                // Origin
                'origrecordid' => $woRaw->origrecordid ?? '-',
                'origrecordclass' => $woRaw->origrecordclass ?? '-',
                // Custom / WOEQ
                'woeq1' => $woRaw->woeq1 ?? '-',
                'woeq2' => $woRaw->woeq2 ?? '-',
                'woeq3' => $woRaw->woeq3 ?? '-',
                'woeq4' => $woRaw->woRaw->woeq4 ?? '-',
                'woeq5' => $woRaw->woeq5 ?? 0,
                'woeq6' => $fmtDate($woRaw->woeq6 ?? null),
                // PLN Custom
                'anggaran' => $woRaw->anggaran ?? '-',
                'wonumpln' => $woRaw->wonumpln ?? '-',
                'remarkdescc' => $woRaw->remarkdescc ?? '-',
                'remarkdescp' => $woRaw->remarkdescp ?? '-',
                'remarkdescpln' => $woRaw->remarkdescpln ?? '-',
                'remarkdescr' => $woRaw->remarkdescr ?? '-',
                // Legacy / Jobcard Fields
                'jobcard_exists' => $jobcardExists,
                'jobcard_path' => $jobcardPath,
                'jobcard_url' => $jobcardUrl,
                'status_unit' => (function() use ($wonum) {
                    try {
                        return UnitStatus::where('wonum', $wonum)->value('status_unit') ?? '-';
                    } catch (\Exception $e) {
                        return '-';
                    }
                })(),
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
     * Update Work Order data (Status Unit in MySQL)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status_unit' => 'nullable|string|max:50',
        ]);

        try {
            $unitStatusModel = new UnitStatus();
            $targetConnection = $unitStatusModel->getConnectionName();

            // Check if table exists first
            if (!Schema::connection($targetConnection)->hasTable('unit_statuses')) {
                 return redirect()->back()
                    ->with('error', "Gagal: Tabel unit_statuses tidak ditemukan di database target ({$targetConnection}). Silakan jalankan SQL script yang diberikan sebelumnya.");
            }

            $wonum = trim($id);
            
            // Use manual update to be more explicit
            $unitStatus = UnitStatus::where('wonum', $wonum)->first();
            if (!$unitStatus) {
                $unitStatus = new UnitStatus();
                $unitStatus->wonum = $wonum;
            }
            $unitStatus->status_unit = $request->input('status_unit');
            $unitStatus->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status Unit berhasil diperbarui.',
                    'wonum' => $wonum,
                    'status_unit' => $request->input('status_unit')
                ]);
            }

            return redirect()->route('pemeliharaan.labor-saya.edit', $wonum)
                ->with('success', 'Status Unit berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating Unit Status in LaborSayaController: ' . $e->getMessage(), [
                'exception' => $e,
                'wonum' => $id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui Status Unit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui Status Unit. Silakan pastikan tabel unit_statuses sudah ada.');
        }
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
                    'WOPRIORTEXT',
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
                'wopriority' => $wo->wopriortext ?? '-',
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