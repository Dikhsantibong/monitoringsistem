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
            Log::info('Accessing attendance scan form', [
                'token' => $token,
                'timestamp' => now()->toDateTimeString()
            ]);
            
            // Validasi token dengan logging
            $validToken = AttendanceToken::where('token', $token)
                ->where('expires_at', '>', now())
                ->first();
            
            Log::info('Token validation result', [
                'token' => $token,
                'valid' => !is_null($validToken),
                'token_data' => $validToken
            ]);
            
            if (!$validToken) {
                Log::warning('Invalid attendance token', [
                    'token' => $token,
                    'timestamp' => now()->toDateTimeString()
                ]);
                
                return view('attendance.scan-from', [
                    'token' => null,
                    'error' => 'QR Code tidak valid atau sudah kadaluarsa. Silakan scan ulang QR Code yang baru.'
                ]);
            }
            
            return view('attendance.scan-from', [
                'token' => $token,
                'tokenData' => $validToken
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in attendance scan form', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('attendance.scan-from', [
                'token' => null,
                'error' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.'
            ]);
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
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'name' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'position' => 'required|string|max:255'
            ]);

            // Proses submit attendance
            // ...

            return redirect()->back()->with('success', 'Absensi berhasil dicatat');
        } catch (\Exception $e) {
            Log::error('Error submitting attendance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mencatat absensi')
                ->withInput();
        }
    }
    public function rekapitulasi(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tanggal_awal' => 'nullable|date',
                'tanggal_akhir' => 'nullable|date'
            ]);

            $query = DB::table('attendance');
            
            // Filter berdasarkan rentang tanggal
            if ($request->filled(['tanggal_awal', 'tanggal_akhir'])) {
                $query->whereDate('time', '>=', $request->tanggal_awal)
                      ->whereDate('time', '<=', $request->tanggal_akhir);
            } else {
                // Default tampilkan bulan ini
                $query->whereMonth('time', now()->month)
                      ->whereYear('time', now()->year);
            }

            // Log query untuk debugging
            \Log::info('Query rekapitulasi:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $kehadiran = $query->orderBy('time', 'desc')->get();

            // Hitung statistik
            $totalKehadiran = $kehadiran->count();
            $tepatWaktu = $kehadiran->filter(function($item) {
                return Carbon::parse($item->time)->format('H:i:s') <= '08:00:00';
            })->count();
            
            $terlambat = $totalKehadiran - $tepatWaktu;
            
            $statistik = [
                'total' => $totalKehadiran,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'persentase_tepat' => $totalKehadiran > 0 ? 
                    round(($tepatWaktu / $totalKehadiran) * 100, 2) : 0
            ];

            // Log data untuk debugging
            \Log::info('Data statistik:', $statistik);

            return view('admin.daftar_hadir.rekapitulasi', compact('kehadiran', 'statistik'));
            
        } catch (\Exception $e) {
            \Log::error('Error in rekapitulasi: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->with('error', 'Terjadi kesalahan saat memuat data rekapitulasi. Silakan coba lagi.')
                ->withInput();
        }
    }
} 