<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineIssue;
use App\Models\MachineHealthCategory;
use Illuminate\Http\Request;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:machines,code',
            'status' => 'required|in:START,STOP,PARALLEL'
        ]);

        Machine::create($validated);

        return redirect()->route('admin.machine-monitor')
            ->with('success', 'Machine added successfully');
    }

    public function showMachine(Machine $machine)
    {
        return response()->json($machine);
    }

    public function updateMachine(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:machines,code,' . $machine->id,
            'status' => 'required|in:START,STOP,PARALLEL'
        ]);

        $machine->update($validated);

        return response()->json(['success' => true]);
    }

    public function destroyMachine(Machine $machine)
    {
        $machine->delete();
        return response()->json(['success' => true]);
    }
} 