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

            // =====================================================
            // 1. Ambil data Work Order LENGKAP dari Maximo
            // =====================================================
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
                    'REPORTEDBY',
                    'ACTSTART',
                    'ACTFINISH',
                    'GLACCOUNT',
                    'FAILURECODE',
                    'JPNUM',
                    'PERSONGROUP',
                    'TARGSTARTDATE',
                    'TARGCOMPDATE',
                    'LEAD',
                    'ORIGRECORDID',
                    'ORIGRECORDCLASS',
                    'ANGGARAN',
                    'WORKORDERID',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                    ->with('error', 'Work Order tidak ditemukan.');
            }

            // Cek apakah status adalah APPR
            if (strtoupper($wo->status ?? '') !== 'APPR') {
                return redirect()->route('pemeliharaan.labor-saya.edit', ['id' => $wonum])
                    ->with('error', 'Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.');
            }

            // =====================================================
            // 2. Ambil Asset Description dari tabel ASSET
            // =====================================================
            $assetDescription = '-';
            if (!empty($wo->assetnum)) {
                try {
                    $asset = DB::connection('oracle')
                        ->table('ASSET')
                        ->select(['DESCRIPTION'])
                        ->where('ASSETNUM', $wo->assetnum)
                        ->where('SITEID', 'KD')
                        ->first();
                    $assetDescription = $asset->description ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch ASSET description', ['assetnum' => $wo->assetnum, 'error' => $e->getMessage()]);
                }
            }

            // =====================================================
            // 3. Ambil Location Description dari tabel LOCATIONS
            // =====================================================
            $locationDescription = '-';
            if (!empty($wo->location)) {
                try {
                    $location = DB::connection('oracle')
                        ->table('LOCATIONS')
                        ->select(['DESCRIPTION'])
                        ->where('LOCATION', $wo->location)
                        ->where('SITEID', 'KD')
                        ->first();
                    $locationDescription = $location->description ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch LOCATIONS description', ['location' => $wo->location, 'error' => $e->getMessage()]);
                }
            }

            // =====================================================
            // 4. Cari linked Service Request
            // =====================================================
            $srData = null;
            $srTicketId = null;

            if (!empty($wo->origrecordid) && strtoupper($wo->origrecordclass ?? '') === 'SR') {
                $srTicketId = $wo->origrecordid;
            }

            if (!$srTicketId) {
                try {
                    $related = DB::connection('oracle')
                        ->table('RELATEDRECORD')
                        ->select(['RELATEDRECKEY'])
                        ->where('RECORDKEY', $wonum)
                        ->where('RELATEDRECCLASS', 'SR')
                        ->first();
                    if ($related) {
                        $srTicketId = $related->relatedreckey;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch RELATEDRECORD for SR', ['wonum' => $wonum, 'error' => $e->getMessage()]);
                }
            }

            if ($srTicketId) {
                try {
                    $sr = DB::connection('oracle')
                        ->table('SR')
                        ->select([
                            'TICKETID',
                            'TICKETUID',
                            'DESCRIPTION',
                            'STATUS',
                            'REPORTEDBY',
                            'REPORTDATE',
                            'LOCATION',
                            'ASSETNUM',
                        ])
                        ->where('TICKETID', $srTicketId)
                        ->where('SITEID', 'KD')
                        ->first();

                    if ($sr) {
                        $srReportedByName = '-';
                        if (!empty($sr->reportedby)) {
                            try {
                                $srPerson = DB::connection('oracle')
                                    ->table('PERSON')
                                    ->select(['DISPLAYNAME'])
                                    ->where('PERSONID', $sr->reportedby)
                                    ->first();
                                $srReportedByName = $srPerson->displayname ?? '-';
                            } catch (\Exception $e) {
                                Log::warning('Failed to fetch PERSON for SR reportedby', ['personid' => $sr->reportedby]);
                            }
                        }

                        $srLongDesc = '-';
                        $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION', 'TICKETLONGDESC'];

                        foreach ($ldTables as $ldTable) {
                            try {
                                $longDesc = DB::connection('oracle')
                                    ->table($ldTable)
                                    ->select(['LDTEXT'])
                                    ->where('LDKEY', $srTicketId)
                                    ->where('LDOWNERTABLE', 'SR')
                                    ->first();
                                if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                    $srLongDesc = $longDesc->ldtext;
                                    break;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        }

                        if ($srLongDesc === '-' && isset($sr->ticketuid)) {
                            foreach ($ldTables as $ldTable) {
                                try {
                                    $longDesc = DB::connection('oracle')
                                        ->table($ldTable)
                                        ->select(['LDTEXT'])
                                        ->where(function($q) use ($sr) {
                                            $q->where('LDKEY', $sr->ticketuid)
                                              ->orWhere('LDOWNERID', $sr->ticketuid);
                                        })
                                        ->where('LDOWNERTABLE', 'SR')
                                        ->first();
                                    if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                        $srLongDesc = $longDesc->ldtext;
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }

                        $srData = [
                            'ticketid' => $sr->ticketid ?? '-',
                            'description' => $sr->description ?? '-',
                            'status' => $sr->status ?? '-',
                            'reportedby' => $sr->reportedby ?? '-',
                            'reportedby_name' => $srReportedByName,
                            'reportdate' => isset($sr->reportdate) && $sr->reportdate ? Carbon::parse($sr->reportdate)->format('d-m-Y H:i') : '-',
                            'longdescription' => $srLongDesc,
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch SR data', ['ticketid' => $srTicketId, 'error' => $e->getMessage()]);
                }
            }

            // =====================================================
            // 5. Ambil nama person untuk WO Reported By
            // =====================================================
            $woReportedByName = '-';
            if (!empty($wo->reportedby)) {
                try {
                    $woPerson = DB::connection('oracle')
                        ->table('PERSON')
                        ->select(['DISPLAYNAME'])
                        ->where('PERSONID', $wo->reportedby)
                        ->first();
                    $woReportedByName = $woPerson->displayname ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch PERSON for WO reportedby', ['personid' => $wo->reportedby]);
                }
            }

            // =====================================================
            // 6. Ambil data Assignment (assigned labor)
            // =====================================================
            $assignedTo = '-';
            $assignedToName = '-';
            try {
                $assignment = DB::connection('oracle')
                    ->table('ASSIGNMENT')
                    ->select(['LABORCODE'])
                    ->where('WONUM', $wonum)
                    ->where('SITEID', 'KD')
                    ->first();
                if ($assignment && !empty($assignment->laborcode)) {
                    $assignedTo = $assignment->laborcode;
                    try {
                        $laborPerson = DB::connection('oracle')
                            ->table('LABOR')
                            ->select(['PERSONID'])
                            ->where('LABORCODE', $assignment->laborcode)
                            ->first();
                        if ($laborPerson && !empty($laborPerson->personid)) {
                            $assignPerson = DB::connection('oracle')
                                ->table('PERSON')
                                ->select(['DISPLAYNAME'])
                                ->where('PERSONID', $laborPerson->personid)
                                ->first();
                            $assignedToName = $assignPerson->displayname ?? '-';
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to fetch assigned person name', ['laborcode' => $assignment->laborcode]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch ASSIGNMENT', ['wonum' => $wonum, 'error' => $e->getMessage()]);
            }

            // =====================================================
            // 6.5. Ambil Long Description untuk PARENT WO
            // =====================================================
            $woLongDesc = '-';
            if (!empty($wo->workorderid)) {
                $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION'];
                foreach ($ldTables as $ldTable) {
                    try {
                        $longDesc = DB::connection('oracle')
                            ->table($ldTable)
                            ->select(['LDTEXT'])
                            ->where(function($q) use ($wo) {
                                $q->where('LDKEY', $wo->workorderid)
                                  ->orWhere('LDOWNERID', $wo->workorderid);
                            })
                            ->whereIn('LDOWNERTABLE', ['WORKORDER', 'WO'])
                            ->first();
                        if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                            $woLongDesc = $longDesc->ldtext;
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // =====================================================
            // 7. Ambil child tasks (child WO dimana PARENT = WONUM)
            // =====================================================
            $tasks = [];
            $allWOs = [$wonum];

            try {
                $childWOs = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->select([
                        'WONUM',
                        'DESCRIPTION',
                        'STATUS',
                        'SCHEDSTART',
                        'SCHEDFINISH',
                        'ACTSTART',
                        'ACTFINISH',
                        'PERSONGROUP',
                        'REPORTEDBY',
                        'SITEID',
                        'TARGSTARTDATE',
                        'TARGCOMPDATE',
                        'PARENT',
                        'WORKTYPE',
                        'REPORTDATE',
                        'FAILURECODE',
                        'GLACCOUNT',
                        'WOPRIORITY',
                        'ASSETNUM',
                        'LOCATION',
                        'WORKORDERID'
                    ])
                    ->where('PARENT', $wonum)
                    ->where('SITEID', 'KD')
                    ->orderBy('WONUM')
                    ->get();

                foreach ($childWOs as $child) {
                    $allWOs[] = $child->wonum;

                    // Cek assignment task
                    $taskAssign = '-';
                    try {
                        $ta = DB::connection('oracle')
                            ->table('ASSIGNMENT')
                            ->select(['LABORCODE'])
                            ->where('WONUM', $child->wonum)
                            ->where('SITEID', 'KD')
                            ->first();
                        if ($ta && !empty($ta->laborcode)) {
                            $taskAssign = $ta->laborcode;
                        }
                    } catch (\Exception $e) {}

                    // Cek Asset & Location Description untuk task
                    $taskAssetDesc = '-';
                    $taskLocDesc = '-';
                    if (!empty($child->assetnum)) {
                        try {
                            $ast = DB::connection('oracle')
                                ->table('ASSET')
                                ->select(['DESCRIPTION'])
                                ->where('ASSETNUM', $child->assetnum)
                                ->where('SITEID', 'KD')
                                ->first();
                            $taskAssetDesc = $ast->description ?? '-';
                        } catch (\Exception $e) {}
                    }
                    if (!empty($child->location)) {
                        try {
                            $loc = DB::connection('oracle')
                                ->table('LOCATIONS')
                                ->select(['DESCRIPTION'])
                                ->where('LOCATION', $child->location)
                                ->where('SITEID', 'KD')
                                ->first();
                            $taskLocDesc = $loc->description ?? '-';
                        } catch (\Exception $e) {}
                    }

                    // Cek Long Description untuk task WT
                    $taskLongDesc = '-';
                    if (!empty($child->workorderid)) {
                        $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION'];
                        foreach ($ldTables as $ldTable) {
                            try {
                                $longDesc = DB::connection('oracle')
                                    ->table($ldTable)
                                    ->select(['LDTEXT'])
                                    ->where(function($q) use ($child) {
                                        $q->where('LDKEY', $child->workorderid)
                                          ->orWhere('LDOWNERID', $child->workorderid);
                                    })
                                    ->whereIn('LDOWNERTABLE', ['WORKORDER', 'JOBTASK', 'WO'])
                                    ->first();
                                if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                    $taskLongDesc = $longDesc->ldtext;
                                    break;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }

                    $tasks[] = [
                        'wonum' => $child->wonum ?? '-',
                        'description' => $child->description ?? '-',
                        'status' => $child->status ?? '-',
                        'schedstart' => isset($child->schedstart) && $child->schedstart ? Carbon::parse($child->schedstart)->format('d-m-Y H:i') : '-',
                        'schedfinish' => isset($child->schedfinish) && $child->schedfinish ? Carbon::parse($child->schedfinish)->format('d-m-Y H:i') : '-',
                        'actstart' => isset($child->actstart) && $child->actstart ? Carbon::parse($child->actstart)->format('d-m-Y H:i') : '-',
                        'actfinish' => isset($child->actfinish) && $child->actfinish ? Carbon::parse($child->actfinish)->format('d-m-Y H:i') : '-',
                        'persongroup' => $child->persongroup ?? '-',
                        'siteid' => $child->siteid ?? '-',
                        'targstartdate' => isset($child->targstartdate) && $child->targstartdate ? Carbon::parse($child->targstartdate)->format('d-m-Y H:i') : '-',
                        'targcompdate' => isset($child->targcompdate) && $child->targcompdate ? Carbon::parse($child->targcompdate)->format('d-m-Y H:i') : '-',
                        'parent' => $child->parent ?? '-',
                        'worktype' => $child->worktype ?? '-',
                        'reportdate' => isset($child->reportdate) && $child->reportdate ? Carbon::parse($child->reportdate)->format('d-m-Y H:i') : '-',
                        'reportedby' => $child->reportedby ?? '-',
                        'failurecode' => $child->failurecode ?? '-',
                        'glaccount' => $child->glaccount ?? '-',
                        'wopriority' => $child->wopriority ?? '-',
                        'assetnum' => $child->assetnum ?? '-',
                        'asset_description' => $taskAssetDesc,
                        'location' => $child->location ?? '-',
                        'location_description' => $taskLocDesc,
                        'assigned_to' => $taskAssign,
                        'longdescription' => $taskLongDesc,
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch child tasks', ['parent' => $wonum, 'error' => $e->getMessage()]);
            }

            // =====================================================
            // 8. Ambil Planned Labor (WPLABOR)
            // =====================================================
            $wplabors = [];
            try {
                $labors = DB::connection('oracle')->table('WPLABOR')
                    ->whereIn('WONUM', $allWOs)
                    ->where('SITEID', 'KD')
                    ->get();
                foreach($labors as $l) {
                    $wplabors[] = [
                        'wonum' => $l->wonum ?? '-',
                        'craft' => $l->craft ?? '-',
                        'skilllevel' => $l->skilllevel ?? '-',
                        'labor' => $l->laborcode ?? '-',
                        'quantity' => $l->quantity ?? '-',
                        'laborhrs' => $l->laborhrs ?? '-',
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch WPLABOR', ['error' => $e->getMessage()]);
            }

            // =====================================================
            // 9. Format data lengkap untuk PDF
            // =====================================================
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
                'reportedby' => $wo->reportedby ?? '-',
                'reportedby_name' => $woReportedByName,
                'actstart' => isset($wo->actstart) && $wo->actstart ? Carbon::parse($wo->actstart)->format('d-m-Y H:i') : '-',
                'actfinish' => isset($wo->actfinish) && $wo->actfinish ? Carbon::parse($wo->actfinish)->format('d-m-Y H:i') : '-',
                'glaccount' => $wo->glaccount ?? '-',
                'failurecode' => $wo->failurecode ?? '-',
                'jpnum' => $wo->jpnum ?? '-',
                'persongroup' => $wo->persongroup ?? '-',
                'targstartdate' => isset($wo->targstartdate) && $wo->targstartdate ? Carbon::parse($wo->targstartdate)->format('d-m-Y H:i') : '-',
                'targcompdate' => isset($wo->targcompdate) && $wo->targcompdate ? Carbon::parse($wo->targcompdate)->format('d-m-Y H:i') : '-',
                'lead' => $wo->lead ?? '-',
                'asset_description' => $assetDescription,
                'location_description' => $locationDescription,
                'assigned_to' => $assignedTo,
                'assigned_to_name' => $assignedToName,
                'anggaran' => $wo->anggaran ?? '-',
                'longdescription' => $woLongDesc,
            ];

            // Generate PDF dengan data lengkap
            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', [
                'wo' => $woData,
                'sr' => $srData,
                'tasks' => $tasks,
                'wplabors' => $wplabors,
            ]);

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