<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerPlant;
use App\Models\MachineStatusLog;
use App\Models\UnitOperationHour;
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
            $logsQuery = MachineStatusLog::with(['machine', 'powerPlant'])
                ->whereDate('tanggal', $date);
            
            if ($searchQuery) {
                $logsQuery->where(function($query) use ($searchQuery) {
                    $query->whereHas('machine', function($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%");
                    })
                    ->orWhere('status', 'like', "%{$searchQuery}%");
                });
            }
            
            $logs = $logsQuery->get();

            // Get unit operation hours dengan eager loading powerPlant
            $hopQuery = UnitOperationHour::with('powerPlant')
                ->whereDate('tanggal', $date);

            // Filter berdasarkan unit_source jika ada
            if ($unitSource) {
                $hopQuery->where('unit_source', $unitSource);
            } elseif (session('unit') !== 'mysql') {
                $hopQuery->where('unit_source', session('unit'));
            }

            // Ambil data HOP dan kelompokkan berdasarkan power_plant_id
            $unitOperationHours = $hopQuery->get()->mapWithKeys(function ($item) {
                return [$item->power_plant_id => $item];
            });

            // Hitung total HOP untuk setiap pembangkit
            $totalHopByPlant = [];
            foreach ($powerPlants as $powerPlant) {
                $hop = $unitOperationHours->get($powerPlant->id);
                $totalHopByPlant[$powerPlant->id] = [
                    'value' => $hop ? $hop->hop_value : 0,
                    'status' => $hop && $hop->hop_value >= 7 ? 'aman' : 'siaga'
                ];
            }

            if ($request->ajax()) {
                $html = View::make('admin.machine-status._table', compact(
                    'powerPlants', 
                    'date', 
                    'logs', 
                    'unitOperationHours',
                    'totalHopByPlant'
                ))->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html
                ]);
            }

            return view('admin.machine-status.view', compact(
                'powerPlants', 
                'date', 
                'logs', 
                'unitOperationHours',
                'totalHopByPlant'
            ));
            
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
