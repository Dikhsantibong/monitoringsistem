<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class PowerPlantController extends Controller
{
    public function ready()
    {
        $units = Unit::all(); // Ambil semua data unit pembangkit
        return view('admin.pembangkit.ready', compact('units'));
    }
} 