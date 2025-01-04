<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineOperation;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardPemantauanController extends Controller
{
    protected $connection = 'u478221055_up_kendari';

    public function index()
    {
        // Inisialisasi default values dengan nilai yang aman
        $viewData = [
            'machineData' => collect([]),
            'error' => null,
            'gangguanPercentage' => [
                'normal' => 100,
                'gangguan' => 0
            ],
            'deratingPercentage' => 0,
            'statistics' => [
                'total' => 0,
                'active' => 0,
                'maintenance' => 0,
                'percentage' => 0
            ]
        ];

        try {
            // Set dan cek koneksi database
            DB::purge($this->connection);
            config(['database.default' => $this->connection]);
            DB::reconnect($this->connection);

            // Verifikasi koneksi database
            if (!$this->checkDatabaseConnection()) {
                throw new Exception('Tidak dapat terhubung ke database');
            }

            // Get gangguan percentage dengan error handling
            try {
                $gangguanData = MachineStatusLog::on($this->connection)
                    ->select(DB::raw('
                        COUNT(CASE WHEN status = "Gangguan" THEN 1 END) as gangguan_count,
                        COUNT(*) as total_count
                    '))
                    ->whereDate('tanggal', now()->toDateString())
                    ->first();

                if ($gangguanData && $gangguanData->total_count > 0) {
                    $gangguanPercentage = ($gangguanData->gangguan_count / $gangguanData->total_count) * 100;
                    $viewData['gangguanPercentage'] = [
                        'normal' => 100 - $gangguanPercentage,
                        'gangguan' => $gangguanPercentage
                    ];
                }
            } catch (Exception $e) {
                \Log::error('Gangguan Percentage Error: ' . $e->getMessage());
            }

            // Get machine data dengan error handling
            try {
                $machines = Machine::on($this->connection)
                    ->with(['powerPlant' => function($query) {
                        $query->on($this->connection);
                    }])
                    ->get();

                $viewData['machineData'] = $machines->map(function ($machine) {
                    $latestStatus = MachineStatusLog::on($this->connection)
                        ->where('machine_id', $machine->id)
                        ->latest('tanggal')
                        ->first();

                    return [
                        'id' => $machine->id,
                        'type' => $machine->name ?? 'Unknown',
                        'unit_name' => $machine->powerPlant->name ?? 'Unknown',
                        'status' => $latestStatus ? $latestStatus->status : 'unknown'
                    ];
                });
            } catch (Exception $e) {
                \Log::error('Machine Data Error: ' . $e->getMessage());
            }

            // Get derating percentage dengan error handling
            try {
                $deratingData = MachineStatusLog::on($this->connection)
                    ->whereDate('tanggal', now()->toDateString())
                    ->select(
                        DB::raw('SUM(dmn) as total_dmn'),
                        DB::raw('SUM(dmp) as total_dmp')
                    )
                    ->first();

                if ($deratingData && $deratingData->total_dmp > 0) {
                    $viewData['deratingPercentage'] = round((($deratingData->total_dmp - $deratingData->total_dmn) / $deratingData->total_dmp) * 100, 2);
                }
            } catch (Exception $e) {
                \Log::error('Derating Percentage Error: ' . $e->getMessage());
            }

            // Get statistics dengan error handling
            try {
                $viewData['statistics'] = $this->getMachineStatistics();
            } catch (Exception $e) {
                \Log::error('Statistics Error: ' . $e->getMessage());
            }

        } catch (Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            $viewData['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return view('dashboard_pemantauan', $viewData);
    }

    protected function checkDatabaseConnection()
    {
        try {
            DB::connection($this->connection)->getPdo();
            return true;
        } catch (Exception $e) {
            \Log::error('Database Connection Error: ' . $e->getMessage());
            return false;
        }
    }

    protected function getMachineStatistics()
    {
        $latestStatuses = MachineStatusLog::on($this->connection)
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