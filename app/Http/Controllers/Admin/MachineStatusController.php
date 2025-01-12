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
            $searchQuery = $request->get('search');
            
            // Query power plants
            $powerPlantsQuery = PowerPlant::with(['machines']);
            
            // Filter berdasarkan unit_source
            if (session('unit') === 'mysql') {
                if ($unitSource) {
                    $powerPlantsQuery->where('unit_source', $unitSource);
                }
            } else {
                $powerPlantsQuery->where('unit_source', session('unit'));
            }
            
            // Filter pencarian untuk power plant
            if ($searchQuery) {
                $powerPlantsQuery->where(function($query) use ($searchQuery) {
                    $query->where('name', 'like', "%{$searchQuery}%")
                        ->orWhereHas('machines', function($q) use ($searchQuery) {
                            $q->where('name', 'like', "%{$searchQuery}%");
                        });
                });
            }
            
            $powerPlants = $powerPlantsQuery->get();
            
            // Get logs dengan filter pencarian
            $logsQuery = MachineStatusLog::whereDate('tanggal', $date);
            
            if ($searchQuery) {
                $logsQuery->where(function($query) use ($searchQuery) {
                    $query->whereHas('machine', function($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    })
                    ->orWhere('status', 'like', "%{$searchQuery}%");
                });
            }
            
            $logs = $logsQuery->get();

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