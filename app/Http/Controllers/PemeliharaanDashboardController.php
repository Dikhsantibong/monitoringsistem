<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;
use App\Models\MachineStatusLog;
use App\Models\PowerPlant;
use App\Models\Notification;
use App\Models\WorkOrder;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PemeliharaanDashboardController extends Controller
{
    public function index()
    {
        // Mengambil data untuk overview cards (Local DB)
        $totalMachines = MachineStatusLog::whereDate('created_at', today())
            ->distinct('machine_id')
            ->count('machine_id');
        $operatingMachines = MachineStatusLog::where('status', 'Operasi')
            ->whereDate('created_at', today())
            ->distinct('machine_id')
            ->count('machine_id');
        $troubleMachines = MachineStatusLog::where('status', 'Gangguan')
            ->whereDate('created_at', today())
            ->distinct('machine_id')
            ->count('machine_id');
        $maintenanceMachines = MachineStatusLog::where('status', 'Pemeliharaan')
            ->whereDate('created_at', today())
            ->distinct('machine_id')
            ->count('machine_id');

        // Mengambil data Maximo (Oracle)
        $maximoData = [
            'total_wo' => 0,
            'appr_wo' => 0,
            'total_sr' => 0,
            'recent_wo' => collect([]),
        ];

        try {
            // Summary WO
            $maximoData['total_wo'] = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->count();

            $maximoData['appr_wo'] = DB::connection('oracle')
                ->table('WORKORDER')
                ->where('SITEID', 'KD')
                ->where('STATUS', 'APPR')
                ->count();

            // Summary SR
            $maximoData['total_sr'] = DB::connection('oracle')
                ->table('SR')
                ->where('SITEID', 'KD')
                ->count();

            // Recent Work Orders
            $recentMaximoWo = DB::connection('oracle')
                ->table('WORKORDER')
                ->select(['WONUM', 'DESCRIPTION', 'STATUS', 'STATUSDATE', 'WORKTYPE'])
                ->where('SITEID', 'KD')
                ->orderBy('STATUSDATE', 'desc')
                ->take(5)
                ->get();

            $maximoData['recent_wo'] = collect($recentMaximoWo)->map(function($wo) {
                return [
                    'wonum' => trim($wo->wonum),
                    'description' => $wo->description,
                    'status' => $wo->status,
                    'statusdate' => isset($wo->statusdate) ? Carbon::parse($wo->statusdate)->format('d-m-Y H:i') : '-',
                    'worktype' => $wo->worktype,
                ];
            });

        } catch (\Exception $e) {
            Log::error('Gagal mengambil data Maximo di Dashboard: ' . $e->getMessage());
        }

        // Mengambil data kinerja pembangkit dengan pengecekan pembagi nol
        $powerPlantPerformance = PowerPlant::select('name')
            ->withCount(['machines as total_machines'])
            ->withCount(['machines as operating_machines' => function($query) {
                $query->whereHas('statusLogs', function($q) {
                    $q->where('status', 'Operasi')
                        ->whereDate('created_at', today());
                });
            }])
            ->get()
            ->map(function($plant) {
                $plant->efficiency = $plant->total_machines > 0 
                    ? ($plant->operating_machines / $plant->total_machines) * 100 
                    : 0;
                return $plant;
            });

        // Mengambil meeting hari ini
        $todayMeetings = Meeting::whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], '');
        
        $myWorkOrders = WorkOrder::whereRaw(
            "LOWER(REPLACE(REPLACE(labor, '-', ''), ' ', '')) LIKE ? AND status != 'Closed'",
            ['%' . $normalizedName . '%']
        )->get();

        return view('pemeliharaan.dashboard', compact(
            'totalMachines',
            'operatingMachines',
            'troubleMachines',
            'maintenanceMachines',
            'powerPlantPerformance',
            'todayMeetings',
            'myWorkOrders',
            'maximoData'
        ));
    }
}
