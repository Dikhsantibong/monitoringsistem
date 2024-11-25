<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineIssue;
use Illuminate\Http\Request;

class MachineMonitorController extends Controller
{
    public function index()
    {
        $machines = Machine::with('issues')->get();
        
        // Menghitung uptime dan downtime
        $uptime = $machines->map(function($machine) {
            return [
                'name' => $machine->name,
                'uptime' => $machine->status === 'START' ? 1 : 0, // Contoh sederhana
                'downtime' => $machine->status === 'STOP' ? 1 : 0,
            ];
        });

        // Menghitung monthly issues
        $monthlyIssues = MachineIssue::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count')
            ->toArray();

        return view('admin.machine-monitor.index', [
            'machines' => $machines,
            'uptime' => $uptime,
            'monthlyIssues' => $monthlyIssues,
            'healthCategories' => [], // Ganti dengan data kategori kesehatan jika ada
            'recentIssues' => MachineIssue::with('machine', 'category')->latest()->take(5)->get(),
        ]);
    }

    public function create()
    {

    }

    public function store()
    {

    }

    


    
} 