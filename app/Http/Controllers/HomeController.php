<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;

class HomeController extends Controller
{
    public function index()
    {
        $units = Machine::with(['powerPlant', 'machineOperations', 'operations'])->get();
        $markers = Marker::all()->toArray();
        $machineOperations = MachineOperation::all(); // Ambil semua data dari tabel machine_operations

        // Hitung total capacity    
        $total_capacity = $units->sum('capacity');
        
        // Hitung total units
        $total_units = $units->count();
        
        // Hitung active units
        $active_units = $units->where('status', 'Aktif')->count();

        // Ambil data untuk grafik
        $total_capacity_data = $units->map(function ($unit) {
            return $unit->capacity;
        })->toArray();

        $total_units_data = $units->map(function ($unit) {
            return $unit->machineOperations->count();
        })->toArray();

        $active_units_data = $units->map(function ($unit) {
            return $unit->status === 'Aktif' ? 1 : 0;
        })->toArray();

        $dates = $units->map(function ($unit) {
            return $unit->created_at->format('M Y');
        })->unique()->toArray();

        return view('homepage', compact(
            'units',
            'markers',
            'total_capacity',
            'total_units',
            'active_units',
            'total_capacity_data',
            'total_units_data',
            'active_units_data',
            'dates'
        ));
    }
}
