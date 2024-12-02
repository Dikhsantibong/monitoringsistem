<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance; 

class DaftarHadirController extends Controller
{
    public function index()
    {
        $attendances = Attendance::all(); 
        return view('admin.daftar_hadir.index', compact('attendances')); 
    }

    // Tambahkan metode lain sesuai kebutuhan
}
