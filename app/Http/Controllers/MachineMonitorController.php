<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MachineStatusLog;
use App\Models\PowerPlant;

class MachineMonitorController extends Controller
{
    public function index()
    {
        $machines = Machine::with(['statusLogs', 'machineOperations'])->get();
        
        return view('admin.machine-monitor.index', [
            'machines' => $machines,
            // ... other data ...
        ]);
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

    public function edit($id)
    {
        $machine = Machine::findOrFail($id);
        return view('admin.machine-monitor.edit', compact('machine'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'serial_number' => 'required|string|max:50',
            'powerPlant' => 'required|string|max:255',
            'status' => 'required|in:START,STOP,PARALLEL',
        ]);

        $machine = Machine::findOrFail($id);
        $machine->update($validated);

        return redirect()->route('admin.machine-monitor')->with('success', 'Mesin berhasil diperbarui');
    }
}
