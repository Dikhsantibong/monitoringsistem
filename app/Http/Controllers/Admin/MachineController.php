<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:machines',
        ]);

        try {
            // Buat mesin baru
            $machine = Machine::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'status' => 'STOP', // Status default
            ]);

            return redirect()->route('admin.machine-monitor')
                ->with('success', 'Mesin berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan mesin: ' . $e->getMessage());
        }
    }
}