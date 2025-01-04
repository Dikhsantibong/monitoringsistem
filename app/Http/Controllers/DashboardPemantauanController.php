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
            'statistics' => [
                'total' => 0,
                'active' => 0,
                'maintenance' => 0,
                'percentage' => 0
            ]
        ];

        try {
            // Set koneksi database
            config(['database.default' => 'u478221055_up_kendari']);
            
            // Cek koneksi database
            DB::connection('u478221055_up_kendari')->getPdo();

            // Query data
            $machines = Machine::on('u478221055_up_kendari')
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
                    'type' => $machine->name,
                    'unit_name' => $machine->powerPlant->name,
                    'status' => $latestStatus ? $latestStatus->status : 'unknown',
                    'latest_operation' => $machine->operations()
                        ->latest('recorded_at')
                        ->first()
                ];
            });

            // Get statistics
            $viewData['statistics'] = $this->getMachineStatistics();

            // Tambahkan data gangguan percentage
            $gangguanData = MachineStatusLog::getGangguanPercentage();
            $viewData['gangguanPercentage'] = $gangguanData;

            // Tambahkan data derating percentage
            $deratingPercentage = MachineStatusLog::getDeratingPercentage();
            $viewData['deratingPercentage'] = $deratingPercentage;

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