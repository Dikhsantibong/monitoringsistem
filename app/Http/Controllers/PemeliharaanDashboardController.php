<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

            $totalWO = (clone $baseWOQuery)->count();

            $openWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $openStatuses)
                ->count();

            $closedWO = (clone $baseWOQuery)
                ->whereIn('STATUS', $closedStatuses)
                ->count();

            // Service Request Stats
            $baseSRQuery = DB::connection('oracle')
                ->table('SR')
                ->where('SITEID', 'KD');

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

            $recentWorkOrders = $recentQuery
                ->orderBy('STATUSDATE', 'desc')
                ->take(10)
                ->get();

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
                'recentWorkOrders',
                'woStatusData'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Oracle Error: ' . $e->getMessage());
            return view('pemeliharaan.dashboard', [
                'totalWO' => 0,
                'openWO' => 0,
                'closedWO' => 0,
                'totalSR' => 0,
                'openSR' => 0,
                'recentWorkOrders' => collect([]),
                'woStatusData' => ['labels' => [], 'counts' => []],
                'error' => 'Gagal mengambil data dari Oracle'
            ]);
        }
    }
}