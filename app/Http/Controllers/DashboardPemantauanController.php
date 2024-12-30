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
    public function __construct()
    {
        // Set koneksi database default
        config(['database.default' => 'u478221055_up_kendari']);
    }

    public function index()
    {
        // Inisialisasi variabel
        $error = null;
        $machineData = collect([]);
        $statistics = [
            'total' => 0,
            'active' => 0,
            'maintenance' => 0,
            'percentage' => 0
        ];

        try {
            DB::connection('u478221055_up_kendari')->getPdo();
            
            $machineData = Machine::on('u478221055_up_kendari')
                ->with(['operations', 'powerPlant', 'statusLogs'])
                ->select('machines.*')
                ->join('machine_operations', 'machines.id', '=', 'machine_operations.machine_id')
                ->join('power_plants', 'machines.power_plant_id', '=', 'power_plants.id')
                ->groupBy('machines.id')
                ->get()
                ->map(function ($machine) {
                    // Ambil status log terbaru untuk mesin ini
                    $latestStatus = MachineStatusLog::on('u478221055_up_kendari')
                        ->where('machine_id', $machine->id)
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

            $statistics = $this->getMachineStatistics();

        } catch (\Exception $e) {
            // Log error jika terjadi masalah koneksi
            \Log::error('Database connection error: ' . $e->getMessage());
            $error = 'Tidak dapat terhubung ke database';
        }

        return view('dashboard_pemantauan', compact('machineData', 'statistics', 'error'));
    }

    private function getMachineStatistics()
    {
        $latestStatuses = MachineStatusLog::on('u478221055_up_kendari')
            ->select('machine_id', 'status')
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
    }
} 