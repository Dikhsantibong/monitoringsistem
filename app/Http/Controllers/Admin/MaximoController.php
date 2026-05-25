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
use App\Support\MaximoJobcardHazards;

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
                    'WORKLOCATION',
                    'HASCHILDREN', 'OUTLABCOST', 'OUTMATCOST', 'OUTTOOLCOST', 'HISTORYFLAG',
                    'CONTRACT', 'WOPRIORTEXT', 'TARGCOMPDATE', 'TARGSTARTDATE',
                    'WOEQ1', 'WOEQ2', 'WOEQ3', 'WOEQ4', 'WOEQ5', 'WOEQ6',
                    'WOEQ7', 'WOEQ8', 'WOEQ9', 'WOEQ10', 'WOEQ11', 'WOEQ12', 'WOEQ13', 'WOEQ14',
                    'REPORTEDBY', 'REPORTDATE', 'PROBLEMCODE', 'DOWNTIME',
                    'ACTSTART', 'ACTFINISH', 'SCHEDSTART', 'SCHEDFINISH',
                    'REMDUR', 'CREWID', 'SUPERVISOR', 'FAILURECODE',
                    'ESTSERVCOST', 'ACTSERVCOST', 'ORGID', 'SITEID',
                    'WOCLASS', 'OWNER', 'OWNERGROUP', 'PERSONGROUP', 'LEAD',
                    'ORIGRECORDID', 'ORIGRECORDCLASS', 'GLACCOUNT',
                    'ANGGARAN', 'WONUMPLN',
                    'REMARKDESCC', 'REMARKDESCP', 'REMARKDESCPLN', 'REMARKDESCR',
                    'PROBLEMCODEPLN', 'ACTIONCODEPLN', 'CAUSECODEPLN', 'FAILURECODEPLN',
                    'RISK', 'ENVIRONMENT', 'BACKOUTPLAN', 'JUSTIFYPRIORITY',
                    'MATCOSTELLIPSE', 'SERVCOSTELLIPSE', 'TOTALCOSTELLIPSE',
                    'MATCOSTWO', 'SERVCOSTWO', 'TOTALCOSTWO',
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
                    'wopriortext' => $wo->wopriortext ?? '-',
                    'woclass' => $wo->woclass ?? '-',
                    'description' => $wo->description ?? '-',
                    // Asset & Lokasi
                    'assetnum' => $wo->assetnum ?? '-',
                    'location' => $wo->location ?? '-',
                    'worklocation' => $wo->worklocation ?? '-',
                    'siteid' => $wo->siteid ?? '-',
                    'orgid' => $wo->orgid ?? '-',
                    'downtime' => $wo->downtime ?? '-',
                    'risk' => $wo->risk ?? '-',
                    'environment' => $wo->environment ?? '-',
                    'backoutplan' => $wo->backoutplan ?? '-',
                    'justifypriority' => $wo->justifypriority ?? '-',
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
                    'matcostellipse' => $wo->matcostellipse ?? 0,
                    'servcostellipse' => $wo->servcostellipse ?? 0,
                    'totalcostellipse' => $wo->totalcostellipse ?? 0,
                    'matcostwo' => $wo->matcostwo ?? 0,
                    'servcostwo' => $wo->servcostwo ?? 0,
                    'totalcostwo' => $wo->totalcostwo ?? 0,
                    // Codes
                    'jpnum' => $wo->jpnum ?? '-',
                    'pmnum' => $wo->pmnum ?? '-',
                    'failurecode' => $wo->failurecode ?? '-',
                    'problemcode' => $wo->problemcode ?? '-',
                    'problemcodepln' => $wo->problemcodepln ?? '-',
                    'actioncodepln' => $wo->actioncodepln ?? '-',
                    'causecodepln' => $wo->causecodepln ?? '-',
                    'failurecodepln' => $wo->failurecodepln ?? '-',
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
                    'woeq7' => is_numeric($wo->woeq7 ?? null) ? number_format($wo->woeq7, 2) : ($wo->woeq7 ?? '-'),
                    'woeq8' => $wo->woeq8 ?? '-',
                    'woeq9' => $wo->woeq9 ?? '-',
                    'woeq10' => $wo->woeq10 ?? '-',
                    'woeq11' => $wo->woeq11 ?? '-',
                    'woeq12' => is_numeric($wo->woeq12 ?? null) ? number_format($wo->woeq12, 2) : ($wo->woeq12 ?? '-'),
                    'woeq13' => $fmtDate($wo->woeq13 ?? null),
                    'woeq14' => is_numeric($wo->woeq14 ?? null) ? number_format($wo->woeq14, 2) : ($wo->woeq14 ?? '-'),
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
                    // Global & Template
                    'globalticketid' => $sr->globalticketid ?? '-',
                    'globalticketclass' => $sr->globalticketclass ?? '-',
                    'isglobal' => $sr->isglobal ?? 0,
                    'relatedtoglobal' => $sr->relatedtoglobal ?? 0,
                    'template' => $sr->template ?? 0,
                    'templateid' => $sr->templateid ?? '-',
                    // Contacts
                    'affectedphone' => $sr->affectedphone ?? '-',
                    'affectedemail' => $sr->affectedemail ?? '-',
                    'reportedphone' => $sr->reportedphone ?? '-',
                    'reportedemail' => $sr->reportedemail ?? '-',
                    'reportedpriority' => $sr->reportedpriority ?? '-',
                    'urgency' => $sr->urgency ?? '-',
                    'targetcontactdate' => $fmtDate($sr->targetcontactdate ?? null),
                    // Specifics
                    'fr1code' => $sr->fr1code ?? '-',
                    'fr2code' => $sr->fr2code ?? '-',
                    'fieldcba' => $sr->fieldcba ?? '-',
                    'fieldfmea' => $sr->fieldfmea ?? '-',
                    'fieldlcca' => $sr->fieldlcca ?? '-',
                    'fieldrcfa' => $sr->fieldrcfa ?? '-',
                    'vendor' => $sr->vendor ?? '-',
                    'sitevisit' => $sr->sitevisit ?? 0,
                    'isknownerror' => $sr->isknownerror ?? 0,
                    'commodity' => $sr->commodity ?? '-',
                    'commoditygroup' => $sr->commoditygroup ?? '-',
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
                'wopriority'  => $wo->wopriortext ?? '-',
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
                    'WOPRIORTEXT',
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
                    'OWNERGROUP',
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
                return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'Work Order tidak ditemukan.');
            }

            // Normalisasi keys ke lowercase untuk Oracle compatibility
            $woArr = array_change_key_case((array) $wo, CASE_LOWER);

            // Cek apakah status adalah APPR
            if (strtoupper($woArr['status'] ?? '') !== 'APPR') {
                return redirect()->route('admin.maximo.index', $returnQuery)->with('error', 'Jobcard hanya dapat di-generate untuk Work Order dengan status APPR.');
            }

            // =====================================================
            // 2. Ambil Asset Description dari tabel ASSET
            // =====================================================
            $assetDescription = '-';
            if (!empty($woArr['assetnum'])) {
                try {
                    $asset = DB::connection('oracle')
                        ->table('ASSET')
                        ->select(['DESCRIPTION'])
                        ->where('ASSETNUM', $woArr['assetnum'])
                        ->where('SITEID', 'KD')
                        ->first();
                    $assetDescription = $asset->description ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch ASSET description', ['assetnum' => $woArr['assetnum'], 'error' => $e->getMessage()]);
                }
            }

            // =====================================================
            // 3. Ambil Location Description dari tabel LOCATIONS
            // =====================================================
            $locationDescription = '-';
            if (!empty($woArr['location'])) {
                try {
                    $location = DB::connection('oracle')
                        ->table('LOCATIONS')
                        ->select(['DESCRIPTION'])
                        ->where('LOCATION', $woArr['location'])
                        ->where('SITEID', 'KD')
                        ->first();
                    $locationDescription = $location->description ?? '-';
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch LOCATIONS description', ['location' => $woArr['location'], 'error' => $e->getMessage()]);
                }
            }

            // =====================================================
            // 4. Cari linked Service Request
            // =====================================================
            $srData = null;
            $srTicketId = null;

            // Cara 1: Cek ORIGRECORDID pada WORKORDER (jika WO dibuat dari SR)
            if (!empty($woArr['origrecordid']) && strtoupper($woArr['origrecordclass'] ?? '') === 'SR') {
                $srTicketId = $woArr['origrecordid'];
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
                                    ->whereIn('LDOWNERTABLE', ['SR', 'TICKET'])
                                    ->first();
                                if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                    $val = $longDesc->ldtext;
                                    if (is_resource($val)) $val = stream_get_contents($val);
                                    elseif (is_object($val) && method_exists($val, 'load')) $val = $val->load();
                                    
                                    if (is_string($val) && trim($val) !== '') {
                                        $srLongDesc = $val;
                                        break;
                                    }
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
                                        ->whereIn('LDOWNERTABLE', ['SR', 'TICKET'])
                                        ->first();
                                    if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                        $val = $longDesc->ldtext;
                                        if (is_resource($val)) $val = stream_get_contents($val);
                                        elseif (is_object($val) && method_exists($val, 'load')) $val = $val->load();
                                        
                                        if (is_string($val) && trim($val) !== '') {
                                            $srLongDesc = $val;
                                            break;
                                        }
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
            // 6.5. Ambil Long Description untuk PARENT WO
            // =====================================================
            $woLongDesc = '-';
            if (!empty($woArr['workorderid'])) {
                $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION'];
                foreach ($ldTables as $ldTable) {
                    try {
                        $longDesc = DB::connection('oracle')
                            ->table($ldTable)
                            ->select(['LDTEXT'])
                            ->where('LDKEY', $woArr['workorderid'])
                            ->whereIn('LDOWNERTABLE', ['WORKORDER', 'WO'])
                            ->first();
                        if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                            $val = $longDesc->ldtext;
                            if (is_resource($val)) $val = stream_get_contents($val);
                            elseif (is_object($val) && method_exists($val, 'load')) $val = $val->load();
                            
                            if (is_string($val) && trim($val) !== '') {
                                $woLongDesc = $val;
                                break;
                            }
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
            $allWOs = [$wonum]; // Menyimpan parent dan semua child wonum untuk WPLABOR
            
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
                        'WOPRIORTEXT',
                        'ASSETNUM',
                        'LOCATION',
                        'WORKORDERID',
                        'LEAD'
                    ])
                    ->where('PARENT', $wonum)
                    ->where('SITEID', 'KD')
                    ->orderBy('WONUM')
                    ->get();

                foreach ($childWOs as $child) {
                    $childArr = array_change_key_case((array) $child, CASE_LOWER);
                    $allWOs[] = $childArr['wonum'];
                    
                    // Cek assignment task
                    $taskAssign = '-';
                    try {
                        $ta = DB::connection('oracle')
                            ->table('ASSIGNMENT')
                            ->select(['LABORCODE'])
                            ->where('WONUM', $childArr['wonum'])
                            ->where('SITEID', 'KD')
                            ->first();
                        if ($ta && !empty($ta->laborcode)) {
                            $taskAssign = $ta->laborcode;
                        }
                    } catch (\Exception $e) {}

                    // Fallback: gunakan LEAD dari WORKORDER jika ASSIGNMENT kosong
                    if ($taskAssign === '-' && !empty($childArr['lead'])) {
                        $taskAssign = $childArr['lead'];
                    }

                    // Cek Asset & Location Description untuk task
                    $taskAssetDesc = '-';
                    $taskLocDesc = '-';
                    if (!empty($childArr['assetnum'])) {
                        try {
                            $ast = DB::connection('oracle')
                                ->table('ASSET')
                                ->select(['DESCRIPTION'])
                                ->where('ASSETNUM', $childArr['assetnum'])
                                ->where('SITEID', 'KD')
                                ->first();
                            $taskAssetDesc = $ast->description ?? '-';
                        } catch (\Exception $e) {}
                    }
                    if (!empty($childArr['location'])) {
                        try {
                            $loc = DB::connection('oracle')
                                ->table('LOCATIONS')
                                ->select(['DESCRIPTION'])
                                ->where('LOCATION', $childArr['location'])
                                ->where('SITEID', 'KD')
                                ->first();
                            $taskLocDesc = $loc->description ?? '-';
                        } catch (\Exception $e) {}
                    }

                    // Cek Long Description untuk task WT
                    $taskLongDesc = '-';
                    if (!empty($childArr['workorderid'])) {
                        $ldTables = ['LONGDESCRIPTION', 'LONG_DESCRIPTION'];
                        foreach ($ldTables as $ldTable) {
                            try {
                                $longDesc = DB::connection('oracle')
                                    ->table($ldTable)
                                    ->select(['LDTEXT'])
                                    ->where('LDKEY', $childArr['workorderid'])
                                    ->whereIn('LDOWNERTABLE', ['WORKORDER', 'JOBTASK', 'WO', 'WOACTIVITY'])
                                    ->first();
                                if ($longDesc && isset($longDesc->ldtext) && $longDesc->ldtext) {
                                    $val = $longDesc->ldtext;
                                    if (is_resource($val)) $val = stream_get_contents($val);
                                    elseif (is_object($val) && method_exists($val, 'load')) $val = $val->load();
                                    
                                    if (is_string($val) && trim($val) !== '') {
                                        $taskLongDesc = $val;
                                        break;
                                    }
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }

                    $tasks[] = [
                        'wonum' => $childArr['wonum'] ?? '-',
                        'description' => $childArr['description'] ?? '-',
                        'status' => $childArr['status'] ?? '-',
                        'schedstart' => !empty($childArr['schedstart']) ? Carbon::parse($childArr['schedstart'])->format('d-m-Y H:i') : '-',
                        'schedfinish' => !empty($childArr['schedfinish']) ? Carbon::parse($childArr['schedfinish'])->format('d-m-Y H:i') : '-',
                        'actfinish' => !empty($childArr['actfinish']) ? Carbon::parse($childArr['actfinish'])->format('d-m-Y H:i') : '-',
                        'persongroup' => !empty($childArr['persongroup']) ? $childArr['persongroup'] : ($woArr['ownergroup'] ?? '-'),
                        'siteid' => $childArr['siteid'] ?? '-',
                        'targstartdate' => !empty($childArr['targstartdate']) ? Carbon::parse($childArr['targstartdate'])->format('d-m-Y H:i') : '-',
                        'targcompdate' => !empty($childArr['targcompdate']) ? Carbon::parse($childArr['targcompdate'])->format('d-m-Y H:i') : '-',
                        'parent' => $childArr['parent'] ?? '-',
                        'worktype' => $childArr['worktype'] ?? '-',
                        'reportdate' => !empty($childArr['reportdate']) ? Carbon::parse($childArr['reportdate'])->format('d-m-Y H:i') : '-',
                        'reportedby' => $childArr['reportedby'] ?? '-',
                        'failurecode' => $childArr['failurecode'] ?? '-',
                        'glaccount' => $childArr['glaccount'] ?? '-',
                        'wopriority' => !empty($childArr['wopriortext']) ? $childArr['wopriortext'] : ($woArr['wopriortext'] ?? '-'),
                        'assetnum' => $childArr['assetnum'] ?? '-',
                        'asset_description' => $taskAssetDesc,
                        'location' => $childArr['location'] ?? '-',
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
            // 8.5. Ambil Hazard & Precaution (WOHAZARD / HAZARD)
            // =====================================================
            $hazards = MaximoJobcardHazards::fetch($allWOs);

            // =====================================================
            // 9. Format data lengkap untuk PDF
            // =====================================================
            $woData = [
                'wonum' => $woArr['wonum'] ?? '-',
                'parent' => $woArr['parent'] ?? '-',
                'status' => $woArr['status'] ?? '-',
                'statusdate' => !empty($woArr['statusdate']) ? Carbon::parse($woArr['statusdate'])->format('d-m-Y H:i') : '-',
                'worktype' => $woArr['worktype'] ?? '-',
                'wopriority' => $woArr['wopriortext'] ?? '-',
                'reportdate' => !empty($woArr['reportdate']) ? Carbon::parse($woArr['reportdate'])->format('d-m-Y H:i') : '-',
                'assetnum' => $woArr['assetnum'] ?? '-',
                'location' => $woArr['location'] ?? '-',
                'siteid' => $woArr['siteid'] ?? '-',
                'downtime' => $woArr['downtime'] ?? '-',
                'schedstart' => !empty($woArr['schedstart']) ? Carbon::parse($woArr['schedstart'])->format('d-m-Y H:i') : '-',
                'schedfinish' => !empty($woArr['schedfinish']) ? Carbon::parse($woArr['schedfinish'])->format('d-m-Y H:i') : '-',
                'description' => $woArr['description'] ?? '-',
                // Field tambahan dari Maximo
                'reportedby' => $woArr['reportedby'] ?? '-',
                'reportedby_name' => $woReportedByName,
                'actstart' => !empty($woArr['actstart']) ? Carbon::parse($woArr['actstart'])->format('d-m-Y H:i') : '-',
                'actfinish' => !empty($woArr['actfinish']) ? Carbon::parse($woArr['actfinish'])->format('d-m-Y H:i') : '-',
                'glaccount' => $woArr['glaccount'] ?? '-',
                'failurecode' => $woArr['failurecode'] ?? '-',
                'jpnum' => $woArr['jpnum'] ?? '-',
                'persongroup' => $woArr['persongroup'] ?? '-',
                'targstartdate' => !empty($woArr['targstartdate']) ? Carbon::parse($woArr['targstartdate'])->format('d-m-Y H:i') : '-',
                'targcompdate' => !empty($woArr['targcompdate']) ? Carbon::parse($woArr['targcompdate'])->format('d-m-Y H:i') : '-',
                'lead' => $woArr['lead'] ?? '-',
                'asset_description' => $assetDescription,
                'location_description' => $locationDescription,
                'assigned_to' => $assignedTo,
                'assigned_to_name' => $assignedToName,
                'anggaran' => $woArr['anggaran'] ?? '-',
                'longdescription' => $woLongDesc,
            ];

            // Generate PDF dengan data lengkap
            $pdf = Pdf::loadView('admin.maximo.jobcard-pdf', [
                'wo' => $woData,
                'sr' => $srData,
                'tasks' => $tasks,
                'wplabors' => $wplabors,
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