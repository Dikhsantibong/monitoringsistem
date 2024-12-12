<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Category;
use Illuminate\Http\Request;

class MachineMonitorController extends Controller
{
    public function index()
    {
        $machines = Machine::with('metrics')->get();
        $efficiencyData = $machines->map(function ($machine) {
            return [
                'name' => $machine->name,

        return view('admin.machine-monitor.index', compact('efficiencyData'));
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
}
