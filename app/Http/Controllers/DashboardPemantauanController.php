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
        // Inisialisasi default values
        $viewData = [
            'machineData' => collect([]),
            'error' => null,
            'gangguanPercentage' => ['normal' => 100, 'gangguan' => 0],
            'deratingPercentage' => 0,
            'statistics' => [
                'total' => 0,
                'active' => 0,
                'maintenance' => 0,
                'percentage' => 0
            ]
        ];

        try {
            // Set koneksi database
            $connection = 'u478221055_up_kendari';
            config(['database.default' => $connection]);
            
            // Cek koneksi database
            DB::connection($connection)->getPdo();

            // Query data dengan error handling
            try {
                $machines = Machine::on($connection)
                    ->with(['operations', 'powerPlant', 'statusLogs'])
                    ->select('machines.*')
                    ->join('machine_operations', 'machines.id', '=', 'machine_operations.machine_id')
                    ->join('power_plants', 'machines.power_plant_id', '=', 'power_plants.id')
                    ->groupBy('machines.id')
                    ->get();

                // Transform data
                $viewData['machineData'] = $machines->map(function ($machine) {
                    $latestStatus = MachineStatusLog::on('u478221055_up_kendari')
                        ->where('machine_id', $machine->id)
                        ->latest('tanggal')
                        ->first();

                    return [
                        'id' => $machine->id,
                        'type' => $machine->name ?? 'Unknown',
                        'unit_name' => $machine->powerPlant->name ?? 'Unknown',
                        'status' => $latestStatus ? $latestStatus->status : 'unknown',
                        'latest_operation' => $machine->operations()
                            ->latest('recorded_at')
                            ->first()
                    ];
                });
            } catch (\Exception $e) {
                \Log::error('Error querying machine data: ' . $e->getMessage());
            }

            // Get gangguan percentage dengan error handling
            try {
                $gangguanData = MachineStatusLog::getGangguanPercentage();
                $viewData['gangguanPercentage'] = $gangguanData;
            } catch (\Exception $e) {
                \Log::error('Error getting gangguan percentage: ' . $e->getMessage());
            }

            // Get derating percentage dengan error handling
            try {
                $deratingPercentage = MachineStatusLog::getDeratingPercentage();
                $viewData['deratingPercentage'] = $deratingPercentage;
            } catch (\Exception $e) {
                \Log::error('Error getting derating percentage: ' . $e->getMessage());
            }

            // Get statistics dengan error handling
            try {
                $viewData['statistics'] = $this->getMachineStatistics();
            } catch (\Exception $e) {
                \Log::error('Error getting machine statistics: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            \Log::error('Dashboard Pemantauan Error: ' . $e->getMessage());
            $viewData['error'] = 'Terjadi kesalahan saat mengambil data. Silakan coba lagi nanti.';
        }

        // Return view dengan data yang sudah dipersiapkan
        return view('dashboard_pemantauan', $viewData);
    }

    private function getMachineStatistics()
    {
        try {
            $connection = 'u478221055_up_kendari';
            $latestStatuses = MachineStatusLog::on($connection)
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