<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use App\Models\UnitOperationHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Machine;

class MachineStatusController extends Controller
{
    public function view(Request $request)
    {
        try {
            $date = $request->get('date', now()->toDateString());
            $search = $request->get('search');
            $unitSource = $request->get('unit_source');
            
            // Query untuk mengambil data pembangkit
            $query = PowerPlant::with(['machines']);
            
            // Filter berdasarkan unit_source
            if (session('unit') === 'mysql') {
                if ($unitSource) {
                    $query->where('unit_source', $unitSource);
                }
            } else {
                $query->where('unit_source', session('unit'));
            }
            
            $powerPlants = $query->get();
            
            // Ambil semua log untuk tanggal yang dipilih
            $logs = MachineStatusLog::whereDate('tanggal', $date)
                ->when($search, function($query) use ($search) {
                    $query->where(function($q) use ($search) {
                        $q->whereHas('machine', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhere('status', 'like', "%{$search}%");
                    });
                })
                ->get();

            // Ambil data HOP
            $hops = UnitOperationHour::whereDate('tanggal', $date)
                ->when(session('unit') !== 'mysql', function($query) {
                    $query->whereHas('powerPlant', function($q) {
                        $q->where('unit_source', session('unit'));
                    });
                })
                ->when($unitSource, function($query) use ($unitSource) {
                    $query->whereHas('powerPlant', function($q) use ($unitSource) {
                        $q->where('unit_source', $unitSource);
                    });
                })
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.machine-status._table', compact('powerPlants', 'logs', 'hops', 'date'))->render()
                ]);
            }

            return view('admin.machine-status.view', compact('powerPlants', 'logs', 'hops', 'date'));
            
        } catch (\Exception $e) {
            \Log::error('Error in machine status view: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data');
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

    public function index()
    {
        $machines = Machine::with(['powerPlant', 'logs' => function($query) {
            $query->latest('tanggal')->take(1);
        }])->get();

        $formattedMachines = $machines->map(function($machine) {
            $latestLog = $machine->logs->first();
            
            // Cari gambar dari direktori
            $imagePath = null;
            $pattern = storage_path('app/public/machine-images/machine_' . $machine->id . '_*');
            $files = glob($pattern);
            if (!empty($files)) {
                rsort($files); // Sort descending untuk mendapatkan file terbaru
                $latestFile = $files[0];
                $imagePath = 'machine-images/' . basename($latestFile);
            }

            // Bersihkan deskripsi dari tag gambar
            $cleanDescription = $latestLog ? preg_replace('/\[image:.*?\]/', '', $latestLog->deskripsi ?? '') : '';
            
            return [
                'id' => $machine->id,
                'name' => $machine->name,
                'power_plant' => $machine->powerPlant->name,
                'latest_log' => $latestLog ? [
                    'status' => $latestLog->status,
                    'tanggal' => $latestLog->tanggal,
                    'deskripsi' => trim($cleanDescription),
                    'image_url' => $imagePath
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedMachines
        ]);
    }

    public function show($id)
    {
        try {
            $machine = Machine::with(['powerPlant', 'logs' => function($query) {
                $query->latest('tanggal');
            }])->findOrFail($id);

            // Cari gambar dari direktori
            $imagePath = null;
            $pattern = storage_path('app/public/machine-images/machine_' . $machine->id . '_*');
            $files = glob($pattern);
            if (!empty($files)) {
                rsort($files); // Sort descending untuk mendapatkan file terbaru
                $latestFile = $files[0];
                $imagePath = 'machine-images/' . basename($latestFile);
            }

            $latestLog = $machine->logs->first();
            
            // Bersihkan deskripsi dari tag gambar
            $cleanDescription = $latestLog ? preg_replace('/\[image:.*?\]/', '', $latestLog->deskripsi ?? '') : '';

            $machineData = [
                'id' => $machine->id,
                'name' => $machine->name,
                'power_plant' => $machine->powerPlant->name,
                'latest_log' => $latestLog ? [
                    'status' => $latestLog->status,
                    'tanggal' => $latestLog->tanggal,
                    'deskripsi' => trim($cleanDescription),
                    'image_url' => $imagePath
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data' => $machineData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mesin: ' . $e->getMessage()
            ], 500);
        }
    }
} 
