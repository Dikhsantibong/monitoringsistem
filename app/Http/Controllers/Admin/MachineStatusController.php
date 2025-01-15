<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use App\Models\UnitOperationHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class MachineStatusController extends Controller
{
    public function view(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            $unitSource = $request->get('unit_source');
            $searchQuery = $request->get('search');
            
            // Query power plants
            $powerPlantsQuery = PowerPlant::with(['machines' => function($query) {
                $query->with(['statusLogs' => function($query) {
                    $query->where('tanggal', '>=', now()->subDays(30))
                          ->orderBy('tanggal', 'desc');
                }]);
            }]);
            
            // Filter berdasarkan unit_source
            if (session('unit') === 'mysql') {
                if ($unitSource) {
                    $powerPlantsQuery->where('unit_source', $unitSource);
                }
            } else {
                $powerPlantsQuery->where('unit_source', session('unit'));
            }
            
            // Filter pencarian
            if ($searchQuery) {
                $powerPlantsQuery->where(function($query) use ($searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%")
                        ->orWhereHas('machines', function($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        });
                });
            }
            
            $powerPlants = $powerPlantsQuery->get();
            
            // Hitung statistik downtime untuk setiap mesin
            foreach ($powerPlants as $powerPlant) {
                foreach ($powerPlant->machines as $machine) {
                    $machine->downtime_stats = $this->calculateDowntimeStats($machine);
                }
            }
            
            // Get logs dengan filter pencarian
            $logsQuery = MachineStatusLog::with(['machine', 'powerPlant'])
                ->whereDate('tanggal', $date);
            
            if ($searchQuery) {
                $logsQuery->where(function($query) use ($searchQuery) {
                    $query->whereHas('machine', function($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    })
                    ->orWhere('status', 'like', "%{$searchQuery}%");
                });
            }
            
            $logs = $logsQuery->get();

            // Get dan hitung HOP
            $hopQuery = UnitOperationHour::with('powerPlant')
                ->whereDate('tanggal', $date);

            if ($unitSource) {
                $hopQuery->where('unit_source', $unitSource);
            } elseif (session('unit') !== 'mysql') {
                $hopQuery->where('unit_source', session('unit'));
            }

            $unitOperationHours = $hopQuery->get()->mapWithKeys(function ($item) {
                return [$item->power_plant_id => $item];
            });

            $totalHopByPlant = [];
            foreach ($powerPlants as $powerPlant) {
                $hop = $unitOperationHours->get($powerPlant->id);
                $totalHopByPlant[$powerPlant->id] = [
                    'value' => $hop ? $hop->hop_value : 0,
                    'status' => $hop && $hop->hop_value >= 7 ? 'aman' : 'siaga'
                ];
            }

            if ($request->ajax()) {
                $html = View::make('admin.machine-status._table', compact(
                    'powerPlants', 
                    'date', 
                    'logs', 
                    'unitOperationHours',
                    'totalHopByPlant'
                ))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html
                ]);
            }

            return view('admin.machine-status.view', compact(
                'powerPlants', 
                'date', 
                'logs', 
                'unitOperationHours',
                'totalHopByPlant'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in MachineStatusController@view: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghitung statistik downtime untuk sebuah mesin
     */
    private function calculateDowntimeStats($machine)
    {
        $stats = [
            'total_downtime' => 0,
            'current_downtime' => null,
            'is_down' => false
        ];

        $statusLogs = $machine->statusLogs()
            ->where('status', 'STOP')
            ->where('tanggal', '>=', now()->subDays(30))
            ->orderBy('tanggal', 'desc')
            ->get();

        foreach ($statusLogs as $log) {
            $startTime = Carbon::parse($log->tanggal);
            
            // Hitung waktu selesai
            if ($log->target_selesai) {
                $endTime = Carbon::parse($log->target_selesai);
                if ($endTime->isFuture()) {
                    $endTime = now();
                }
            } else {
                $nextLog = $machine->statusLogs()
                    ->where('tanggal', '>', $log->tanggal)
                    ->orderBy('tanggal', 'asc')
                    ->first();
                $endTime = $nextLog ? Carbon::parse($nextLog->tanggal) : now();
            }
            
            $duration = $startTime->diffInHours($endTime, true);
            $stats['total_downtime'] += $duration;

            // Jika ini adalah log terbaru dan statusnya STOP
            if ($log === $statusLogs->first()) {
                $stats['is_down'] = true;
                $stats['current_downtime'] = [
                    'duration' => $duration,
                    'component' => $log->component,
                    'equipment' => $log->equipment,
                    'deskripsi' => $log->deskripsi,
                    'progres' => $log->progres,
                    'start_time' => $startTime,
                    'target_selesai' => $log->target_selesai
                ];
            }
        }

        return $stats;
    }
} 
