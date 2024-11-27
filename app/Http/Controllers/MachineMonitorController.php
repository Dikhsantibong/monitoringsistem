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
        $machines = Machine::all();

        $uptime = [
            'START' => $machines->where('status', 'START')->sum('uptime'),
            'STOP' => $machines->where('status', 'STOP')->sum('uptime'),
            'PARALLEL' => $machines->where('status', 'PARALLEL')->sum('uptime'),
        ];

        return view('admin.machine-monitor.index', compact('machines', 'uptime'));
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