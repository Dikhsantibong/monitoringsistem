<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineOperation;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPemantauanController extends Controller
{
    public function index()
    {
        try {
            // Query data mesin dengan relasi yang dibutuhkan
            $machines = Machine::with(['powerPlant', 'statusLogs' => function($query) {
                $query->latest('created_at');
            }])
            ->select('machines.*')
            ->get();

            // Transform data untuk tampilan
            $machineData = $machines->map(function ($machine) {
                $latestStatus = $machine->statusLogs->first();
                
                return [
                    'id' => $machine->id,
                    'type' => $machine->type ?? $machine->name,
                    'unit_name' => $machine->powerPlant->name ?? 'N/A',
                    'status' => $latestStatus ? $latestStatus->status : 'Unknown',
                    'updated_at' => $latestStatus ? $latestStatus->created_at : null,
                    'serial_number' => $machine->serial_number ?? 'N/A'
                ];
            });

            return view('dashboard_pemantauan', compact('machineData'));

        } catch (\Exception $e) {
            \Log::error('Dashboard Pemantauan Error: ' . $e->getMessage());
            return view('dashboard_pemantauan', [
                'machineData' => collect([]),
                'error' => 'Terjadi kesalahan saat mengambil data.'
            ]);
        }
    }

    private function getMachineStatistics()
    {
        try {
            $latestStatuses = MachineStatusLog::select('machine_id', 'status')
                ->whereIn('id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('machine_status_logs')
                        ->groupBy('machine_id');
                })
                ->get();

            $totalMachines = $latestStatuses->count();
            $activeMachines = $latestStatuses->where('status', 'normal')->count();
            $maintenanceMachines = $latestStatuses->where('status', 'maintenance')->count();

            return [
                'total' => $totalMachines,
                'active' => $activeMachines,
                'maintenance' => $maintenanceMachines,
                'percentage' => $totalMachines > 0 ? 
                    round(($maintenanceMachines / $totalMachines) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            \Log::error('Statistics Error: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'maintenance' => 0,
                'percentage' => 0
            ];
        }
    }
} 