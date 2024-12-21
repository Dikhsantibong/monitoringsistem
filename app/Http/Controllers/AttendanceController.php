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
            Log::info('Accessing scan form with token: ' . $token);
            
            // Validasi token
            $validToken = AttendanceToken::where('token', $token)
                ->whereDate('created_at', Carbon::today())
                ->first();
            
            if (!$validToken) {
                Log::warning('Invalid token accessed: ' . $token);
                return view('attendance.scan-from', ['token' => null])
                    ->with('error', 'QR Code tidak valid atau sudah kadaluarsa.');
            }
            
            return view('attendance.scan-from', compact('token'));
            
        } catch (\Exception $e) {
            Log::error('Error in showScanForm: ' . $e->getMessage());
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