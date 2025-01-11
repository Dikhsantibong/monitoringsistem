<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UnitGroupService;
use App\Models\MachineStatusLog;
use App\Models\PowerPlant;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MachineStatusController extends Controller
{
    public function view(Request $request)
    {
        try {
            // Set default date ke hari ini jika tidak ada di request
            $date = $request->get('date', Carbon::today()->format('Y-m-d'));
            $selectedUnit = $request->get('unit', 'ULPLTD BAU BAU');
            
            // Ambil semua power plant dengan relasi machines dan logs
            $powerPlants = PowerPlant::with(['machines' => function($query) {
                $query->orderBy('name');
            }])->get();

            // Ambil logs untuk tanggal yang dipilih
            $logs = MachineStatusLog::with(['machine.powerPlant'])
                ->whereDate('tanggal', $date)
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.machine-status._table', [
                        'powerPlants' => $powerPlants,
                        'logs' => $logs,
                        'date' => $date
                    ])->render()
                ]);
            }

            return view('admin.machine-status.view', compact('powerPlants', 'logs', 'date'));

        } catch (\Exception $e) {
            \Log::error('Error in MachineStatusController:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 