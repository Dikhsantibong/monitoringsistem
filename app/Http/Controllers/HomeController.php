<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\News;
use App\Models\Marker;
use App\Models\Machine;
use App\Models\PowerPlant;
use App\Models\MachineOperation;

class HomeController extends Controller
{
    public function index()
    {
        $units = Machine::with(['powerPlant', 'operations'])->get()->toArray();
        $markers = Marker::all()->toArray();

        $total_units = count($units);
        $total_capacity = array_sum(array_map(function ($unit) {
            return $unit['capacity'];
        }, $units));
        $active_units = count(array_filter($units, function ($unit) {
            return $unit['status'] === 'Aktif';
        }));

        $total_capacity_data = [100, 200, 150, 300];
        $total_units_data = [10, 20, 15, 30];
        $active_units_data = [5, 10, 8, 15];
        $dates = ['Jan', 'Feb', 'Mar', 'Apr'];

        return view('homepage', compact(
            'units',
            'markers',
            'total_units',
            'total_capacity',
            'active_units',
            'total_capacity_data',
            'total_units_data',
            'active_units_data',
            'dates'
        ));
    }
}
