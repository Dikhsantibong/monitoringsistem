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
            // Base query builder
            $woQuery = DB::connection('oracle')->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');

            // Get SUMMARY data first (full count)
            $totalCmCountQuery = (clone $woQuery)
                ->where('WORKTYPE', 'CM')
                ->whereNotNull('ASSETNUM')
                ->whereBetween('REPORTDATE', [$startDate, $endDate]);

            $totalCmWo = (clone $totalCmCountQuery)->count();
            
            $allCmAssetsCount = (clone $totalCmCountQuery)
                ->select('ASSETNUM')
                ->groupBy('ASSETNUM')
                ->get()
                ->count();

            // ============================================
            // 1. ASET SERING GANGGUAN (CM) - Paginated
            // ============================================
            $cmAssetsPaginator = (clone $woQuery)
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
                ->paginate(15, ['*'], 'cm_page');

            $cmAssets = collect($cmAssetsPaginator->items())->map(function ($item) {
                return [
                    'assetnum' => $item->assetnum ?? $item->ASSETNUM ?? '-',
                    'location' => $item->location ?? $item->LOCATION ?? '-',
                    'cm_count' => $item->cm_count ?? $item->CM_COUNT ?? 0,
                ];
            });

            // ============================================
            // 2. ANTISIPASI PM - Fetch for assets on both CM and Recurring current pages
            // ============================================
            $pmCoverage = collect();
            
            // Assets from CM Tab (Page 1 by default or specific page)
            $cmPageAssets = $cmAssets->pluck('assetnum')->filter(fn($v) => $v !== '-')->unique()->values()->toArray();
            
            // Assets from Recurring Tab (Page 1 by default or specific page)
            $recurringPageAssets = collect($recurringAssetsPaginator->items())->pluck('assetnum')->toArray() 
                                 ?: collect($recurringAssetsPaginator->items())->pluck('ASSETNUM')->toArray();
                                 
            // Combine both to fetch PM coverage once
            $assetsOnPage = array_unique(array_merge($cmPageAssets, $recurringPageAssets));
            
            if (!empty($assetsOnPage)) {
                $pmCoverage = (clone $woQuery)
                    ->select([
                        'ASSETNUM',
                        'LOCATION',
                        DB::raw('COUNT(*) as PM_COUNT'),
                        DB::raw("SUM(CASE WHEN STATUS IN ('COMP','CLOSE') THEN 1 ELSE 0 END) as PM_CLOSED"),
                        DB::raw("SUM(CASE WHEN STATUS NOT IN ('COMP','CLOSE','CAN') THEN 1 ELSE 0 END) as PM_OPEN"),
                    ])
                    ->where('WORKTYPE', 'PM')
                    ->whereIn('ASSETNUM', $assetsOnPage)
                    ->whereBetween('REPORTDATE', [$startDate, $endDate])
                    ->groupBy('ASSETNUM', 'LOCATION')
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

            // Summary stats for total covered (needs full check or approximation)
            // For simplicity, we'll keep the summary logic but it might need a separate full query if accuracy is critical
            $assetsWithPmCount = (clone $woQuery)
                ->where('WORKTYPE', 'PM')
                ->whereBetween('REPORTDATE', [$startDate, $endDate])
                ->whereNotNull('ASSETNUM')
                ->distinct('ASSETNUM')
                ->count('ASSETNUM');

            // ============================================
            // 3. ASET GANGGUAN BERULANG (≥2x CM) - Paginated
            // ============================================
            $recurringAssetsQuery = (clone $woQuery)
                ->select([
                    'ASSETNUM',
                    'LOCATION',
                    DB::raw('COUNT(*) as CM_COUNT'),
                ])
                ->where('WORKTYPE', 'CM')
                ->whereNotNull('ASSETNUM')
                ->whereBetween('REPORTDATE', [$startDate, $endDate])
                ->groupBy('ASSETNUM', 'LOCATION')
                ->havingRaw('COUNT(*) >= 2')
                ->orderByRaw('COUNT(*) DESC');

            $recurringAssetsPaginator = $recurringAssetsQuery->paginate(10, ['*'], 'recurring_page');
            
            $recurringAssetNums = collect($recurringAssetsPaginator->items())->pluck('assetnum')->toArray() 
                                 ?: collect($recurringAssetsPaginator->items())->pluck('ASSETNUM')->toArray();

            $recurringAssetDetails = collect();
            if (!empty($recurringAssetNums)) {
                $rawDetails = (clone $woQuery)
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
                    ->orderBy('REPORTDATE', 'desc') // Keep history in individual asset descending
                    ->get()
                    ->map(function ($item) {
                        return [
                            'assetnum'    => $item->assetnum ?? $item->ASSETNUM ?? '-',
                            'location'    => $item->location ?? $item->LOCATION ?? '-',
                            'wonum'       => $item->wonum ?? $item->WONUM ?? '-',
                            'description' => $item->description ?? $item->DESCRIPTION ?? '-',
                            'status'      => $item->status ?? $item->STATUS ?? '-',
                            'reportdate'  => isset($item->reportdate) && $item->reportdate ? Carbon::parse($item->reportdate)->format('d-m-Y') : '-',
                            'statusdate'  => isset($item->statusdate) && $item->statusdate ? Carbon::parse($item->statusdate)->format('d-m-Y') : '-',
                        ];
                    });

                // Group by assetnum
                $grouped = $rawDetails->groupBy('assetnum');

                // Reorder the grouped collection to match the recurringAssetNums order (Sorted by CM count)
                $recurringAssetDetails = collect();
                foreach ($recurringAssetNums as $anum) {
                    if (isset($grouped[$anum])) {
                        $recurringAssetDetails[$anum] = $grouped[$anum];
                    }
                }
            }

            // Summary stats
            $summary = [
                'total_cm_wo'          => $totalCmWo,
                'total_assets_with_cm' => $allCmAssetsCount,
                'assets_with_pm'       => $assetsWithPmCount,
                'assets_without_pm'    => max(0, $allCmAssetsCount - $assetsWithPmCount),
                'recurring_assets'     => (clone $recurringAssetsQuery)->get()->count(),
            ];

            return view('peta-kesehatan-unit', [
                'cmAssets'                 => $cmAssets,
                'cmAssetsPaginator'        => $cmAssetsPaginator,
                'pmCoverage'               => $pmCoverage,
                'recurringAssets'          => $recurringAssetDetails,
                'recurringAssetsPaginator' => $recurringAssetsPaginator,
                'summary'                  => $summary,
                'filterMonths'             => $filterMonths,
                'startDate'                => $startDate,
                'endDate'                  => $endDate,
                'error'                    => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Peta Kesehatan Unit Error: ' . $e->getMessage());

            return view('peta-kesehatan-unit', [
                'cmAssets'                 => collect(),
                'cmAssetsPaginator'        => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'pmCoverage'               => collect(),
                'recurringAssets'          => collect(),
                'recurringAssetsPaginator' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
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
