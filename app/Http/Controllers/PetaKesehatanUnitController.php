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
            // ============================================
            // 1. ASET SERING GANGGUAN (CM) - Top Repeat Offenders
            // ============================================
            $cmAssets = collect();
            try {
                $cmAssets = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->select([
                        'ASSETNUM',
                        'LOCATION',
                        DB::raw('COUNT(*) as CM_COUNT'),
                        DB::raw('MAX(DESCRIPTION) as LAST_DESCRIPTION'),
                        DB::raw('MAX(REPORTDATE) as LAST_REPORT_DATE'),
                    ])
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->where('WORKTYPE', 'CM')
                    ->whereNotNull('ASSETNUM')
                    ->where('ASSETNUM', '!=', '')
                    ->whereRaw("REPORTDATE >= TO_DATE(?, 'YYYY-MM-DD')", [$startDate->format('Y-m-d')])
                    ->groupBy('ASSETNUM', 'LOCATION')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(50)
                    ->get()
                    ->map(function ($item) {
                        $item = (object) array_change_key_case((array) $item, CASE_LOWER);
                        return [
                            'assetnum' => $item->assetnum ?? '-',
                            'location' => $item->location ?? '-',
                            'cm_count' => $item->cm_count ?? 0,
                            'last_description' => $item->last_description ?? '-',
                            'last_report_date' => isset($item->last_report_date) && $item->last_report_date
                                ? Carbon::parse($item->last_report_date)->format('d-m-Y')
                                : '-',
                        ];
                    });
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - CM Assets Error: ' . $e->getMessage());
            }

            // ============================================
            // 2. ANTISIPASI PM - Aset yang sudah di-cover PM
            // ============================================
            $pmCoverage = collect();
            try {
                // Get list of asset numbers that have CM records
                $cmAssetNums = $cmAssets->pluck('assetnum')->filter()->unique()->values()->toArray();

                if (!empty($cmAssetNums)) {
                    $pmCoverage = DB::connection('oracle')
                        ->table('WORKORDER')
                        ->select([
                            'ASSETNUM',
                            'LOCATION',
                            DB::raw('COUNT(*) as PM_COUNT'),
                            DB::raw("SUM(CASE WHEN STATUS IN ('COMP', 'CLOSE', 'CLOSED') THEN 1 ELSE 0 END) as PM_CLOSED"),
                            DB::raw("SUM(CASE WHEN STATUS NOT IN ('COMP', 'CLOSE', 'CLOSED', 'CAN') THEN 1 ELSE 0 END) as PM_OPEN"),
                            DB::raw('MAX(DESCRIPTION) as LAST_PM_DESCRIPTION'),
                            DB::raw('MAX(SCHEDSTART) as NEXT_SCHED'),
                        ])
                        ->where('SITEID', 'KD')
                        ->where('WONUM', 'LIKE', 'WO%')
                        ->where('WORKTYPE', 'PM')
                        ->whereIn('ASSETNUM', $cmAssetNums)
                        ->whereRaw("REPORTDATE >= TO_DATE(?, 'YYYY-MM-DD')", [$startDate->format('Y-m-d')])
                        ->groupBy('ASSETNUM', 'LOCATION')
                        ->orderByRaw('COUNT(*) DESC')
                        ->get()
                        ->map(function ($item) {
                            $item = (object) array_change_key_case((array) $item, CASE_LOWER);
                            return [
                                'assetnum' => $item->assetnum ?? '-',
                                'location' => $item->location ?? '-',
                                'pm_count' => $item->pm_count ?? 0,
                                'pm_closed' => $item->pm_closed ?? 0,
                                'pm_open' => $item->pm_open ?? 0,
                                'last_pm_description' => $item->last_pm_description ?? '-',
                                'next_sched' => isset($item->next_sched) && $item->next_sched
                                    ? Carbon::parse($item->next_sched)->format('d-m-Y')
                                    : '-',
                            ];
                        })
                        ->keyBy('assetnum');
                }
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - PM Coverage Error: ' . $e->getMessage());
            }

            // ============================================
            // 3. ASET GANGGUAN BERULANG (Recurring CM by ASSETNUM)
            // ============================================
            $recurringAssets = collect();
            try {
                $recurringAssets = DB::connection('oracle')
                    ->table('WORKORDER')
                    ->select([
                        'ASSETNUM',
                        'LOCATION',
                        'WONUM',
                        'DESCRIPTION',
                        'STATUS',
                        'REPORTDATE',
                        'STATUSDATE',
                    ])
                    ->where('SITEID', 'KD')
                    ->where('WONUM', 'LIKE', 'WO%')
                    ->where('WORKTYPE', 'CM')
                    ->whereNotNull('ASSETNUM')
                    ->where('ASSETNUM', '!=', '')
                    ->whereIn('ASSETNUM', $cmAssets->where('cm_count', '>=', 2)->pluck('assetnum')->toArray() ?: ['__NONE__'])
                    ->whereRaw("REPORTDATE >= TO_DATE(?, 'YYYY-MM-DD')", [$startDate->format('Y-m-d')])
                    ->orderBy('ASSETNUM')
                    ->orderBy('REPORTDATE', 'desc')
                    ->limit(200)
                    ->get()
                    ->map(function ($item) {
                        $item = (object) array_change_key_case((array) $item, CASE_LOWER);
                        return [
                            'assetnum' => $item->assetnum ?? '-',
                            'location' => $item->location ?? '-',
                            'wonum' => $item->wonum ?? '-',
                            'description' => $item->description ?? '-',
                            'status' => $item->status ?? '-',
                            'reportdate' => isset($item->reportdate) && $item->reportdate
                                ? Carbon::parse($item->reportdate)->format('d-m-Y')
                                : '-',
                            'statusdate' => isset($item->statusdate) && $item->statusdate
                                ? Carbon::parse($item->statusdate)->format('d-m-Y')
                                : '-',
                        ];
                    })
                    ->groupBy('assetnum');
            } catch (\Exception $e) {
                Log::error('Peta Kesehatan - Recurring Assets Error: ' . $e->getMessage());
            }

            // ============================================
            // 4. SUMMARY STATS
            // ============================================
            $summary = [
                'total_cm_wo' => $cmAssets->sum('cm_count'),
                'total_assets_with_cm' => $cmAssets->count(),
                'assets_with_pm' => $pmCoverage->count(),
                'assets_without_pm' => $cmAssets->count() - $pmCoverage->count(),
                'recurring_assets' => $cmAssets->where('cm_count', '>=', 2)->count(),
            ];

            return view('peta-kesehatan-unit', [
                'cmAssets' => $cmAssets,
                'pmCoverage' => $pmCoverage,
                'recurringAssets' => $recurringAssets,
                'summary' => $summary,
                'filterMonths' => $filterMonths,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Peta Kesehatan Unit Error: ' . $e->getMessage());

            return view('peta-kesehatan-unit', [
                'cmAssets' => collect(),
                'pmCoverage' => collect(),
                'recurringAssets' => collect(),
                'summary' => [
                    'total_cm_wo' => 0,
                    'total_assets_with_cm' => 0,
                    'assets_with_pm' => 0,
                    'assets_without_pm' => 0,
                    'recurring_assets' => 0,
                ],
                'filterMonths' => $filterMonths,
                'startDate' => $startDate ?? Carbon::now()->subMonths(6),
                'endDate' => $endDate ?? Carbon::now(),
                'error' => 'Gagal mengambil data dari Maximo: ' . $e->getMessage(),
            ]);
        }
    }
}
