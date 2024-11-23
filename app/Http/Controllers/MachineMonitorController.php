<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineHealthCategory;
use App\Models\MachineIssue;
use App\Models\MachineMetric;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MachineMonitorController extends Controller
{
    public function index()
    {
        $data = [
            'machines' => Machine::with(['metrics', 'issues'])->get(),
            'healthCategories' => MachineHealthCategory::withCount(['issues as open_issues' => function($query) {
                $query->where('status', 'open');
            }])->get(),
            'monthlyIssues' => $this->getMonthlyIssues(),
            'metrics' => MachineMetric::with('machine')->get(),
            'recentIssues' => MachineIssue::with(['machine', 'category'])
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('admin.machine-monitor.index', $data);
    }

    private function getMonthlyIssues()
    {
        return MachineIssue::selectRaw('COUNT(*) as count, MONTH(created_at) as month')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
    }

    public function storeIssue(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'category_id' => 'required|exists:machine_health_categories,id',
            'description' => 'required|string',
        ]);

        MachineIssue::create($validated + ['status' => 'open']);

        return redirect()->back()->with('success', 'Issue reported successfully');
    }

    public function updateMachineStatus(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'status' => 'required|in:START,STOP,PARALLEL',
        ]);

        $machine->update($validated);

        return response()->json(['success' => true]);
    }

    public function updateMetrics(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'metrics.*.name' => 'required|string',
            'metrics.*.value' => 'required|numeric',
            'metrics.*.target' => 'required|numeric',
        ]);

        foreach ($validated['metrics'] as $metric) {
            $machine->metrics()->updateOrCreate(
                ['metric_name' => $metric['name']],
                [
                    'current_value' => $metric['value'],
                    'target_value' => $metric['target'],
                    'achievement_percentage' => ($metric['value'] / $metric['target']) * 100
                ]
            );
        }

        return response()->json(['success' => true]);
    }
} 