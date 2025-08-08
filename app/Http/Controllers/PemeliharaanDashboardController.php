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

class PemeliharaanDashboardController extends Controller
{
    public function index()
    {
        // Mengambil data untuk overview cards
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
                // Hindari pembagian dengan nol
                $plant->efficiency = $plant->total_machines > 0 
                    ? ($plant->operating_machines / $plant->total_machines) * 100 
                    : 0;
                return $plant;
            });

        // Mengambil aktivitas pemeliharaan terbaru
        $recentMaintenances = MachineStatusLog::with('machine')
            ->where('status', 'Pemeliharaan')
            ->whereDate('created_at', today())
            ->latest()
            ->take(5)
            ->get();

        // Mengambil meeting hari ini
        $todayMeetings = Meeting::whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        $normalizedName = Str::of(Auth::user()->name)
            ->lower()
            ->replace(['-', ' '], ''); // hilangkan strip dan spasi
        
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
            'recentMaintenances',
            'todayMeetings',
            'myWorkOrders'
        ));
    }
}