<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marker;

class PembangkitController extends Controller
{
    public function ready()
    {
        $units = Marker::all();
        return view('admin.pembangkit.ready', compact('units'));
    }
    

    // Tambahkan metode lain sesuai kebutuhan
}
