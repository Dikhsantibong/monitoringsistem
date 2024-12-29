<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MachineStatusLog;

class MachineMonitorController extends Controller
{
    public function index()
    {
        $machines = Machine::with('metrics')->get();
        $efficiencyData = $machines->map(function ($machine) {
            return [
                'name' => $machine->name,
                'metrics' => $machine->metrics->pluck('value')->toArray(),
            ];
        });

        // Gunakan data dummy untuk testing
        $monthlyIssues = MachineStatusLog::getDummyMonthlyData();
        $activeIssues = MachineStatusLog::getDummyActiveIssues();

        return view('admin.machine-monitor.index', compact(
            'machines',
            'efficiencyData',
            'monthlyIssues',
            'activeIssues'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.machine-monitor.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:machines',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'status' => 'required|in:START,STOP,PARALLEL',
            'uptime' => 'required|integer|min:0',
        ]);

        $machine = Machine::create($validated);

        return redirect()->route('admin.machine-monitor')->with('success', 'Mesin berhasil ditambahkan');
    }

    public function showAll()
    {
        $machines = Machine::all();
        return view('admin.machine-monitor.show', compact('machines'));
    }
}
