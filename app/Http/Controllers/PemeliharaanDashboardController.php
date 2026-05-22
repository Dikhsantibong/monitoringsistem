<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\PemeliharaanLocationHelper;

class PemeliharaanDashboardController extends Controller
{
    public function index()
    {
        try {
            $openStatuses = ['WAPPR', 'APPR', 'WSCH', 'WMATL', 'WPCOND', 'INPRG'];
            $closedStatuses = ['COMP', 'CLOSE'];

            // Work Order Stats
            $baseWOQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');
            PemeliharaanLocationHelper::applyLocationFilter($baseWOQuery);


            $totalWO = (clone $baseWOQuery)->count();

            $openWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $openStatuses)
                ->count();

            $closedWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $closedStatuses)
                ->count();

            $urgentWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $openStatuses)
                ->where('WOPRIORTEXT', 'URGENT')
                ->count();

            $emergencyWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $openStatuses)
                ->where('WOPRIORTEXT', 'EMERGENCY')
                ->count();

            // Service Request Stats
            $baseSRQuery = DB::connection('oracle')
                ->table('SR')
                ->where('SITEID', 'KD');
            PemeliharaanLocationHelper::applyLocationFilter($baseSRQuery);

            $totalSR = (clone $baseSRQuery)->count();

            $openSR = (clone $baseSRQuery)
                ->where('STATUS', 'QUEUED')
                ->count();

            // Recent Work Orders
            $recentQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'SCHEDFINISH',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%');
            PemeliharaanLocationHelper::applyLocationFilter($recentQuery);

            $recentWorkOrders = $recentQuery
                ->orderBy('STATUSDATE', 'desc')
                ->take(10)
                ->get();

            // Urgent/Emergency Open Work Orders
            $urgentQuery = DB::connection('oracle')
                ->table('WORKORDER')
                ->select([
                    'WONUM',
                    'DESCRIPTION',
                    'STATUS',
                    'STATUSDATE',
                    'WORKTYPE',
                    'WOPRIORTEXT',
                    'SCHEDFINISH',
                ])
                ->where('SITEID', 'KD')
                ->where('WONUM', 'LIKE', 'WO%')
                ->whereIn('STATUS', $openStatuses)
                ->whereIn('WOPRIORTEXT', ['URGENT', 'EMERGENCY']);
            PemeliharaanLocationHelper::applyLocationFilter($urgentQuery);
            $urgentWorkOrders = $urgentQuery
                ->orderBy('STATUSDATE', 'desc')
                ->take(10)
                ->get();


            // ==========================================
            // KINERJA KPIs (6 Months)
            // ==========================================
            $startDate = Carbon::now()->subMonths(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            // 1. PM Compliance
            $pmC = (clone $baseWOQuery)->where('WORKTYPE', 'PM')->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$startDate, $endDate])->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')->count();
            $pmT = (clone $baseWOQuery)->where('WORKTYPE', 'PM')->whereNotIn('STATUS', ['CAN', 'WSCH'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $kpiPmCompliance = $pmT > 0 ? round(($pmC / $pmT) * 100, 1) : 0;

            // 2. Non PM Compliance
            $npmC = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $npmT = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereNotIn('STATUS', ['CAN', 'WSCH'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $kpiNonPmCompliance = $npmT > 0 ? round(($npmC / $npmT) * 100, 1) : 0;

            // 3. Reactive Work
            $reactNonTac = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $reactTacClosed = (clone $baseWOQuery)->whereIn('WORKTYPE', ['PM', 'PDM', 'EJ', 'OH'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $reactTotal = $reactNonTac + $reactTacClosed;
            $kpiReactiveWork = $reactTotal > 0 ? round(($reactNonTac / $reactTotal) * 100, 1) : 0;

            // 4. Schedule Compliance
            $schedTot = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM', 'EJ', 'NM', 'SF'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$startDate, $endDate])->count();
            $schedC = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM', 'EJ', 'NM', 'SF'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$startDate, $endDate])->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')->count();
            $kpiScheduleCompliance = $schedTot > 0 ? round(($schedC / $schedTot) * 100, 1) : 0;

            // ==========================================
            // MONTHLY TREND FOR KPIs
            // ==========================================
            $trendLabels = [];
            $pmCompTrend = [];
            $nonPmCompTrend = [];
            $reactiveTrend = [];
            $schedCompTrend = [];

            $periodIterator = $startDate->copy()->startOfMonth();
            $endPeriod = $endDate->copy()->endOfMonth();

            while ($periodIterator <= $endPeriod) {
                $mStart = $periodIterator->copy()->startOfMonth();
                $mEnd = $periodIterator->copy()->endOfMonth();
                $trendLabels[] = $mStart->format('M Y');
                
                // PM Compliance Trend
                $mPmC = (clone $baseWOQuery)->where('WORKTYPE', 'PM')->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$mStart, $mEnd])->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')->count();
                $mPmT = (clone $baseWOQuery)->where('WORKTYPE', 'PM')->whereNotIn('STATUS', ['CAN', 'WSCH'])->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $pmCompTrend[] = $mPmT > 0 ? round(($mPmC / $mPmT) * 100, 1) : 0;
                
                // Non PM Compliance Trend
                $mNpmC = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $mNpmT = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereNotIn('STATUS', ['CAN', 'WSCH'])->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $nonPmCompTrend[] = $mNpmT > 0 ? round(($mNpmC / $mNpmT) * 100, 1) : 0;
                
                // Reactive Work Trend
                $mReactNonTac = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM'])->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $mReactTacClosed = (clone $baseWOQuery)->whereIn('WORKTYPE', ['PM', 'PDM', 'EJ', 'OH'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $mReactTotal = $mReactNonTac + $mReactTacClosed;
                $reactiveTrend[] = $mReactTotal > 0 ? round(($mReactNonTac / $mReactTotal) * 100, 1) : 0;
                
                // Schedule Compliance Trend
                $mSchedTot = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM', 'EJ', 'NM', 'SF'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$mStart, $mEnd])->count();
                $mSchedC = (clone $baseWOQuery)->whereIn('WORKTYPE', ['CM', 'EM', 'EJ', 'NM', 'SF'])->whereIn('STATUS', $closedStatuses)->whereBetween('REPORTDATE', [$mStart, $mEnd])->whereNotNull('ACTFINISH')->whereNotNull('SCHEDSTART')->whereNotNull('SCHEDFINISH')->whereNotNull('ACTLABHRS')->whereRaw('ACTFINISH >= SCHEDSTART')->whereRaw('ACTFINISH <= SCHEDFINISH')->count();
                $schedCompTrend[] = $mSchedTot > 0 ? round(($mSchedC / $mSchedTot) * 100, 1) : 0;

                $periodIterator->addMonth();
            }

            // Format data for chart
            $woStatusData = [
                'labels' => ['Open', 'Closed', 'Others'],
                'counts' => [
                    $openWO,
                    $closedWO,
                    max(0, $totalWO - ($openWO + $closedWO))
                ]
            ];

            return view('pemeliharaan.dashboard', compact(
                'totalWO',
                'openWO',
                'closedWO',
                'totalSR',
                'openSR',
                'urgentWO',
                'emergencyWO',
                'recentWorkOrders',
                'urgentWorkOrders',
                'woStatusData',
                'kpiPmCompliance',
                'kpiNonPmCompliance',
                'kpiReactiveWork',
                'kpiScheduleCompliance',
                'trendLabels',
                'pmCompTrend',
                'nonPmCompTrend',
                'reactiveTrend',
                'schedCompTrend'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Oracle Error: ' . $e->getMessage());
            return view('pemeliharaan.dashboard', [
                'totalWO' => 0,
                'openWO' => 0,
                'closedWO' => 0,
                'totalSR' => 0,
                'openSR' => 0,
                'urgentWO' => 0,
                'emergencyWO' => 0,
                'recentWorkOrders' => collect([]),
                'urgentWorkOrders' => collect([]),
                'woStatusData' => ['labels' => [], 'counts' => []],
                'kpiPmCompliance' => 0,
                'kpiNonPmCompliance' => 0,
                'kpiReactiveWork' => 0,
                'kpiScheduleCompliance' => 0,
                'trendLabels' => [],
                'pmCompTrend' => [],
                'nonPmCompTrend' => [],
                'reactiveTrend' => [],
                'schedCompTrend' => [],
                'error' => 'Gagal mengambil data dari Oracle'
            ]);
        }
    }
}