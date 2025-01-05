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
            // Data untuk OEE Chart (Keseluruhan Kondisi Mesin)
            $totalMachines = Machine::count();
            $faultMachines = Machine::whereHas('statusLogs', function($query) {
                $query->where('status', 'Gangguan')
                      ->whereDate('created_at', now());
            })->count();

            $chartData = [
                'total' => $totalMachines,
                'fault' => $faultMachines,
                'percentages' => [
                    'active' => $totalMachines > 0 ? round((($totalMachines - $faultMachines) / $totalMachines) * 100) : 0,
                    'fault' => $totalMachines > 0 ? round(($faultMachines / $totalMachines) * 100) : 0
                ]
            ];

            // Data untuk grafik kondisi pemeliharaan
            $maintenanceData = [
                'mothballed' => [
                    'total' => 100,
                    'current' => 30
                ],
                'maintenance' => [
                    'total' => 100,
                    'current' => 45
                ],
                'overhaul' => [
                    'total' => 100,
                    'current' => 75
                ]
            ];

            // Data untuk grafik penyelesaian
            $completionData = MachineStatusLog::with('machine')
                ->where('status', 'Gangguan')
                ->whereNotNull('tanggal_mulai')
                ->whereNotNull('target_selesai')
                ->get()
                ->map(function ($log) {
                    $targetDuration = $log->tanggal_mulai->diffInDays($log->target_selesai);
                    $actualDuration = $log->tanggal_mulai->diffInDays(now());
                    return [
                        'machine_name' => $log->machine->name ?? 'Mesin ' . $log->machine_id,
                        'target_days' => $targetDuration,
                        'actual_days' => $actualDuration,
                        'delay_percentage' => $targetDuration > 0 ? 
                            round((($actualDuration - $targetDuration) / $targetDuration) * 100, 1) : 0
                    ];
                });

            // Data mesin untuk tabel
            $machineData = Machine::with(['powerPlant', 'statusLogs' => function($query) {
                $query->latest('created_at');
            }])->get()->map(function($machine) {
                $latestStatus = $machine->statusLogs->first();
                return [
                    'type' => $machine->type ?? 'N/A',
                    'unit_name' => $machine->powerPlant->name ?? 'N/A',
                    'status' => $latestStatus ? $latestStatus->status : 'N/A',
                    'updated_at' => $latestStatus ? $latestStatus->created_at : null
                ];
            });

            // Data untuk Progress Pekerjaan
            $progressData = MachineStatusLog::with('machine')
                ->where('status', 'Gangguan')
                ->whereNotNull('progres')
                ->whereNotNull('target_selesai')
                ->where('target_selesai', '>=', now())
                ->get()
                ->map(function ($log) {
                    $remainingDays = now()->diffInDays($log->target_selesai, false);
                    return [
                        'machine_name' => $log->machine->name ?? 'Mesin ' . $log->machine_id,
                        'progress' => (float) $log->progres,
                        'remaining_days' => $remainingDays,
                        'target_days' => $log->tanggal_mulai->diffInDays($log->target_selesai)
                    ];
                });

            // Data untuk Tren Penyelesaian (6 bulan terakhir)
            $trendData = MachineStatusLog::where('status', 'Gangguan')
                ->whereNotNull('tanggal_mulai')
                ->whereNotNull('target_selesai')
                ->where('tanggal_mulai', '>=', now()->subMonths(6))
                ->get()
                ->groupBy(function ($log) {
                    return $log->tanggal_mulai->format('M Y');
                })
                ->map(function ($logs) {
                    return [
                        'total_cases' => $logs->count(),
                        'on_time' => $logs->filter(function($log) {
                            return $log->tanggal_mulai->diffInDays($log->created_at) <= 
                                   $log->tanggal_mulai->diffInDays($log->target_selesai);
                        })->count(),
                        'delayed' => $logs->filter(function($log) {
                            return $log->tanggal_mulai->diffInDays($log->created_at) > 
                                   $log->tanggal_mulai->diffInDays($log->target_selesai);
                        })->count()
                    ];
                });

            return view('dashboard_pemantauan', compact(
                'chartData',
                'maintenanceData',
                'completionData',
                'machineData',
                'progressData',
                'trendData'
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard Pemantauan Error: ' . $e->getMessage());
            return view('dashboard_pemantauan', [
                'chartData' => [
                    'total' => 0,
                    'fault' => 0,
                    'percentages' => ['active' => 0, 'fault' => 0]
                ],
                'maintenanceData' => [
                    'mothballed' => ['total' => 100, 'current' => 0],
                    'maintenance' => ['total' => 100, 'current' => 0],
                    'overhaul' => ['total' => 100, 'current' => 0]
                ],
                'completionData' => collect([]),
                'machineData' => collect([]),
                'progressData' => collect([]),
                'trendData' => collect([]),
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }

    private function getProgressStatus($progress, $remainingDays)
    {
        if ($remainingDays <= 0) return 'critical';
        if ($progress >= 80) return 'on-track';
        if ($progress >= 50) return 'warning';
        return 'critical';
    }
} 