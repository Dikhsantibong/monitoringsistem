<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceToken;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DaftarHadirController extends Controller
{
    public function index()
    {
        $attendances = Attendance::orderBy('created_at', 'desc')->get();
        return view('admin.daftar_hadir.index', compact('attendances'));
    }

    public function storeToken(Request $request)
    {
        try {
            \Log::info('Received token request:', $request->all());
            
            $token = AttendanceToken::create([
                'token' => $request->token,
                'expires_at' => now()->addDay()
            ]);

            \Log::info('Token stored successfully:', ['token' => $token]);
            
            return response()->json([
                'success' => true,
                'message' => 'Token berhasil disimpan',
                'data' => $token
            ]);
        } catch (\Exception $e) {
            \Log::error('Error storing token: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan token: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateQRCode()
    {
        try {
            $token = 'attendance_' . date('Y-m-d') . '_' . strtolower(Str::random(8));
            
            // Pastikan menggunakan URL lengkap dengan protocol
            $appUrl = config('app.url');
            if (!str_starts_with($appUrl, 'http')) {
                $appUrl = 'https://' . $appUrl;
            }
            
            $qrUrl = $appUrl . '/attendance/scan/' . $token;
            
            // Simpan token dengan waktu kadaluarsa yang benar
            AttendanceToken::create([
                'token' => $token,
                'expires_at' => now()->endOfDay(), // Expired di akhir hari
            ]);

            Log::info('QR Code generated', [
                'token' => $token,
                'url' => $qrUrl,
                'expires_at' => now()->endOfDay()
            ]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'qr_url' => $qrUrl
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code'
            ], 500);
        }
    }
}
