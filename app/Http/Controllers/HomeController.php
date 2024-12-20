<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $units = DB::connection('u478221055_up_kendari')->table('units')->get();
        $markers = DB::connection('u478221055_up_kendari')->table('markers')->get();

        // Hitung total capacity    
        $total_capacity = $units->sum('capacity');
        
        // Hitung total units
        $total_units = $units->count();
        
        // Hitung active units
        $active_units = $units->where('status', 'Aktif')->count();

        // Ambil data untuk grafik dari database u478221055_up_kendari
        $machineOperations = DB::connection('u478221055_up_kendari')
            ->table('machine_operations')
            ->join('machines', 'machine_operations.machine_id', '=', 'machines.id')
            ->select('machine_operations.*', 'machines.capacity')
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->recorded_at)->format('Y-m-d');
            });

        $dmn_data = [];
        $dmp_data = [];
        $load_value_data = [];
        $capacity_data = [];
        $total_capacity_data = [];
        $total_units_data = [];
        $active_units_data = [];
        $dates = [];

        foreach ($machineOperations as $date => $operations) {
            $dates[] = $date;
            $dmn_data[] = $operations->avg('dmn');
            $dmp_data[] = $operations->avg('dmp');
            $load_value_data[] = $operations->avg('load_value');
            $capacity_data[] = $operations->first()->capacity;
            $total_capacity_data[] = $total_capacity;
            $total_units_data[] = $total_units;
            $active_units_data[] = $active_units;
        }

        return view('homepage', compact(
            'units',
            'markers',
            'total_capacity',
            'total_units',
            'active_units',
            'dmn_data',
            'dmp_data',
            'load_value_data',
            'capacity_data',
            'total_capacity_data',
            'total_units_data',
            'active_units_data',
            'dates'
        ));
    }
}
