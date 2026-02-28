<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PetaKesehatanUnitController extends Controller
{
    public function index(Request $request)
    {
        $filterMonths = $request->input('months', 6);
        $startDate = Carbon::now()->subMonths($filterMonths)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        try {
            // Base query builder - sama persis dengan KinerjaPemeliharaanController
            $woQuery = DB::connection('oracle')->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');

            // ============================================
            // 1. ASET SERING GANGGUAN (CM) - Group by ASSETNUM, count CM occurrences
            // ============================================
            $cmAssets = collect();
            try {
                $cmAssets = (clone $woQuery)
                    ->select([
                        'ASSETNUM',
                        'LOCATION',
                        DB::raw('COUNT(*) as CM_COUNT'),
                    ])
                    ->where('WORKTYPE', 'CM')
                    ->whereNotNull('ASSETNUM')
                    ->whereBetween('REPORTDATE', [$startDate, $endDate])
                    ->groupBy('ASSETNUM', 'LOCATION')
                    ->orderByRaw('COUNT(*) DESC')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'assetnum' => $item->assetnum ?? $item->ASSETNUM ?? '-',
                            'location' => $item->location ?? $item->LOCATION ?? '-',
                            'cm_count' => $item->cm_count ?? $item->CM_COUNT ?? 0,
                        ];
                    });
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - CM Assets Error: ' . $e->getMessage());
            }

            // ============================================
            // 2. ANTISIPASI PM - Cek aset CM yang punya PM juga
            // ============================================
            $pmCoverage = collect();
            try {
                $cmAssetNums = $cmAssets->pluck('assetnum')->filter(fn($v) => $v !== '-')->unique()->values()->toArray();

                if (!empty($cmAssetNums)) {
                    $pmCoverage = (clone $woQuery)
                        ->select([
                            'ASSETNUM',
                            'LOCATION',
                            DB::raw('COUNT(*) as PM_COUNT'),
                            DB::raw("SUM(CASE WHEN STATUS IN ('COMP','CLOSE') THEN 1 ELSE 0 END) as PM_CLOSED"),
                            DB::raw("SUM(CASE WHEN STATUS NOT IN ('COMP','CLOSE','CAN') THEN 1 ELSE 0 END) as PM_OPEN"),
                        ])
                        ->where('WORKTYPE', 'PM')
                        ->whereIn('ASSETNUM', $cmAssetNums)
                        ->whereBetween('REPORTDATE', [$startDate, $endDate])
                        ->groupBy('ASSETNUM', 'LOCATION')
                        ->orderByRaw('COUNT(*) DESC')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'assetnum'  => $item->assetnum ?? $item->ASSETNUM ?? '-',
                                'location'  => $item->location ?? $item->LOCATION ?? '-',
                                'pm_count'  => $item->pm_count ?? $item->PM_COUNT ?? 0,
                                'pm_closed' => $item->pm_closed ?? $item->PM_CLOSED ?? 0,
                                'pm_open'   => $item->pm_open ?? $item->PM_OPEN ?? 0,
                            ];
                        })
                        ->keyBy('assetnum');
                }
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - PM Coverage Error: ' . $e->getMessage());
            }

            // ============================================
            // 3. ASET GANGGUAN BERULANG (≥2x CM) - Detail WO per aset
            // ============================================
            $recurringAssets = collect();
            try {
                $recurringAssetNums = $cmAssets->where('cm_count', '>=', 2)->pluck('assetnum')->filter(fn($v) => $v !== '-')->toArray();

                if (!empty($recurringAssetNums)) {
                    $recurringAssets = (clone $woQuery)
                        ->select([
                            'ASSETNUM',
                            'LOCATION',
                            'WONUM',
                            'DESCRIPTION',
                            'STATUS',
                            'WORKTYPE',
                            'REPORTDATE',
                            'STATUSDATE',
                        ])
                        ->where('WORKTYPE', 'CM')
                        ->whereIn('ASSETNUM', $recurringAssetNums)
                        ->whereBetween('REPORTDATE', [$startDate, $endDate])
                        ->orderBy('ASSETNUM')
                        ->orderBy('REPORTDATE', 'desc')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'assetnum'    => $item->assetnum ?? $item->ASSETNUM ?? '-',
                                'location'    => $item->location ?? $item->LOCATION ?? '-',
                                'wonum'       => $item->wonum ?? $item->WONUM ?? '-',
                                'description' => $item->description ?? $item->DESCRIPTION ?? '-',
                                'status'      => $item->status ?? $item->STATUS ?? '-',
                                'worktype'    => $item->worktype ?? $item->WORKTYPE ?? '-',
                                'reportdate'  => isset($item->reportdate) && $item->reportdate
                                    ? Carbon::parse($item->reportdate)->format('d-m-Y')
                                    : (isset($item->REPORTDATE) && $item->REPORTDATE
                                        ? Carbon::parse($item->REPORTDATE)->format('d-m-Y')
                                        : '-'),
                                'statusdate'  => isset($item->statusdate) && $item->statusdate
                                    ? Carbon::parse($item->statusdate)->format('d-m-Y')
                                    : (isset($item->STATUSDATE) && $item->STATUSDATE
                                        ? Carbon::parse($item->STATUSDATE)->format('d-m-Y')
                                        : '-'),
                            ];
                        })
                        ->groupBy('assetnum');
                }
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - Recurring Assets Error: ' . $e->getMessage());
            }

            // ============================================
            // 4. SUMMARY
            // ============================================
            $summary = [
                'total_cm_wo'          => $cmAssets->sum('cm_count'),
                'total_assets_with_cm' => $cmAssets->count(),
                'assets_with_pm'       => $pmCoverage->count(),
                'assets_without_pm'    => max(0, $cmAssets->count() - $pmCoverage->count()),
                'recurring_assets'     => $cmAssets->where('cm_count', '>=', 2)->count(),
            ];

            return view('peta-kesehatan-unit', [
                'cmAssets'         => $cmAssets,
                'pmCoverage'       => $pmCoverage,
                'recurringAssets'  => $recurringAssets,
                'summary'          => $summary,
                'filterMonths'     => $filterMonths,
                'startDate'        => $startDate,
                'endDate'          => $endDate,
                'error'            => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Peta Kesehatan Unit Error: ' . $e->getMessage());

            return view('peta-kesehatan-unit', [
                'cmAssets'        => collect(),
                'pmCoverage'      => collect(),
                'recurringAssets' => collect(),
                'summary'         => [
                    'total_cm_wo'          => 0,
                    'total_assets_with_cm' => 0,
                    'assets_with_pm'       => 0,
                    'assets_without_pm'    => 0,
                    'recurring_assets'     => 0,
                ],
                'filterMonths'    => $filterMonths,
                'startDate'       => $startDate ?? Carbon::now()->subMonths(6),
                'endDate'         => $endDate ?? Carbon::now(),
                'error'           => 'Gagal mengambil data dari Maximo: ' . $e->getMessage(),
            ]);
        }
    }
}
