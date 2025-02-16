<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineIssue;
use App\Models\MachineHealthCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use App\Models\MachineStatusLog;
use App\Models\MachineOperation;
use App\Models\PowerPlant;
use App\Models\Issue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineMonitorController extends Controller
{
    public function index()
    {
        // Mengambil data mesin dan relasinya
        $machines = Machine::with(['issues', 'metrics'])->get();
        
        // Menghitung efisiensi untuk setiap mesin
        $efficiencyData = $machines->map(function ($machine) {
            return [
                'name' => $machine->name,
                'efficiency' => $machine->metrics->avg('efficiency') ?? 0 // Rata-rata efisiensi
            ];
        });

        // Menggunakan MachineHealthCategory yang sudah ada
        $healthCategories = Machine::with(['statusLogs', 'operations'])
            ->get()
            ->map(function ($machine) {
                return [
                    'machine_id' => $machine->id,
                    'name' => $machine->name,
                    'open_issues' => $machine->issues()->where('status', 'open')->count(),
                    'status_logs' => $machine->statusLogs,
                    'operations' => $machine->operations,
                ];
            });

        // Menghitung uptime/downtime untuk setiap mesin
        $uptime = $machines->map(function($machine) {
            // Ambil log status mesin dalam 24 jam terakhir
            $logs = MachineStatusLog::where('machine_id', $machine->id)
                ->where('tanggal', '>=', Carbon::now()->subDay())
                ->get();
            
            $totalTime = 0;
            $uptimeMinutes = 0;
            
            if ($logs->count() > 0) {
                foreach ($logs as $index => $log) {
                    $startTime = Carbon::parse($log->tanggal);
                    $endTime = isset($logs[$index + 1]) 
                        ? Carbon::parse($logs[$index + 1]->tanggal) 
                        : Carbon::now();
                    
                    $duration = $startTime->diffInMinutes($endTime);
                    $totalTime += $duration;
                    
                    if ($log->status === 'START' || $log->status === 'PARALLEL') {
                        $uptimeMinutes += $duration;
                    }
                }
                
                $uptimePercentage = $totalTime > 0 ? ($uptimeMinutes / $totalTime) * 100 : 0;
                $downtimePercentage = $totalTime > 0 ? 100 - $uptimePercentage : 0;
            } else {
                // Jika tidak ada log, gunakan status terakhir mesin
                $uptimePercentage = $machine->status === 'START' ? 100 : 0;
                $downtimePercentage = $machine->status === 'STOP' ? 100 : 0;
            }
            
            return [
                'name' => $machine->name,
                'uptime' => round($uptimePercentage, 2),
                'downtime' => round($downtimePercentage, 2),
            ];
        });

        // Mengambil masalah terbaru
        $recentIssues = MachineIssue::with(['machine', 'category'])
            ->latest()
            ->take(10)
            ->get();

        // Menghitung jumlah masalah per bulan
        $monthlyIssues = MachineIssue::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
        
        // Menyiapkan array 12 bulan
        $monthlyIssuesData = array_fill(1, 12, 0);
        foreach ($monthlyIssues as $month => $count) {
            $monthlyIssuesData[$month] = $count;
        }

        // Ambil data dari MachineStatusLog
        $machineStatusLogs = MachineStatusLog::with('machine')->get(); // Ambil data status mesin

        // Ambil semua power plants untuk filter
        $powerPlants = PowerPlant::all();

        return view('admin.machine-monitor.index', compact(
            'machines',
            'healthCategories',
            'monthlyIssues',
            'uptime',
            'recentIssues',
            'machineStatusLogs',
            'efficiencyData',
            'powerPlants'
        ));
    }

    public function storeIssue(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|integer',
            'category_id' => 'required|integer',
            'description' => 'required|string',
        ]);

        // Simpan issue baru (gunakan session untuk testing)
        session()->flash('success', 'Issue reported successfully');
        Alert::success('Berhasil', 'Masalah berhasil dilaporkan');
        return redirect()->back();
    }

    public function updateMachineStatus(Request $request, $machineId)
    {
        $validated = $request->validate([
            'status' => 'required|in:START,STOP,PARALLEL',
        ]);

        return response()->json(['success' => true]);
    }

    public function updateMetrics(Request $request, $machineId)
    {
        $validated = $request->validate([
            'metrics.*.name' => 'required|string',
            'metrics.*.value' => 'required|numeric',
            'metrics.*.target' => 'required|numeric',
        ]);

        return response()->json(['success' => true]);
    }

    public function storeMachine(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:machines',
                'category_id' => 'required|exists:machine_categories,id',
                'location' => 'required|string|max:255',
                'status' => 'required|in:START,STOP,PARALLEL',
                'description' => 'nullable|string'
            ]);

            // Debug: tampilkan data yang divalidasi
            \Log::info('Validated data:', $validated);

            // Buat mesin baru
            $machine = Machine::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Mesin berhasil ditambahkan',
                'data' => $machine
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating machine:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'serial_number' => 'required|string|max:50',
                'power_plant_id' => 'required|exists:power_plants,id',
                'dmn' => 'required|numeric',
                'dmp' => 'required|numeric',
                'load_value' => 'required|numeric',
            ]);

            // Get PowerPlant untuk unit_source
            $powerPlant = PowerPlant::findOrFail($validated['power_plant_id']);

            // Create the machine first
            $machine = Machine::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'serial_number' => $validated['serial_number'],
                'power_plant_id' => $validated['power_plant_id'],
                'status' => 'STOP',
                'unit_source' => $powerPlant->unit_source // Tambahkan unit_source dari PowerPlant
            ]);

            // Then create the machine operation
            if ($machine) {
                MachineOperation::create([
                    'machine_id' => $machine->id,
                    'dmn' => $validated['dmn'],
                    'dmp' => $validated['dmp'],
                    'load_value' => $validated['load_value'],
                    'recorded_at' => now(),
                    'unit_source' => $powerPlant->unit_source // Tambahkan unit_source dari PowerPlant
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data mesin berhasil ditambahkan!',
                'redirect_url' => route('admin.machine-monitor.show')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Failed to create machine: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showMachine(Machine $machine)
    {
        $machine->load(['metrics', 'issues']);
        $powerPlants = PowerPlant::all(); // Ambil semua power plants untuk filter
        return view('admin.machine-monitor.show', compact('machine', 'powerPlants'));
    }

    public function updateMachine(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:machines,code,' . $machine->id,
            'status' => 'required|in:START,STOP,PARALLEL'
        ]);

        try {
            $machine->update($validated);
            Alert::success('Berhasil', 'Mesin berhasil diperbarui');
            return response()->json([
                'success' => true,
                'message' => 'Mesin berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Gagal memperbarui mesin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyMachine(Machine $machine)
    {
        DB::beginTransaction();
        try {
            // Verifikasi bahwa mesin ada
            if (!$machine) {
                throw new \Exception('Mesin tidak ditemukan');
            }

            // Catat informasi mesin sebelum dihapus untuk logging
            $machineInfo = [
                'id' => $machine->id,
                'name' => $machine->name,
                'unit_source' => $machine->unit_source
            ];

            // Hapus mesin (akan mentrigger event deleting di model)
            $machine->delete();

            DB::commit();

            Log::info('Machine deleted successfully', $machineInfo);

            Alert::success('Berhasil', 'Mesin berhasil dihapus');
            return response()->json([
                'success' => true,
                'message' => 'Mesin berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to delete machine:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Alert::error('Gagal', 'Gagal menghapus mesin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $machines = Machine::all(); // Ambil semua kategori mesin
        return view('admin.machine-monitor.create', compact('machines'));
    }

    public function crud()
    {
        return view('admin.machine-monitor.crud');
    }
    
    public function show(Request $request)
    {
        $query = Machine::with(['powerPlant', 'operations' => function($query) {
            $query->latest('recorded_at')->take(1);
        }]);

        // Filter berdasarkan power plant jika ada
        if ($request->has('power_plant_id')) {
            $query->where('power_plant_id', $request->power_plant_id);
        }

        // Pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('serial_number', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('powerPlant', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  })
                  ->orWhereHas('operations', function($q) use ($searchTerm) {
                      $q->where('dmn', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('dmp', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('load_value', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('hop', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('keterangan', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $machines = $query->orderBy('id')->paginate(10);
        
        if ($request->ajax()) {
            return view('admin.machine-monitor.table-body', compact('machines'))->render();
        }
        
        return view('admin.machine-monitor.show', compact('machines'));
    }

    public function edit($id)
    {
        $item = Machine::with(['operations' => function($query) {
            $query->latest('recorded_at');
        }])->findOrFail($id);
        
        return view('admin.machine-monitor.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'serial_number' => 'required',
                'power_plant_id' => 'required|exists:power_plants,id',
                'dmn' => 'required|numeric',
                'dmp' => 'required|numeric',
                'load_value' => 'required|numeric',
            ]);

            $machine = Machine::findOrFail($id);
            
            // Update data mesin
            $machine->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'serial_number' => $validated['serial_number'],
                'power_plant_id' => $validated['power_plant_id'],
            ]);

            // Update atau buat data operasi mesin baru
            MachineOperation::updateOrCreate(
                ['machine_id' => $machine->id],
                [
                    'dmn' => $validated['dmn'],
                    'dmp' => $validated['dmp'],
                    'load_value' => $validated['load_value'],
                    'recorded_at' => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Mesin berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating machine: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showAll()
    {
        $machines = Machine::with(['powerPlant'])
                          ->orderBy('id')
                          ->paginate(10);
                          
        return view('admin.machine-monitor.show', compact('machines'));
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Verifikasi password
            if (!Hash::check($request->password, Auth::user()->password)) {
                return back()->with('error', 'Password yang Anda masukkan salah');
            }

            $machine = Machine::findOrFail($id);
            $machine->delete();

            return redirect()->route('admin.machine-monitor')
                ->with('success', 'Data mesin berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data mesin: ' . $e->getMessage());
        }
    }
}
