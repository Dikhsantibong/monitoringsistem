<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MachineStatusLog;
use App\Models\PowerPlant;
use Illuminate\Http\Request;

class MachineStatusViewController extends Controller
{
    public function index()
    {
        $units = [
            'UP KENDARI',
            'ULPLTD POASIA',
            'ULPLTD WUA WUA',
            'ULPLTD KOLAKA',
            'ULPLTD BAU BAU'
        ];

        $selectedUnit = request('unit', 'UP KENDARI');
        $date = request('date', now()->format('Y-m-d'));

        $logs = MachineStatusLog::with(['machine', 'powerPlant'])
            ->whereHas('powerPlant', function($query) use ($selectedUnit) {
                $query->where('power_plants.name', $selectedUnit);
            })
            ->whereDate('tanggal', $date)
            ->latest()
            ->get();

        return view('admin.machine-status.view', compact('units', 'logs', 'selectedUnit', 'date'));
    }
} 