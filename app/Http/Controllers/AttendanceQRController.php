<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceToken;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceQRController extends Controller
{
    public function index()
    {
        // Ambil data absensi hari ini
        $attendances = Attendance::whereDate('time', Carbon::today())
            ->orderBy('time', 'desc')
            ->get();
            
        return view('admin.attendance.qr', compact('attendances'));
    }

    public function generate()
    {
        try {
            // Generate token sederhana seperti controller lain
            $token = 'ATT-' . strtoupper(Str::random(8));
            
            // Gunakan koneksi yang sesuai dengan session atau default
            $connection = session('unit', 'mysql');
            
            // Insert token ke database
            DB::connection($connection)->table('attendance_tokens')->insert([
                'token' => $token,
                'user_id' => auth()->id(),
                'expires_at' => now()->addMinutes(15),
                'unit_source' => $connection,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // URL untuk QR - gunakan app.url atau external url jika ada
            $externalUrl = config('services.attendance_external.url');
            if ($externalUrl) {
                $qrUrl = rtrim($externalUrl, '/') . '/scan/' . $token;
            } else {
                $qrUrl = url("/attendance/scan/{$token}");
            }

            Log::info('QR Code generated', [
                'token' => $token,
                'url' => $qrUrl,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl,
                'token' => $token
            ]);
            
        } catch (\Exception $e) {
            Log::error('QR Code generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code: ' . $e->getMessage()
            ], 500);
        }
    }
}
