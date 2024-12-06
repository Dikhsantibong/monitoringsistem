<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;
use Carbon\Carbon;

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

        // Mengambil data untuk grafik dari MachineOperation
        $machineOperations = MachineOperation::with(['machine.powerPlant'])
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->recorded_at)->format('Y-m-d');
            });

        $dmn_data = [];
        $dmp_data = [];
        $load_value_data = [];
        $capacity_data = [];
        $dates = [];

        foreach ($machineOperations as $date => $operations) {
            $dates[] = $date;
            $dmn_data[] = $operations->avg('dmn');
            $dmp_data[] = $operations->avg('dmp');
            $load_value_data[] = $operations->avg('load_value');
            $capacity_data[] = $operations->first()->machine->capacity;
        }

        return view('homepage', compact(
            'units',
            'markers',
            'total_capacity',
            'total_units',
            'active_units',
            'total_capacity_data',
            'total_units_data',
            'active_units_data',
            'dates',
            'dmn_data',
            'dmp_data',
            'load_value_data',
            'capacity_data'
        ));
    }
}
