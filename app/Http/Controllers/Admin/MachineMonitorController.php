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
            return [
                'name' => $machine->name,
                'uptime' => $machine->status === 'START' ? 100 : 0,
                'downtime' => $machine->status === 'STOP' ? 100 : 0,
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'serial_number' => 'required|string|max:50',
                'power_plant_id' => 'required|exists:power_plants,id',
            ]);

            // Tambahkan default value untuk capacity
            $validated['capacity'] = 0;

            $machine = Machine::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data mesin berhasil ditambahkan!',
                'redirect_url' => route('admin.machine-monitor.show')
            ]);
        } catch (\Exception $e) {
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
        try {
            $machine->delete();
            Alert::success('Berhasil', 'Mesin berhasil dihapus');
            return response()->json([
                'success' => true,
                'message' => 'Mesin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
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

    public function show()
    {
        $machines = Machine::with('powerPlant')
                          ->orderBy('id')
                          ->paginate(10); // Ubah ke 10 item per halaman
        
        return view('admin.machine-monitor.show', compact('machines'));
    }

    public function edit($id)
    {
        $item = Machine::findOrFail($id);
        $powerPlants = PowerPlant::all(); // Tambahkan ini untuk mendapatkan daftar unit
        return view('admin.machine-monitor.edit', compact('item', 'powerPlants'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'serial_number' => 'required',
                'power_plant_id' => 'required|exists:power_plants,id',
            ]);

            $machine = Machine::findOrFail($id);
            $machine->update($request->only([
                'name',
                'type',
                'serial_number',
                'power_plant_id'
            ]));

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

    public function destroy($id)
    {
        try {
            $machine = Machine::findOrFail($id);
            $machineName = $machine->name;
            $machine->delete();

            return redirect()->route('admin.machine-monitor.show.all')
                ->with('success', "Mesin $machineName berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->route('admin.machine-monitor.show.all')
                ->with('error', 'Gagal menghapus mesin: ' . $e->getMessage());
        }
    }
}
