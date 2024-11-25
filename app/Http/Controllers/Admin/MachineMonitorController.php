<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineIssue;
use App\Models\MachineHealthCategory;
use App\Models\MachineCategory;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class MachineMonitorController extends Controller
{
    public function index()
    {
        // Mengambil data mesin dan relasinya
        $machines = Machine::with(['issues', 'metrics'])->get();
        
        // Menggunakan MachineHealthCategory yang sudah ada
        $healthCategories = MachineHealthCategory::withCount(['issues as open_issues' => function ($query) {
            $query->where('status', 'open');
        }])->get();

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

        return view('admin.machine-monitor.index', compact(
            'machines',
            'healthCategories',
            'monthlyIssues',
            'uptime',
            'recentIssues'
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
            // Debug: tampilkan data yang diterima
            \Log::info('Received data:', $request->all());

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

            // Buat array data yang akan disimpan
            $machineData = [
                'name' => $validated['name'],
                'code' => $validated['code'],
                'category_id' => $validated['category_id'],
                'location' => $validated['location'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null
            ];

            // Debug: tampilkan data yang akan disimpan
            \Log::info('Data to be saved:', $machineData);

            // Simpan data
            $machine = Machine::create($machineData);

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
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'category_id' => 'required|exists:machine_health_categories,id',
            'description' => 'required|string'
        ]);

        try {
            MachineIssue::create($validated);
            
            Alert::success('Berhasil', 'Masalah berhasil dilaporkan');
            return response()->json([
                'success' => true,
                'message' => 'Masalah berhasil dilaporkan'
            ]);
        } catch (\Exception $e) {
            Alert::error('Gagal', 'Gagal melaporkan masalah');
            return response()->json([
                'success' => false,
                'message' => 'Gagal melaporkan masalah'
            ], 500);
        }
    }

    public function showMachine(Machine $machine)
    {
        $machine->load(['metrics', 'issues']);
        return view('admin.machine-monitor.show', compact('machine'));
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
        // Ambil semua kategori untuk dropdown
        $categories = MachineCategory::all();
        return view('admin.machine-monitor.create', compact('categories'));
    }
} 