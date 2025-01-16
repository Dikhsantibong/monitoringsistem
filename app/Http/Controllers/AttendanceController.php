<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceToken;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    
 

    
    public function rekapitulasi(Request $request)
    {
        try {
            $attendances = Attendance::query();

            if ($request->filled(['tanggal_awal', 'tanggal_akhir'])) {
                // Konversi tanggal ke WITA
                $tanggalAwal = Carbon::parse($request->tanggal_awal)->setTimezone('Asia/Makassar')->startOfDay();
                $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->setTimezone('Asia/Makassar')->endOfDay();

                $attendances->whereBetween('time', [$tanggalAwal, $tanggalAkhir]);
            } else {
                // Default tampilkan bulan ini dalam WITA
                $now = now()->setTimezone('Asia/Makassar');
                $attendances->whereMonth('time', $now->month)
                           ->whereYear('time', $now->year);
            }

            $attendances = $attendances->get();

            // Hitung statistik dengan waktu WITA
            $totalKehadiran = $attendances->count();
            $tepatWaktu = $attendances->filter(function($item) {
                return Carbon::parse($item->time)
                            ->setTimezone('Asia/Makassar')
                            ->format('H:i:s') <= '08:00:00';
            })->count();
            
            $terlambat = $totalKehadiran - $tepatWaktu;
            
            $statistik = [
                'total' => $totalKehadiran,
                'tepat_waktu' => $tepatWaktu,
                'terlambat' => $terlambat,
                'persentase_tepat' => $totalKehadiran > 0 ? 
                    round(($tepatWaktu / $totalKehadiran) * 100, 2) : 0
            ];

            return view('admin.daftar_hadir.rekapitulasi', compact('attendances', 'statistik'));
            
        } catch (\Exception $e) {
            \Log::error('Error in rekapitulasi: ' . $e->getMessage());
            return back()
                ->with('error', 'Terjadi kesalahan saat memuat data rekapitulasi. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function generateQRCode()
    {
        try {
            $token = 'attendance_' . now()->format('Y-m-d') . '_' . strtolower(Str::random(8));

            // Simpan token di database dengan user_id
            AttendanceToken::create([
                'token' => $token,
                'expires_at' => now()->endOfDay(),
                'user_id' => auth()->id() // Tambahkan user_id dari user yang login
            ]);

            $qrUrl = route('attendance.scan', ['token' => $token]);

            Log::info('Generating QR Code', ['token' => $token, 'url' => $qrUrl]);

            return response()->json([
                'success' => true,
                'token' => $token,
                'qr_url' => $qrUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate QR Code',
            ], 500);
        }
    }

    public function scan($token)
    {
        try {
            $attendanceToken = AttendanceToken::where('token', $token)
                ->where('expires_at', '>=', now())
                ->first();

            if (!$attendanceToken) {
                return redirect()->route('attendance.error')->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
            }

            return view('admin.daftar_hadir.scan', compact('token'));
        } catch (\Exception $e) {
            Log::error('Scan error: ' . $e->getMessage());
            return redirect()->route('attendance.error')->with('error', 'Terjadi kesalahan saat memproses QR Code');
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'name' => 'required|string',
                'position' => 'required|string',
                'division' => 'required|string',
                'token' => 'required|string',
                'signature' => 'required|string'
            ]);

            // Set timezone ke WITA
            $now = now()->setTimezone('Asia/Makassar');

            // Debug log
            \Log::info('Processing attendance with signature', [
                'name' => $request->name,
                'signature_length' => strlen($request->signature),
                'time' => $now->format('Y-m-d H:i:s')
            ]);

            // Buat record attendance dengan waktu WITA
            $attendance = Attendance::create([
                'name' => $request->name,
                'position' => $request->position,
                'division' => $request->division,
                'token' => $request->token,
                'time' => $now,
                'signature' => $request->signature
            ]);

            return redirect()->route('attendance.success')
                ->with('success', 'Absensi berhasil disimpan');

        } catch (\Exception $e) {
            \Log::error('Error saving attendance: ' . $e->getMessage());
            return back()
                ->with('error', 'Gagal menyimpan absensi. Silakan coba lagi.')
                ->withInput();
        }
    }

    // Tambahkan method untuk menampilkan tanda tangan
    public function showSignature($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            return response()->json([
                'success' => true,
                'signature' => $attendance->signature
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tanda tangan tidak ditemukan'
            ], 404);
        }
    }
    
} 