<?php

namespace App\Http\Controllers;

use App\Models\Attendance; // Pastikan model Attendance diimpor
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::all(); // Ambil semua data kehadiran
        return view('admin.daftar_hadir.index', compact('attendances')); // Kirim data ke view
    }
} 