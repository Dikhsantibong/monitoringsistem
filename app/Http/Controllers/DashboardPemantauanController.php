<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineOperation;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use Illuminate\Http\Request;

class DashboardPemantauanController extends Controller
{
    public function index()
    {
        $machineData = Machine::with(['operations', 'powerPlant', 'statusLogs'])
            ->select('machines.*')
            ->join('machine_operations', 'machines.id', '=', 'machine_operations.machine_id')
            ->join('power_plants', 'machines.power_plant_id', '=', 'power_plants.id')
            ->groupBy('machines.id')
            ->get()
            ->map(function ($machine) {
                // Ambil status log terbaru untuk mesin ini
                $latestStatus = MachineStatusLog::where('machine_id', $machine->id)
                    ->latest('tanggal')
                    ->first();

                return [
                    'id' => $machine->id,
                    'type' => $machine->name,
                    'unit_name' => $machine->powerPlant->name,
                    'status' => $latestStatus ? $latestStatus->status : 'unknown',
                    'latest_operation' => $machine->operations()
                        ->latest('recorded_at')
                        ->first()
                ];
            });

        return view('dashboard_pemantauan', compact('machineData'));
    }

    private function getMachineStatistics()
    {
        // Hitung statistik berdasarkan status dari MachineStatusLog
        $latestStatuses = MachineStatusLog::select('machine_id', 'status')
            ->whereIn('id', function($query) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('machine_status_logs')
                    ->groupBy('machine_id');
            })
            ->get();

        $totalMachines = $latestStatuses->count();
        $activeMachines = $latestStatuses->where('status', 'active')->count();
        $maintenanceMachines = $latestStatuses->where('status', 'maintenance')->count();

        return [
            'total' => $totalMachines,
            'active' => $activeMachines,
            'maintenance' => $maintenanceMachines,
            'percentage' => $totalMachines > 0 ? 
                round(($maintenanceMachines / $totalMachines) * 100, 2) : 0
        ];
    }
} 