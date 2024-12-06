<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')
            ->orderBy('attended_at', 'desc')
            ->get();

        return view('admin.daftar_hadir.index', compact('attendances'));
    }

    public function generateQrCode()
    {
        $user = auth()->user();
        $timestamp = now()->timestamp;
        $qrCode = "attendance_{$timestamp}_" . Str::random(10);
        
        return response()->json([
            'code' => $qrCode,
            'valid_until' => now()->addMinutes(5)->timestamp
        ]);
    }

    public function recordAttendance(Request $request)
    {
        try {
            $user = auth()->user();
            $qrCode = $request->qr_code;

            // Cek apakah user sudah absen hari ini
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('attended_at', now())
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah melakukan absensi hari ini'
                ], 400);
            }

            // Catat kehadiran baru
            Attendance::create([
                'user_id' => $user->id,
                'qr_code' => $qrCode,
                'attended_at' => now(),
                'is_valid' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi berhasil dicatat'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencatat absensi'
            ], 500);
        }
    }
} 