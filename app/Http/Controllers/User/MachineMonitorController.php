<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MachineMonitorController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari request atau gunakan tanggal hari ini
        $date = $request->get('date', date('Y-m-d'));

        // Ambil semua power plant dengan relasi machines
        $powerPlants = PowerPlant::with(['machines' => function ($query) {
            $query->orderBy('name');
        }])->get();

        // Ambil log status mesin untuk tanggal yang dipilih
        $machineStatusLogs = MachineStatusLog::whereDate('created_at', $date)
            ->whereIn('machine_id', $powerPlants->pluck('machines')->flatten()->pluck('id'))
            ->get();

        // Jika request adalah AJAX, return partial view
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('user.machine-monitor._table', compact('powerPlants', 'machineStatusLogs', 'date'))->render()
            ]);
        }

        // Return view lengkap
        return view('user.machine-monitor', compact('powerPlants', 'machineStatusLogs', 'date'));
    }
} 