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
        $wonum = strtoupper(trim($request->input('wonum', '')));
        $results = [];
        $error = null;
        $hazardDebug = null;

        if ($wonum !== '') {
            $hazardDebug = $this->fetchHazardDebugData($wonum);
        }

        if ($value) {
            try {
                $queryValue = is_numeric($value) ? (int) $value : $value;

                $query = DB::connection('oracle')->table($table);
                if ($column) {
                    $query->where($column, $queryValue);
                }
                $results = $query->take(20)->get();

                $results = $results->map(function ($item) {
                    return (object) $this->normalizeOracleRow($item);
                });
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('pemeliharaan.debug-oracle', compact(
            'table',
            'column',
            'value',
            'wonum',
            'results',
            'error',
            'hazardDebug'
        ));
    }

    public function jobcard(Request $request, $wonum)
    {
        $hazardDebug = $this->fetchHazardDebugData($wonum);

        $wonum = $hazardDebug['requested_wonum'];
        $allWOs = $hazardDebug['all_wonums'];

        $wplabor = $this->fetchWoTableRows('WPLABOR', $allWOs);
        $wosafetylink = $this->fetchWoTableRows('WOSAFETYLINK', $allWOs);
        $wosafetyplan = $this->fetchWoTableRows('WOSAFETYPLAN', $allWOs);

        $hazardError = null;
        foreach ($hazardDebug['tables'] as $meta) {
            if (! empty($meta['error'])) {
                $hazardError = $meta['error'];
                break;
            }
        }

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'requested_wonum' => $wonum,
                'all_related_wonums' => $allWOs,
                'wplabor_data' => $wplabor['error'] ? ['error' => $wplabor['error']] : $wplabor['rows'],
                'hazard_data' => $hazardDebug['summary'],
                'hazard_tables' => $hazardDebug['tables'],
                'hazard_error' => $hazardError,
                'child_wo_error' => $hazardDebug['child_wo_error'],
                'wosafetylink_data' => $wosafetylink['error'] ? ['error' => $wosafetylink['error']] : $wosafetylink['rows'],
                'wosafetyplan_data' => $wosafetyplan['error'] ? ['error' => $wosafetyplan['error']] : $wosafetyplan['rows'],
            ], 200, [], JSON_PRETTY_PRINT);
        }

        return view('pemeliharaan.debug-jobcard', [
            'wonum' => $wonum,
            'allWonums' => $allWOs,
            'childWoError' => $hazardDebug['child_wo_error'],
            'hazardDebug' => $hazardDebug,
            'wplabor' => $wplabor,
            'wosafetylink' => $wosafetylink,
            'wosafetyplan' => $wosafetyplan,
        ]);
    }

    private function fetchWoTableRows(string $table, array $allWOs): array
    {
        try {
            $rows = DB::connection('oracle')->table($table)
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();

            return [
                'rows' => $this->normalizeOracleRows($rows),
                'error' => null,
            ];
        } catch (\Exception $e) {
            return [
                'rows' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    private function fetchHazardDebugData(string $wonum): array
    {
        $wonum = strtoupper(trim($wonum));
        $allWOs = [$wonum];
        $childWoError = null;

        try {
            $children = DB::connection('oracle')->table('WORKORDER')
                ->select('WONUM')
                ->where('PARENT', $wonum)
                ->where('SITEID', 'KD')
                ->get();
            foreach ($children as $c) {
                $cArr = $this->normalizeOracleRow($c);
                if (! empty($cArr['wonum'])) {
                    $allWOs[] = $cArr['wonum'];
                }
            }
            $allWOs = array_values(array_unique($allWOs));
        } catch (\Exception $e) {
            $childWoError = $e->getMessage();
        }

        $tables = [
            'WOHAZARD' => ['rows' => [], 'error' => null],
            'HAZARD' => ['rows' => [], 'error' => null],
            'WOHAZARDPREC' => ['rows' => [], 'error' => null],
            'HAZARDPREC' => ['rows' => [], 'error' => null],
            'PRECAUTION' => ['rows' => [], 'error' => null],
        ];

        $hazardIds = [];

        try {
            $woHazards = DB::connection('oracle')->table('WOHAZARD')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();
            $tables['WOHAZARD']['rows'] = $this->normalizeOracleRows($woHazards);
            foreach ($tables['WOHAZARD']['rows'] as $row) {
                if (! empty($row['hazardid'])) {
                    $hazardIds[] = $row['hazardid'];
                }
            }
        } catch (\Exception $e) {
            $tables['WOHAZARD']['error'] = $e->getMessage();
        }

        $hazardIds = array_values(array_unique($hazardIds));

        if ($hazardIds !== []) {
            try {
                $hazards = DB::connection('oracle')->table('HAZARD')
                    ->whereIn('HAZARDID', $hazardIds)
                    ->get();
                $tables['HAZARD']['rows'] = $this->normalizeOracleRows($hazards);
            } catch (\Exception $e) {
                $tables['HAZARD']['error'] = $e->getMessage();
            }
        }

        $precautionIds = [];

        try {
            $woPrecs = DB::connection('oracle')->table('WOHAZARDPREC')
                ->whereIn('WONUM', $allWOs)
                ->get();
            $tables['WOHAZARDPREC']['rows'] = $this->normalizeOracleRows($woPrecs);
            foreach ($tables['WOHAZARDPREC']['rows'] as $row) {
                if (! empty($row['precautionid'])) {
                    $precautionIds[] = $row['precautionid'];
                }
            }
        } catch (\Exception $e) {
            $tables['WOHAZARDPREC']['error'] = $e->getMessage();
        }

        if ($hazardIds !== []) {
            try {
                $hazardPrecs = DB::connection('oracle')->table('HAZARDPREC')
                    ->whereIn('HAZARDID', $hazardIds)
                    ->get();
                $tables['HAZARDPREC']['rows'] = $this->normalizeOracleRows($hazardPrecs);
                foreach ($tables['HAZARDPREC']['rows'] as $row) {
                    if (! empty($row['precautionid'])) {
                        $precautionIds[] = $row['precautionid'];
                    }
                }
            } catch (\Exception $e) {
                $tables['HAZARDPREC']['error'] = $e->getMessage();
            }
        }

        $precautionIds = array_values(array_unique($precautionIds));

        if ($precautionIds !== []) {
            try {
                $precs = DB::connection('oracle')->table('PRECAUTION')
                    ->whereIn('PRECAUTIONID', $precautionIds)
                    ->get();
                $tables['PRECAUTION']['rows'] = $this->normalizeOracleRows($precs);
            } catch (\Exception $e) {
                $tables['PRECAUTION']['error'] = $e->getMessage();
            }
        }

        $hazardById = collect($tables['HAZARD']['rows'])->keyBy('hazardid');
        $precById = collect($tables['PRECAUTION']['rows'])->keyBy('precautionid');

        $summary = [];
        foreach ($tables['WOHAZARD']['rows'] as $wh) {
            $hazardId = $wh['hazardid'] ?? '';
            $hazardRec = $hazardById->get($hazardId);
            $hazardDesc = $hazardRec['description'] ?? $hazardId;

            $precautions = [];
            $precSource = 'WOHAZARDPREC';
            $woPrecRows = collect($tables['WOHAZARDPREC']['rows'])
                ->filter(fn ($r) => ($r['hazardid'] ?? '') === $hazardId);

            if ($woPrecRows->isEmpty()) {
                $precSource = 'HAZARDPREC';
                $woPrecRows = collect($tables['HAZARDPREC']['rows'])
                    ->filter(fn ($r) => ($r['hazardid'] ?? '') === $hazardId);
            }

            foreach ($woPrecRows as $wp) {
                $precId = $wp['precautionid'] ?? '';
                if ($precId === '') {
                    continue;
                }
                $precRec = $precById->get($precId);
                $precautions[] = [
                    'precautionid' => $precId,
                    'description' => $precRec['description'] ?? $precId,
                    'source' => $precSource,
                ];
            }

            $summary[] = [
                'wonum' => $wh['wonum'] ?? '',
                'hazardid' => $hazardId,
                'description' => $hazardDesc,
                'precautions' => $precautions,
            ];
        }

        return [
            'requested_wonum' => $wonum,
            'all_wonums' => $allWOs,
            'child_wo_error' => $childWoError,
            'tables' => $tables,
            'summary' => $summary,
        ];
    }

    private function normalizeOracleRows($rows): array
    {
        return collect($rows)->map(fn ($item) => $this->normalizeOracleRow($item))->all();
    }

    private function normalizeOracleRow($item): array
    {
        $arr = array_change_key_case((array) $item, CASE_LOWER);
        foreach ($arr as $k => $v) {
            if (is_resource($v)) {
                $arr[$k] = stream_get_contents($v);
            } elseif (is_object($v) && method_exists($v, 'load')) {
                $arr[$k] = $v->load();
            }
        }

        return $arr;
    }
}
