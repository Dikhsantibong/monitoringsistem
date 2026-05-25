<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class MaximoJobcardHazards
{
    /**
     * Ambil hazard & precaution untuk jobcard (struktur sama dengan hazard_data di debug-jobcard).
     *
     * @return array<int, array{wonum: string, hazardid: string, description: string, precautions: array<int, array{precautionid: string, description: string}>}>
     */
    public static function fetch(array $allWOs): array
    {
        if ($allWOs === []) {
            return [];
        }

        $summary = [];

        try {
            $woHazards = DB::connection('oracle')->table('WOHAZARD')
                ->whereIn('WONUM', $allWOs)
                ->where('SITEID', 'KD')
                ->get();

            foreach ($woHazards as $hz) {
                $hzArr = self::normalizeRow($hz);
                $hazardId = $hzArr['hazardid'] ?? '';
                if ($hazardId === '') {
                    continue;
                }

                $hazardDesc = $hazardId;
                try {
                    $hazardRec = DB::connection('oracle')->table('HAZARD')->where('HAZARDID', $hazardId)->first();
                    if ($hazardRec) {
                        $hRecArr = self::normalizeRow($hazardRec);
                        $hazardDesc = $hRecArr['description'] ?? $hazardId;
                    }
                } catch (\Exception $e) {
                }

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

                    foreach ($woPrecs as $wp) {
                        $wpArr = self::normalizeRow($wp);
                        $precId = $wpArr['precautionid'] ?? '';
                        if ($precId === '') {
                            continue;
                        }
                        $precDesc = $precId;
                        try {
                            $precRec = DB::connection('oracle')->table('PRECAUTION')->where('PRECAUTIONID', $precId)->first();
                            if ($precRec) {
                                $pRecArr = self::normalizeRow($precRec);
                                $precDesc = $pRecArr['description'] ?? $precId;
                            }
                        } catch (\Exception $e) {
                        }
                        $precautions[] = [
                            'precautionid' => $precId,
                            'description' => $precDesc,
                        ];
                    }
                } catch (\Exception $e) {
                }

                $summary[] = [
                    'wonum' => $hzArr['wonum'] ?? '',
                    'hazardid' => $hazardId,
                    'description' => $hazardDesc,
                    'precautions' => $precautions,
                ];
            }
        } catch (\Exception $e) {
            return [];
        }

        return $summary;
    }

    private static function normalizeRow($item): array
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
