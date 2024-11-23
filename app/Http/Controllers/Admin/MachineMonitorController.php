<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Machine;

class MachineMonitorController extends Controller
{
    public function index()
    {
        // Data dummy untuk testing
        $data = [
            'machines' => collect([
                (object)[
                    'id' => 1,
                    'name' => 'Machine A',
                    'code' => 'MCH-001',
                    'status' => 'START',
                    'operational_hours' => 1200,
                    'metrics' => collect([
                        (object)['metric_name' => 'Temperature', 'current_value' => 75, 'target_value' => 80, 'achievement_percentage' => 93.75],
                        (object)['metric_name' => 'Pressure', 'current_value' => 120, 'target_value' => 150, 'achievement_percentage' => 80],
                    ]),
                    'issues' => collect([]),
                ],
            ]),
            'healthCategories' => collect([
                (object)[
                    'id' => 1,
                    'name' => 'Cooling Water',
                    'description' => 'Cooling system status',
                    'open_issues' => 2,
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Electrical',
                    'description' => 'Electrical system status',
                    'open_issues' => 1,
                ],
            ]),
            'monthlyIssues' => [
                1 => 5,  // January
                2 => 3,  // February
                3 => 7,  // March
                4 => 2,  // April
                5 => 4,  // May
                6 => 6,  // June
            ],
            'recentIssues' => collect([
                (object)[
                    'id' => 1,
                    'description' => 'High temperature warning',
                    'status' => 'open',
                    'created_at' => now()->subHours(2),
                    'machine' => (object)['name' => 'Machine A'],
                    'category' => (object)['name' => 'Cooling Water'],
                ],
            ]),
        ];

        return view('admin.machine-monitor.index', $data);
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