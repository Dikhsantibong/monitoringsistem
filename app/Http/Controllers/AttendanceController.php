<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')
            ->orderBy('attended_at', 'desc')
            ->get();

        return view('admin.daftar_hadir.index', compact('attendances'));
    }
} 