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
            DB::beginTransaction();
            
            // Log awal proses
            Log::info('Mulai generate QR Code', [
                'user_id' => auth()->id(),
                'time' => now()
            ]);
            
            // Generate token
            $token = 'ATT-' . strtoupper(Str::random(8));
            
            // Log token yang dibuat
            Log::info('Token generated', ['token' => $token]);
            
            // Siapkan data untuk token
            $tokenData = [
                'token' => $token,
                'user_id' => auth()->id(),
                'expires_at' => now()->addHours(24),
                'unit_source' => session('unit', 'poasia')
            ];
            
            // Log data yang akan disimpan
            Log::info('Data yang akan disimpan', $tokenData);
            
            // Simpan token
            $attendanceToken = AttendanceToken::create($tokenData);
            
            // Log hasil penyimpanan
            Log::info('Token berhasil disimpan', ['attendance_token_id' => $attendanceToken->id]);
            
            // Buat URL untuk QR
            $qrUrl = route('attendance.scan', ['token' => $token]);
            
            // Log URL yang dibuat
            Log::info('QR URL generated', ['url' => $qrUrl]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl,
                'debug_info' => [
                    'token' => $token,
                    'expires_at' => $tokenData['expires_at']->toDateTimeString(),
                    'user_id' => $tokenData['user_id']
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log error detail
            Log::error('QR Code Generation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code: ' . $e->getMessage(),
                'debug_info' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
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