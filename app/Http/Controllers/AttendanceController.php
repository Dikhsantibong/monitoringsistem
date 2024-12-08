<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('attendance');
        
        // Filter berdasarkan tanggal jika ada
        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('time', $date);
        } else {
            // Default tampilkan hari ini
            $query->whereDate('time', Carbon::today());
        }

        $attendances = $query->orderBy('time', 'desc')->get();
        
        return view('admin.daftar_hadir.index', compact('attendances'));
    }

    public function showScanForm($token)
    {
        // Validasi token
        $tokenParts = explode('_', $token);
        if (count($tokenParts) !== 3 || $tokenParts[0] !== 'attendance') {
            return redirect()->back()->with('error', 'QR Code tidak valid');
        }

        $date = $tokenParts[1];
        if ($date !== date('Y-m-d')) {
            return redirect()->back()->with('error', 'QR Code sudah kadaluarsa');
        }

        return view('attendance.scan-form', compact('token'));
    }

    public function submitAttendance(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'division' => 'required|string|max:255',
            'token' => 'required'
        ]);

        // Validasi token lagi
        $tokenParts = explode('_', $request->token);
        if (count($tokenParts) !== 3 || $tokenParts[0] !== 'attendance' || $tokenParts[1] !== date('Y-m-d')) {
            return redirect()->back()->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
        }

        // Simpan attendance menggunakan query builder
        DB::table('attendance')->insert([
            'name' => $request->name,
            'division' => $request->division,
            'time' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Kehadiran berhasil dicatat!');
    }
} 