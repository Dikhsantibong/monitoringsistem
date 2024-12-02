<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance; // Tambahkan ini untuk mengimport model Attendance

class DaftarHadirController extends Controller
{
    public function index()
    {
        $attendances = Attendance::all(); // Tambahkan ini untuk mengambil semua data kehadiran
        return view('admin.daftar_hadir.index', compact('attendances')); // Tambahkan compact untuk mengirimkan data ke view
    }

    // Tambahkan metode lain sesuai kebutuhan
}
