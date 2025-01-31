<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

    public function destroy(Request $request, $id)
    {
        try {
            // Verifikasi password
            if (!Hash::check($request->password, Auth::user()->password)) {
                return back()->with('error', 'Password yang Anda masukkan salah');
            }

            $powerPlant = PowerPlant::findOrFail($id);
            $powerPlant->delete();

            return redirect()->route('admin.power-plants.index')
                ->with('success', 'Unit pembangkit berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus unit pembangkit: ' . $e->getMessage());
        }
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

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // Cari power plant berdasarkan ID
            $powerPlant = PowerPlant::findOrFail($id);

            // Update data
            $powerPlant->update($validated);

            // Redirect dengan pesan sukses
            return redirect()
                ->route('admin.power-plants.index')
                ->with('success', 'Unit pembangkit berhasil diperbarui');

        } catch (\Exception $e) {
            // Redirect dengan pesan error
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui unit pembangkit: ' . $e->getMessage());
        }
    }
} 