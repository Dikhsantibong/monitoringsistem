<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceToken;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
            Log::info('Attempting to validate token: ' . $token); // Perbaikan untuk menggunakan Log
            
            // Cek token di database dengan validasi yang lebih longgar
            $validToken = AttendanceToken::where('token', $token)
                ->whereDate('created_at', Carbon::today())
                ->first();
            
            if (!$validToken) {
                Log::warning('Invalid token or token not found: ' . $token); // Perbaikan untuk menggunakan Log
                return view('attendance.scan-from', ['token' => null])
                       ->with('error', 'QR Code tidak valid atau sudah kadaluarsa. Silakan scan ulang QR Code yang baru.');
            }
            
            Log::info('Valid token found: ' . $token); // Perbaikan untuk menggunakan Log
            return view('attendance.scan-from', compact('token'));
            
        } catch (\Exception $e) {
            Log::error('Error validating token: ' . $e->getMessage()); // Perbaikan untuk menggunakan Log
            return view('attendance.scan-from', ['token' => null])
                   ->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function storeToken(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Hapus token lama untuk hari ini
            AttendanceToken::whereDate('created_at', '<', Carbon::today())->delete();
            
            // Buat token baru
            $token = new AttendanceToken();
            $token->token = $request->token;
            $token->expires_at = Carbon::now()->endOfDay();
            $token->created_at = Carbon::now();
            $token->save();
            
            DB::commit();
            
            Log::info('Token stored successfully: ' . $request->token); // Perbaikan untuk menggunakan Log
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to store token: ' . $e->getMessage()); // Perbaikan untuk menggunakan Log
            return response()->json(['success' => false], 500);
        }
    }

    public function submitAttendance(Request $request)
    {
        if ($request->method() !== 'POST') {
            return response()->json([
                'error' => 'Method not allowed'
            ], 405);
        }
        DB::beginTransaction();
        
        try {
            // Debug log
            Log::info('Form data received:', $request->all());

            // Validasi token
            $token = AttendanceToken::where('token', $request->token)
                ->whereDate('created_at', Carbon::today())
                ->first();
            
            if (!$token) {
                throw new \Exception('Token tidak valid atau sudah kadaluarsa');
            }

            // Cek duplikasi attendance
            $existingAttendance = DB::table('attendance')
                ->where('token', $request->token)
                ->where('name', $request->name)
                ->whereDate('time', Carbon::today())
                ->first();

            if ($existingAttendance) {
                throw new \Exception('Anda sudah melakukan absensi hari ini');
            }

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'token' => 'required|string'
            ]);

            // Simpan attendance
            $saved = DB::table('attendance')->insert([
                'name' => $request->name,
                'division' => $request->division,
                'position' => $request->position,
                'token' => $request->token,
                'time' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$saved) {
                throw new \Exception('Gagal menyimpan kehadiran');
            }

            DB::commit();
            
            Log::info('Attendance saved successfully');
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kehadiran berhasil dicatat!'
                ]);
            }
            
            return redirect()
                ->back()
                ->with('success', 'Kehadiran berhasil dicatat!');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving attendance: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
} 