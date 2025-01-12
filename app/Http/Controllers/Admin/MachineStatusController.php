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
            $unitSource = $request->get('unit_source');
            
            // Query power plants
            $powerPlantsQuery = PowerPlant::with(['machines']);
            
            // Filter berdasarkan unit_source hanya jika session unit adalah mysql
            if (session('unit') === 'mysql') {
                if ($unitSource) {
                    $powerPlantsQuery->where('unit_source', $unitSource);
                }
            } else {
                // Jika bukan session mysql, hanya tampilkan data sesuai unit_source session
                $powerPlantsQuery->where('unit_source', session('unit'));
            }
            
            $powerPlants = $powerPlantsQuery->get();
            
            // Get logs for the selected date
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