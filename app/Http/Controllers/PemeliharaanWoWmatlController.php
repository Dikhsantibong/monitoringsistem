<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PemeliharaanLocationHelper;

class PemeliharaanWoWmatlController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        try {
            $query = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%')
                ->where('STATUS', 'WMATL');
            PemeliharaanLocationHelper::applyLocationFilter($query);

            if ($search) {
                $like = "%" . strtoupper($search) . "%";
                $query->where(function($q) use ($like) {
                    $q->where('WONUM', 'LIKE', $like)
                      ->orWhere('DESCRIPTION', 'LIKE', $like);
                });
            }

            $workOrdersPaginator = $query->orderBy('STATUSDATE', 'desc')->paginate(10);
            
            // Format data for view consistency
            $workOrders = collect($workOrdersPaginator->items())->map(function($wo) {
                $wo = (object) array_change_key_case((array) $wo, CASE_LOWER);
                return (object) [
                    'id' => $wo->wonum,
                    'description' => $wo->description,
                    'status' => $wo->status,
                    'labor' => $wo->lead ?? '-', // Using lead if available, otherwise '-'
                    'schedule_start' => $wo->schedstart ? \Carbon\Carbon::parse($wo->schedstart)->format('d/m/Y') : '-',
                    'schedule_finish' => $wo->schedfinish ? \Carbon\Carbon::parse($wo->schedfinish)->format('d/m/Y') : '-',
                ];
            });

            return view('pemeliharaan.wo-wmatl-index', compact('workOrders', 'workOrdersPaginator', 'search'));
        } catch (\Exception $e) {
            Log::error('Error fetching WO WMATL from Oracle: ' . $e->getMessage());
            $workOrders = collect();
            $workOrdersPaginator = null;
            return view('pemeliharaan.wo-wmatl-index', compact('workOrders', 'workOrdersPaginator', 'search'))
                ->with('error', 'Gagal mengambil data dari Oracle: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $wonum = trim($id);
            if ($wonum === '') {
                return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'WONUM tidak valid.');
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
                ->where('WONUM', $wonum);

            // Terapkan filter lokasi untuk keamanan data pemeliharaan
            PemeliharaanLocationHelper::applyLocationFilter($wo);
            
            $woData = $wo->first();

            if (!$woData) {
                return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'Work Order tidak ditemukan atau Anda tidak memiliki akses ke lokasi ini.');
            }

            // Case conversion to lowercase keys for view consistency
            $woData = (object) array_change_key_case((array) $woData, CASE_LOWER);

            // Helper untuk format tanggal
            $fmtDate = function ($val) {
                return isset($val) && $val ? Carbon::parse($val)->format('d-m-Y H:i') : '-';
            };

            $formattedWo = [
                // Identifikasi
                'wonum' => $woData->wonum ?? '-',
                'parent' => $woData->parent ?? '-',
                'status' => $woData->status ?? '-',
                'statusdate' => $fmtDate($woData->statusdate ?? null),
                'worktype' => $woData->worktype ?? '-',
                'wopriority' => $woData->wopriority ?? '-',
                'woclass' => $woData->woclass ?? '-',
                'description' => $woData->description ?? '-',
                // Asset & Lokasi
                'assetnum' => $woData->assetnum ?? '-',
                'location' => $woData->location ?? '-',
                'siteid' => $woData->siteid ?? '-',
                'orgid' => $woData->orgid ?? '-',
                'downtime' => $woData->downtime ?? '-',
                // People
                'reportedby' => $woData->reportedby ?? '-',
                'supervisor' => $woData->supervisor ?? '-',
                'crewid' => $woData->crewid ?? '-',
                'lead' => $woData->lead ?? '-',
                'owner' => $woData->owner ?? '-',
                'ownergroup' => $woData->ownergroup ?? '-',
                'persongroup' => $woData->persongroup ?? '-',
                'changeby' => $woData->changeby ?? '-',
                // Tanggal
                'reportdate' => $fmtDate($woData->reportdate ?? null),
                'schedstart' => $fmtDate($woData->schedstart ?? null),
                'schedfinish' => $fmtDate($woData->schedfinish ?? null),
                'actstart' => $fmtDate($woData->actstart ?? null),
                'actfinish' => $fmtDate($woData->actfinish ?? null),
                'targstartdate' => $fmtDate($woData->targstartdate ?? null),
                'targcompdate' => $fmtDate($woData->targcompdate ?? null),
                'changedate' => $fmtDate($woData->changedate ?? null),
                'faildate' => $fmtDate($woData->faildate ?? null),
                // Estimasi
                'estdur' => $woData->estdur ?? 0,
                'estlabhrs' => $woData->estlabhrs ?? 0,
                'estmatcost' => $woData->estmatcost ?? 0,
                'estlabcost' => $woData->estlabcost ?? 0,
                'esttoolcost' => $woData->esttoolcost ?? 0,
                'estservcost' => $woData->estservcost ?? 0,
                // Aktual
                'actlabhrs' => $woData->actlabhrs ?? 0,
                'actmatcost' => $woData->actmatcost ?? 0,
                'actlabcost' => $woData->actlabcost ?? 0,
                'acttoolcost' => $woData->acttoolcost ?? 0,
                'actservcost' => $woData->actservcost ?? 0,
                // Outside Cost
                'outlabcost' => $woData->outlabcost ?? 0,
                'outmatcost' => $woData->outmatcost ?? 0,
                'outtoolcost' => $woData->outtoolcost ?? 0,
                // Codes
                'jpnum' => $woData->jpnum ?? '-',
                'pmnum' => $woData->pmnum ?? '-',
                'failurecode' => $woData->failurecode ?? '-',
                'problemcode' => $woData->problemcode ?? '-',
                'glaccount' => $woData->glaccount ?? '-',
                'contract' => $woData->contract ?? '-',
                // Flags
                'haschildren' => $woData->haschildren ?? 0,
                'historyflag' => $woData->historyflag ?? 0,
                'remdur' => $woData->remdur ?? 0,
                // Origin
                'origrecordid' => $woData->origrecordid ?? '-',
                'origrecordclass' => $woData->origrecordclass ?? '-',
                // Custom / WOEQ
                'woeq1' => $woData->woeq1 ?? '-',
                'woeq2' => $woData->woeq2 ?? '-',
                'woeq3' => $woData->woeq3 ?? '-',
                'woeq4' => $woData->woeq4 ?? '-',
                'woeq5' => $woData->woeq5 ?? 0,
                'woeq6' => $fmtDate($woData->woeq6 ?? null),
                // PLN Custom
                'anggaran' => $woData->anggaran ?? '-',
                'wonumpln' => $woData->wonumpln ?? '-',
                'remarkdescc' => $woData->remarkdescc ?? '-',
                'remarkdescp' => $woData->remarkdescp ?? '-',
                'remarkdescpln' => $woData->remarkdescpln ?? '-',
                'remarkdescr' => $woData->remarkdescr ?? '-',
            ];

            return view('pemeliharaan.wo-wmatl-edit', ['wo' => $formattedWo]);
        } catch (\Exception $e) {
            Log::error('Error fetching WO WMATL detail from Oracle: ' . $e->getMessage());
            return redirect()->route('pemeliharaan.wo-wmatl.index')->with('error', 'Gagal mengambil detail dari Oracle.');
        }
    }

    public function update(Request $request, $id)
    {
        // Removed local DB updates as per Oracle data transition
        return redirect()->route('pemeliharaan.wo-wmatl.index')->with('info', 'Update dinonaktifkan untuk data Oracle.');
    }
}
