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
use App\Models\UnitStatus;

class MaximoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $workOrderPage = $request->input('wo_page', 1);
        $serviceRequestPage = $request->input('sr_page', 1);

        try {
            // Filter Work Order
            $woStatusFilter = $request->input('wo_status');
            $statusUnitFilter = $request->input('status_unit');
            $woWorkTypeFilter = $request->input('wo_worktype');

            // Summary Statistics from Oracle (Always filter by SITEID='KD' and PREFIX 'WO')
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
                    ->where('SITEID', 'KD');

                $stats['total'] = (clone $baseStatsQuery)->count();
                
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
                    elseif (in_array($status, ['COMP', 'CLOSE', 'CLOSED'])) $stats['closed'] += $sc->total;
                }

                $stats['new_today'] = (clone $baseStatsQuery)
                    ->whereRaw("TRUNC(REPORTDATE) = TRUNC(SYSDATE)")
                    ->count();

            } catch (\Exception $e) {
                Log::error('Error getting Stats from Maximo in MaximoController: ' . $e->getMessage());
            }

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
            
            // Filter Status Maximo
            if ($woStatusFilter) {
                $workOrdersQuery->where('STATUS', $woStatusFilter);
            }

            // Filter Status Unit (Logic: Query MySQL for WONUMs then filter Oracle)
            if ($statusUnitFilter) {
                $filteredWonums = UnitStatus::where('status_unit', $statusUnitFilter)
                    ->pluck('wonum')
                    ->toArray();
                
                if (!empty($filteredWonums)) {
                    $workOrdersQuery->whereIn('WONUM', $filteredWonums);
                } else {
                    // Jika filter ada tapi tidak ada wonum yang cocok di MySQL, 
                    // buat query menghasilkan hasil kosong (WONUM null tak mungkin ada)
                    $workOrdersQuery->whereNull('WONUM');
                }
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
                    'FAULTPRIORITY',
                    'FAULTTYPE',
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

            // Ambil Status Unit dari MySQL untuk WONUM yang ada
            $wonums = collect($workOrders->items())->pluck('wonum')->unique();
            $unitStatuses = UnitStatus::whereIn('wonum', $wonums)
                ->get()
                ->keyBy('wonum');

            return view('admin.maximo.index', [
                'workOrders'      => $this->formatWorkOrders($workOrders->items(), $unitStatuses),
                'workOrdersPaginator' => $workOrders,
                'serviceRequests' => $this->formatServiceRequests($serviceRequests->items()),
                'serviceRequestsPaginator' => $serviceRequests,
                'search'          => $search,
                'statusFilter'    => $woStatusFilter,
                'statusUnitFilter'=> $statusUnitFilter,
                'woWorkTypeFilter'=> $woWorkTypeFilter,
                'stats'           => $stats,
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
                'search' => $search,
                'statusFilter' => $woStatusFilter,
                'statusUnitFilter' => $statusUnitFilter,
                'woWorkTypeFilter' => $woWorkTypeFilter,
                'stats' => [
                    'total' => 0, 'appr' => 0, 'wmatl' => 0, 'inprg' => 0, 'closed' => 0, 'new_today' => 0
                ],
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
                'search' => $search,
                'statusFilter' => $woStatusFilter,
                'statusUnitFilter' => $statusUnitFilter,
                'woWorkTypeFilter' => $woWorkTypeFilter,
                'stats' => [
                    'total' => 0, 'appr' => 0, 'wmatl' => 0, 'inprg' => 0, 'closed' => 0, 'new_today' => 0
                ],
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
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('admin.maximo.index')->with('error', 'Work Order tidak ditemukan.');
            }

            // Helper untuk format tanggal
            $fmtDate = function ($val) {
                return isset($val) && $val ? Carbon::parse($val)->format('d-m-Y H:i') : '-';
            };

            return view('admin.maximo.workorder-detail', [
                'wo' => [
                    // Identifikasi
                    'wonum' => $wo->wonum ?? '-',
                    'parent' => $wo->parent ?? '-',
                    'status' => $wo->status ?? '-',
                    'statusdate' => $fmtDate($wo->statusdate ?? null),
                    'worktype' => $wo->worktype ?? '-',
                    'wopriority' => $wo->wopriority ?? '-',
                    'woclass' => $wo->woclass ?? '-',
                    'description' => $wo->description ?? '-',
                    // Asset & Lokasi
                    'assetnum' => $wo->assetnum ?? '-',
                    'location' => $wo->location ?? '-',
                    'siteid' => $wo->siteid ?? '-',
                    'orgid' => $wo->orgid ?? '-',
                    'downtime' => $wo->downtime ?? '-',
                    // People
                    'reportedby' => $wo->reportedby ?? '-',
                    'supervisor' => $wo->supervisor ?? '-',
                    'crewid' => $wo->crewid ?? '-',
                    'lead' => $wo->lead ?? '-',
                    'owner' => $wo->owner ?? '-',
                    'ownergroup' => $wo->ownergroup ?? '-',
                    'persongroup' => $wo->persongroup ?? '-',
                    'changeby' => $wo->changeby ?? '-',
                    // Tanggal
                    'reportdate' => $fmtDate($wo->reportdate ?? null),
                    'schedstart' => $fmtDate($wo->schedstart ?? null),
                    'schedfinish' => $fmtDate($wo->schedfinish ?? null),
                    'actstart' => $fmtDate($wo->actstart ?? null),
                    'actfinish' => $fmtDate($wo->actfinish ?? null),
                    'targstartdate' => $fmtDate($wo->targstartdate ?? null),
                    'targcompdate' => $fmtDate($wo->targcompdate ?? null),
                    'changedate' => $fmtDate($wo->changedate ?? null),
                    'faildate' => $fmtDate($wo->faildate ?? null),
                    // Estimasi
                    'estdur' => $wo->estdur ?? 0,
                    'estlabhrs' => $wo->estlabhrs ?? 0,
                    'estmatcost' => $wo->estmatcost ?? 0,
                    'estlabcost' => $wo->estlabcost ?? 0,
                    'esttoolcost' => $wo->esttoolcost ?? 0,
                    'estservcost' => $wo->estservcost ?? 0,
                    // Aktual
                    'actlabhrs' => $wo->actlabhrs ?? 0,
                    'actmatcost' => $wo->actmatcost ?? 0,
                    'actlabcost' => $wo->actlabcost ?? 0,
                    'acttoolcost' => $wo->acttoolcost ?? 0,
                    'actservcost' => $wo->actservcost ?? 0,
                    // Outside Cost
                    'outlabcost' => $wo->outlabcost ?? 0,
                    'outmatcost' => $wo->outmatcost ?? 0,
                    'outtoolcost' => $wo->outtoolcost ?? 0,
                    // Codes
                    'jpnum' => $wo->jpnum ?? '-',
                    'pmnum' => $wo->pmnum ?? '-',
                    'failurecode' => $wo->failurecode ?? '-',
                    'problemcode' => $wo->problemcode ?? '-',
                    'glaccount' => $wo->glaccount ?? '-',
                    'contract' => $wo->contract ?? '-',
                    // Flags
                    'haschildren' => $wo->haschildren ?? 0,
                    'historyflag' => $wo->historyflag ?? 0,
                    'remdur' => $wo->remdur ?? 0,
                    // Origin
                    'origrecordid' => $wo->origrecordid ?? '-',
                    'origrecordclass' => $wo->origrecordclass ?? '-',
                    // Custom / WOEQ
                    'woeq1' => $wo->woeq1 ?? '-',
                    'woeq2' => $wo->woeq2 ?? '-',
                    'woeq3' => $wo->woeq3 ?? '-',
                    'woeq4' => $wo->woeq4 ?? '-',
                    'woeq5' => $wo->woeq5 ?? 0,
                    'woeq6' => $fmtDate($wo->woeq6 ?? null),
                    // PLN Custom
                    'anggaran' => $wo->anggaran ?? '-',
                    'wonumpln' => $wo->wonumpln ?? '-',
                    'remarkdescc' => $wo->remarkdescc ?? '-',
                    'remarkdescp' => $wo->remarkdescp ?? '-',
                    'remarkdescpln' => $wo->remarkdescpln ?? '-',
                    'remarkdescr' => $wo->remarkdescr ?? '-',
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
                ->where('SITEID', 'KD')
                ->where('TICKETID', $ticketid)
                ->first();

            if (!$sr) {
                return redirect()->route('admin.maximo.index')->with('error', 'Service Request tidak ditemukan.');
            }

            $fmtDate = function ($val) {
                return isset($val) && $val ? Carbon::parse($val)->format('d-m-Y H:i') : '-';
            };

            // Ambil Long Description — coba beberapa nama tabel
            $longDescription = '-';
            $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION', 'TICKETLONGDESC'];
            foreach ($ldTables as $ldTable) {
                try {
                    $longDesc = DB::connection('oracle')
                        ->table($ldTable)
                        ->where('LDKEY', $ticketid)
                        ->where('LDOWNERTABLE', 'SR')
                        ->first();
                    if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                        $longDescription = $longDesc->ldtext;
                        break;
                    }
                } catch (\Exception $e) {
                    // tabel tidak ada, coba tabel berikutnya
                    continue;
                }
            }

            // Jika masih belum ketemu, coba via TICKETUID + LDOWNERID
            if ($longDescription === '-' && isset($sr->ticketuid)) {
                foreach ($ldTables as $ldTable) {
                    try {
                        $longDesc = DB::connection('oracle')
                            ->table($ldTable)
                            ->where('LDOWNERID', $sr->ticketuid)
                            ->first();
                        if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                            $longDescription = $longDesc->ldtext;
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // Ambil nama person untuk Reported By
            $reportedByName = '-';
            if (!empty($sr->reportedby)) {
                try {
                    $person = DB::connection('oracle')
                        ->table('PERSON')
                        ->select(['DISPLAYNAME'])
                        ->where('PERSONID', $sr->reportedby)
                        ->first();
                    $reportedByName = $person->displayname ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch PERSON', ['personid' => $sr->reportedby]);
                }
            }

            // Ambil nama affected person
            $affectedPersonName = '-';
            if (!empty($sr->affectedperson) && $sr->affectedperson !== ($sr->reportedby ?? '')) {
                try {
                    $person = DB::connection('oracle')
                        ->table('PERSON')
                        ->select(['DISPLAYNAME'])
                        ->where('PERSONID', $sr->affectedperson)
                        ->first();
                    $affectedPersonName = $person->displayname ?? '-';
                } catch (\Exception $e) {
                    // skip
                }
            } elseif (!empty($sr->affectedperson)) {
                $affectedPersonName = $reportedByName;
            }

            // Ambil Asset Description
            $assetDescription = '-';
            if (!empty($sr->assetnum)) {
                try {
                    $asset = DB::connection('oracle')
                        ->table('ASSET')
                        ->select(['DESCRIPTION'])
                        ->where('ASSETNUM', $sr->assetnum)
                        ->where('SITEID', 'KD')
                        ->first();
                    $assetDescription = $asset->description ?? '-';
                } catch (\Exception $e) {
                    // skip
                }
            }

            // Ambil Location Description
            $locationDescription = '-';
            if (!empty($sr->location)) {
                try {
                    $location = DB::connection('oracle')
                        ->table('LOCATIONS')
                        ->select(['DESCRIPTION'])
                        ->where('LOCATION', $sr->location)
                        ->where('SITEID', 'KD')
                        ->first();
                    $locationDescription = $location->description ?? '-';
                } catch (\Exception $e) {
                    // skip
                }
            }

            return view('admin.maximo.service-request-detail', [
                'sr' => [
                    // Identifikasi
                    'ticketid' => $sr->ticketid ?? '-',
                    'ticketuid' => $sr->ticketuid ?? '-',
                    'class' => $sr->class ?? '-',
                    'status' => $sr->status ?? '-',
                    'statusdate' => $fmtDate($sr->statusdate ?? null),
                    'description' => $sr->description ?? '-',
                    'longdescription' => $longDescription,
                    // Priority & Type
                    'faultpriority' => $sr->faultpriority ?? '-',
                    'faulttype' => $sr->faulttype ?? '-',
                    // Asset & Lokasi
                    'assetnum' => $sr->assetnum ?? '-',
                    'asset_description' => $assetDescription,
                    'location' => $sr->location ?? '-',
                    'location_description' => $locationDescription,
                    'siteid' => $sr->siteid ?? '-',
                    'orgid' => $sr->orgid ?? '-',
                    // People
                    'reportedby' => $sr->reportedby ?? '-',
                    'reportedby_name' => $reportedByName,
                    'reportdate' => $fmtDate($sr->reportdate ?? null),
                    'affectedperson' => $sr->affectedperson ?? '-',
                    'affectedperson_name' => $affectedPersonName,
                    'affecteddate' => $fmtDate($sr->affecteddate ?? null),
                    'changeby' => $sr->changeby ?? '-',
                    'changedate' => $fmtDate($sr->changedate ?? null),
                    'owner' => $sr->owner ?? '-',
                    'ownergroup' => $sr->ownergroup ?? '-',
                    'supervisor' => $sr->supervisor ?? '-',
                    // Tanggal
                    'targetstart' => $fmtDate($sr->targetstart ?? null),
                    'targetfinish' => $fmtDate($sr->targetfinish ?? null),
                    'actualstart' => $fmtDate($sr->actualstart ?? null),
                    'actualfinish' => $fmtDate($sr->actualfinish ?? null),
                    // Kerja
                    'shift' => $sr->shift ?? '-',
                    'workgroup' => $sr->workgroup ?? '-',
                    'oprgroup' => $sr->oprgroup ?? '-',
                    'glaccount' => $sr->glaccount ?? '-',
                    // Actual Cost
                    'actlabcost' => $sr->actlabcost ?? 0,
                    'actlabhrs' => $sr->actlabhrs ?? 0,
                    // Flags
                    'needdt' => $sr->needdt ?? '-',
                    'needdowntime' => $sr->needdowntime ?? 0,
                    'needloto' => $sr->needloto ?? 0,
                    'needecp' => $sr->needecp ?? 0,
                    'needengaln' => $sr->needengaln ?? 0,
                    'needsafapp' => $sr->needsafapp ?? 0,
                    'historyflag' => $sr->historyflag ?? 0,
                    'hasld' => $sr->hasld ?? 0,
                    // Codes
                    'failurecode' => $sr->failurecode ?? '-',
                    'problemcode' => $sr->problemcode ?? '-',
                    'origrecordid' => $sr->origrecordid ?? '-',
                    'origrecordclass' => $sr->origrecordclass ?? '-',
                    // Solution
                    'solution' => $sr->solution ?? '-',
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
    private function formatWorkOrders($workOrders, $unitStatuses = null)
    {
        return collect($workOrders)->map(function ($wo) use ($unitStatuses) {
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
                // Tambahkan info jobcard untuk tombol download
                'jobcard_exists' => $jobcardExists,
                'jobcard_path' => $jobcardPath,
                'jobcard_url' => $jobcardUrl,
                'status_unit' => isset($unitStatuses[$wonum]) ? $unitStatuses[$wonum]->status_unit : '-',
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
                'faultpriority' => $sr->faultpriority ?? '-',
                'faultype'    => $sr->faultype ?? '-',
            ];
        });
    }

    public function generateJobcard(Request $request)
    {
        try {
            $wonum = $request->input('wonum');
            $returnQuery = [
                'wo_page' => $request->input('wo_page', 1),
                'sr_page' => $request->input('sr_page', 1),
                'search' => $request->input('search'),
                'wo_status' => $request->input('wo_status'),
                'wo_worktype' => $request->input('wo_worktype'),
            ];
            
            if (!$wonum) {
                return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'WONUM tidak valid.');
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
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', $wonum)
                ->first();

            if (!$wo) {
                return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'Work Order tidak ditemukan.');
            }

            // Cek apakah status adalah APPR
            if (strtoupper($wo->status ?? '') !== 'APPR') {
                return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.');
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

            // Cara 1: Cek ORIGRECORDID pada WORKORDER (jika WO dibuat dari SR)
            if (!empty($wo->origrecordid) && strtoupper($wo->origrecordclass ?? '') === 'SR') {
                $srTicketId = $wo->origrecordid;
            }

            // Cara 2: Fallback - cari di tabel RELATEDRECORD
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

            // Jika SR ditemukan, ambil data lengkap SR
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
                        // Ambil nama person untuk SR reported by
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

                        // Ambil long description SR (detail: gejala, dampak, resiko, tindakan)
                        $srLongDesc = '-';
                        $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION', 'TICKETLONGDESC'];
                        
                        // Coba berdasarkan LDKEY = TICKETID
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

                        // Jika masih belum ketemu, coba via TICKETUID
                        if ($srLongDesc === '-' && isset($sr->ticketuid)) {
                            foreach ($ldTables as $ldTable) {
                                try {
                                    // Kadang LDKEY mengacu ke TICKETUID untuk TICKET (termasuk SR)
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
                    // Cari nama person dari laborcode
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
            // 7. Ambil child tasks (child WO dimana PARENT = WONUM)
            // =====================================================
            $tasks = [];
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
                        'LOCATION'
                    ])
                    ->where('PARENT', $wonum)
                    ->where('SITEID', 'KD')
                    ->orderBy('WONUM')
                    ->get();

                foreach ($childWOs as $child) {
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
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch child tasks', ['parent' => $wonum, 'error' => $e->getMessage()]);
            }

            // =====================================================
            // 8. Format data lengkap untuk PDF
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
                // Field tambahan dari Maximo
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
            ];

            // Generate PDF dengan data lengkap
            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', [
                'wo' => $woData,
                'sr' => $srData,
                'tasks' => $tasks,
            ]);

            // Simpan PDF ke storage public dengan nama deterministik (tanpa DB tambahan)
            // 1 WO = 1 file jobcard yang selalu di-overwrite
            $directory = 'jobcards';
            $filename = 'JOBCARD_' . $wonum . '.pdf';
            $filePath = $directory . '/' . $filename;
            
            // Pastikan directory ada
            Storage::disk('public')->makeDirectory($directory);
            
            // Simpan / overwrite PDF
            Storage::disk('public')->put($filePath, $pdf->output());

            // Redirect dengan success message
            return redirect()->route('admin.maximo.index', $returnQuery)
                ->with('success', 'Jobcard berhasil di-generate! (Tersimpan di server: ' . $filename . ')');

        } catch (QueryException $e) {
            Log::error('ORACLE QUERY ERROR (GENERATE JOBCARD)', [
                'oracle_code' => $e->errorInfo[1] ?? null,
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);
            return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'Gagal mengambil data Work Order untuk generate jobcard.');
        } catch (\Throwable $e) {
            Log::error('ERROR GENERATE JOBCARD', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->route('admin.maximo.index', $returnQuery ?? [])->with('error', 'Gagal generate jobcard: ' . $e->getMessage());
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

    public function previewJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');

            if (!$filePath || !Storage::disk('public')->exists($filePath)) {
                return redirect()->route('admin.maximo.index')->with('error', 'File jobcard tidak ditemukan.');
            }

            $filename = basename($filePath);
            return Storage::disk('public')->response($filePath, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Throwable $e) {
            Log::error('ERROR PREVIEW JOBCARD', [
                'message' => $e->getMessage(),
            ]);
            return redirect()->route('admin.maximo.index')->with('error', 'Gagal preview jobcard.');
        }
    }

    public function updateJobcard(Request $request)
    {
        try {
            $filePath = $request->input('path');
            
            if (!$filePath) {
                return response()->json(['success' => false, 'message' => 'Path tidak valid.']);
            }

            if (!$request->hasFile('document')) {
                return response()->json(['success' => false, 'message' => 'File tidak ditemukan.']);
            }

            $file = $request->file('document');
            
            // Validasi file PDF
            if ($file->getClientOriginalExtension() !== 'pdf') {
                return response()->json(['success' => false, 'message' => 'File harus berformat PDF.']);
            }

            // Simpan file yang sudah di-edit
            Storage::disk('public')->put($filePath, file_get_contents($file));

            return response()->json([
                'success' => true,
                'message' => 'Jobcard berhasil diupdate!'
            ]);

        } catch (\Throwable $e) {
            Log::error('ERROR UPDATE JOBCARD', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal update jobcard: ' . $e->getMessage()]);
        }
    }


}