<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugOracleController extends Controller
{
    public function index(Request $request)
    {
        $table = strtoupper($request->input('table', 'LONGDESCRIPTION'));
        $column = strtoupper($request->input('column', 'LDKEY'));
        $value = $request->input('value', '');
        $results = [];
        $error = null;

        if ($value) {
            try {
                // Determine if value is numeric or string
                $queryValue = is_numeric($value) ? (int)$value : $value;

                $query = DB::connection('oracle')->table($table);
                if ($column) {
                    // Try exact match first
                    $query->where($column, $queryValue);
                }
                $results = $query->take(20)->get();

                // Convert any CLOB/resources to string
                $results = $results->map(function ($item) {
                    $arr = (array) $item;
                    foreach ($arr as $k => $v) {
                        if (is_resource($v)) {
                            $arr[$k] = stream_get_contents($v);
                        } elseif (is_object($v) && method_exists($v, 'load')) {
                            $arr[$k] = $v->load(); // for Oracle OCI-Lob objects
                        }
                    }
                    return (object) $arr;
                });
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('pemeliharaan.debug-oracle', compact('table', 'column', 'value', 'results', 'error'));
    }

    public function jobcard($wonum)
    {
        $wonum = strtoupper($wonum);
        $allWOs = [$wonum];
        
        // Ambil child WOs
        try {
            $children = DB::connection('oracle')->table('WORKORDER')
                ->select('WONUM')
                ->where('PARENT', $wonum)
                ->where('SITEID', 'KD')
                ->get();
            foreach ($children as $c) {
                $allWOs[] = $c->wonum;
            }
        } catch (\Exception $e) {}

        // WPLABOR
        $wplabors = [];
        try {
            $wplabors = DB::connection('oracle')->table('WPLABOR')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();
        } catch (\Exception $e) {
            $wplabors = ['error' => $e->getMessage()];
        }

        // WOHAZARD
        $hazards = [];
        try {
            $woHazards = DB::connection('oracle')->table('WOHAZARD')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();
                
            foreach ($woHazards as $hz) {
                $hazardId = $hz->hazardid;
                $hazardDesc = $hazardId;
                
                try {
                    $h = DB::connection('oracle')->table('HAZARD')->where('HAZARDID', $hazardId)->first();
                    if ($h) $hazardDesc = $h->description ?? $hazardId;
                } catch (\Exception $e) {}
                
                $precautions = [];
                try {
                    $woPrecs = DB::connection('oracle')->table('WOHAZARDPREC')
                        ->whereIn('WONUM', $allWOs)
                        ->where('HAZARDID', $hazardId)
                        ->get();
                        
                    if ($woPrecs->isEmpty()) {
                        $woPrecs = DB::connection('oracle')->table('HAZARDPREC')
                            ->where('HAZARDID', $hazardId)
                            ->get();
                    }
                    
                    foreach($woPrecs as $wp) {
                        $precId = $wp->precautionid;
                        $precDesc = $precId;
                        try {
                            $p = DB::connection('oracle')->table('PRECAUTION')->where('PRECAUTIONID', $precId)->first();
                            if ($p) $precDesc = $p->description ?? $precId;
                        } catch (\Exception $e) {}
                        $precautions[] = ['precautionid' => $precId, 'description' => $precDesc];
                    }
                } catch (\Exception $e) {}
                
                $hazards[] = [
                    'wonum' => $hz->wonum,
                    'hazardid' => $hazardId,
                    'description' => $hazardDesc,
                    'precautions' => $precautions
                ];
            }
        } catch (\Exception $e) {
            $hazards = ['error' => $e->getMessage()];
        }

        // Coba langsung tembak ke tabel WOSAFETYLINK atau tabel safety standar lainnya
        $safetyLinkData = [];
        try {
            $safetyLinkData = DB::connection('oracle')->table('WOSAFETYLINK')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();
        } catch (\Exception $e) {
            $safetyLinkData = ['error' => $e->getMessage()];
        }
        
        $woSafetyPlan = [];
        try {
            $woSafetyPlan = DB::connection('oracle')->table('WOSAFETYPLAN')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();
        } catch (\Exception $e) {
            $woSafetyPlan = ['error' => $e->getMessage()];
        }

        return response()->json([
            'requested_wonum' => $wonum,
            'all_related_wonums' => $allWOs,
            'wplabor_data' => $wplabors,
            'hazard_data_wohazard_error' => $hazards['error'] ?? null,
            'wosafetylink_data' => $safetyLinkData,
            'wosafetyplan_data' => $woSafetyPlan
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
