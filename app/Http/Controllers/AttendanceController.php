<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        $attendances = Attendance::query()
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            return view('admin.daftar_hadir._table_body', compact('attendances'));
        }

        return view('admin.daftar_hadir.index', compact('attendances'));
    }

    public function recordAttendance(Request $request)
    {
        try {
            $user = auth()->user();
            $qrCode = $request->qr_code;

            // Validasi format QR code
            if (!str_starts_with($qrCode, 'attendance_')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code tidak valid'
                ], 400);
            }

            // Cek apakah QR code masih valid (5 menit)
            $timestamp = explode('_', $qrCode)[1];
            if (now()->timestamp - $timestamp > 300) { // 300 detik = 5 menit
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code sudah kadaluarsa'
                ], 400);
            }

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