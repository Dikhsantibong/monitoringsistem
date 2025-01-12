<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class MachineStatusController extends Controller
{
    public function view(Request $request)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            
            // Ambil data powerplant dengan machines
            $powerPlants = PowerPlant::with(['machines'])->get();
            
            // Ambil logs untuk tanggal yang dipilih
            $logs = MachineStatusLog::whereDate('tanggal', $date)->get();

            if ($request->ajax()) {
                $html = View::make('admin.machine-status._table', compact('powerPlants', 'date', 'logs'))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html
                ]);
            }

            return view('admin.machine-status.view', compact('powerPlants', 'date', 'logs'));
            
        } catch (\Exception $e) {
            \Log::error('Error in MachineStatusController@view: ' . $e->getMessage());
            
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