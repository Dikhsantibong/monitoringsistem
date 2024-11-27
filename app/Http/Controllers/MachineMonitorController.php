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
        return view('admin.machine-monitor.index', compact('machines'));
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
            'code' => 'required|string|max:255|unique:machines,code',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:START,STOP,PARALLEL',
        ]);
    
        Machine::create($validated);
    
        return response()->json(['success' => true], 200);
    }
    
} 