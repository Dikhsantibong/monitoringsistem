<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;

use Illuminate\Http\Request;


class PowerPlantController extends Controller
{
    public function index(Request $request)
    {
        $query = PowerPlant::with(['machines', 'machines.statusLogs']);

        // Pencarian
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'LIKE', "%{$searchTerm}%");
        }

        $powerPlants = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.power-plants.table', compact('powerPlants'))->render();
        }

        return view('admin.power-plants.index', compact('powerPlants'));
    }

    public function edit($id)
    {
        $powerPlant = PowerPlant::findOrFail($id);
        return view('admin.power-plants.edit', compact('powerPlant'));
    }

    public function destroy($id)
    {
        $powerPlant = PowerPlant::findOrFail($id);
        $powerPlant->delete();
        
        return redirect()->route('admin.power-plants.index')
            ->with('success', 'Unit berhasil dihapus');
    }

    public function create()
    {
        return view('admin.power-plants.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // Set nilai default jika kosong
            $validated['latitude'] = $validated['latitude'] ?? 0;
            $validated['longitude'] = $validated['longitude'] ?? 0;

            PowerPlant::create($validated);

            return redirect()
                ->route('admin.power-plants.index')
                ->with('success', 'Unit pembangkit berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan unit pembangkit. ' . $e->getMessage());
        }
    }
} 