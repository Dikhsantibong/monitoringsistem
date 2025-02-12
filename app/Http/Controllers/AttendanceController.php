<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceToken;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $currentUnit = session('unit', 'mysql');
            
            Log::debug('Loading Index Page', [
                'session_unit' => $currentUnit,
                'database' => Attendance::getCurrentDatabase()
            ]);

            // Gunakan query builder dengan koneksi yang sesuai
            $query = DB::connection($currentUnit)->table('attendance');

            // Filter berdasarkan tanggal
            if ($request->has('date')) {
                $date = Carbon::parse($request->date);
                $query->whereDate('time', $date);
            } else {
                $query->whereDate('time', Carbon::today());
            }

            // Filter berdasarkan unit_source
            $query->where('unit_source', $currentUnit);

            $attendances = $query->orderBy('time', 'desc')->get();

            Log::debug('Fetched Attendance Data', [
                'session_unit' => $currentUnit,
                'record_count' => $attendances->count()
            ]);

            return view('admin.daftar_hadir.index', [
                'attendances' => $attendances,
                'currentUnit' => $currentUnit
            ]);
            
        } catch (\Exception $e) {
            Log::error('Index Page Error:', [
                'message' => $e->getMessage(),
                'session_unit' => $currentUnit
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memuat data kehadiran');
        }
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
            $currentUnit = session('unit', 'mysql');
            
            Log::debug('Generating QR Code', [
                'session_unit' => $currentUnit,
                'database' => Attendance::getCurrentDatabase()
            ]);

            // Generate token dengan prefix unit
            $token = 'ATT-' . strtoupper(Str::random(8));
            
            // Simpan token dengan koneksi yang benar
            DB::connection($currentUnit)->table('attendance_tokens')->insert([
                'token' => $token,
                'expires_at' => now()->addDay(),
                'unit_source' => $currentUnit,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'qr_url' => route('attendance.scan', ['token' => $token]),
                'unit' => ucwords(str_replace('mysql_', '', $currentUnit))
            ]);
            
        } catch (\Exception $e) {
            Log::error('QR Generation Error:', [
                'message' => $e->getMessage(),
                'session_unit' => session('unit', 'mysql')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code'
            ], 500);
        }
    }

    public function scanQR($token)
    {
        try {
            // Cek token di semua database yang ada
            $databases = [
                'mysql_bau_bau' => 'u478221055_ulpltd_bau_bau',
                'mysql_kolaka' => 'u478221055_ulpltd_kolaka',
                'mysql_poasia' => 'u478221055_ulpltd_poasia',
                'mysql_wua_wua' => 'u478221055_ulpltd_wua_wua',
                'mysql' => 'u478221055_up_kendari'
            ];

            $tokenData = null;
            $correctUnit = null;

            foreach ($databases as $unit => $database) {
                $result = DB::connection($unit)
                    ->table('attendance_tokens')
                    ->where('token', $token)
                    ->where('expires_at', '>', now())
                    ->first();

                if ($result) {
                    $tokenData = $result;
                    $correctUnit = $unit;
                    break;
                }
            }

            if (!$tokenData) {
                return redirect()->route('attendance.error')
                    ->with('error', 'QR Code tidak valid atau sudah kadaluarsa');
            }

            // Set session unit berdasarkan token yang ditemukan
            session(['unit' => $correctUnit]);

            Log::debug('Scan QR Success', [
                'token' => $token,
                'unit' => $correctUnit,
                'database' => $databases[$correctUnit]
            ]);

            return view('admin.daftar_hadir.scan', [
                'token' => $token,
                'unit' => $correctUnit
            ]);

        } catch (\Exception $e) {
            Log::error('QR Scan Error:', [
                'message' => $e->getMessage(),
                'token' => $token
            ]);
            
            return redirect()->route('attendance.error')
                ->with('error', 'Terjadi kesalahan saat memproses QR Code');
        }
    }

    public function error()
    {
        return view('admin.daftar_hadir.error');
    }

    public function generateBackdateToken(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'tanggal_absen' => 'required|date|before_or_equal:today',
                'waktu_absen' => 'required',
                'alasan' => 'required|string'
            ]);

            DB::beginTransaction();
            
            // Generate token untuk backdate
            $token = 'BACK-' . strtoupper(Str::random(8));
            
            // Tambahkan is_backdate ke data yang disimpan
            $tokenId = DB::table('attendance_tokens')->insertGetId([
                'token' => $token,
                'user_id' => auth()->id(),
                'expires_at' => now()->addMinutes(5),
                'unit_source' => session('unit', 'mysql'),
                'is_backdate' => true, // Tambahkan field ini
                'backdate_data' => json_encode([
                    'tanggal_absen' => $request->tanggal_absen,
                    'waktu_absen' => $request->waktu_absen,
                    'alasan' => $request->alasan
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$tokenId) {
                throw new \Exception('Gagal menyimpan token');
            }

            DB::commit();

            // URL untuk QR Code
            $qrUrl = url("/attendance/scan/{$token}");

            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl,
                'message' => 'QR Code berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Generate QR Code Error:', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'token' => 'required|string',
                'signature' => 'required|string'
            ]);

            $currentUnit = session('unit', 'mysql');
            
            DB::beginTransaction();
            
            try {
                // Generate ID baru
                $lastId = DB::connection($currentUnit)
                           ->table('attendance')
                           ->max('id') ?? 0;
                $newId = $lastId + 1;

                // Simpan attendance
                $attendance = new Attendance();
                $attendance->setConnection($currentUnit);
                $attendance->fill([
                    'id' => $newId,
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                    'division' => $validated['division'],
                    'token' => $validated['token'],
                    'signature' => $validated['signature'],
                    'time' => now(),
                    'unit_source' => $currentUnit
                ]);

                $attendance->save();

                // Update token status
                DB::connection($currentUnit)
                    ->table('attendance_tokens')
                    ->where('token', $validated['token'])
                    ->update(['used_at' => now()]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Absensi berhasil disimpan'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Attendance Store Error:', [
                'message' => $e->getMessage(),
                'unit' => session('unit', 'mysql')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function success()
    {
        return view('admin.daftar_hadir.success');
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

    public function exportExcel(Request $request)
    {
        try {
            $tanggalAwal = $request->get('tanggal_awal', now()->startOfMonth()->format('Y-m-d'));
            $tanggalAkhir = $request->get('tanggal_akhir', now()->endOfMonth()->format('Y-m-d'));

            return Excel::download(
                new AttendanceExport($tanggalAwal, $tanggalAkhir),
                'rekapitulasi_kehadiran_' . Carbon::now()->format('d-m-Y') . '.xlsx'
            );
        } catch (\Exception $e) {
            \Log::error('Export Excel Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengexport data ke Excel');
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $tanggalAwal = Carbon::parse($request->tanggal_awal ?? now()->startOfMonth())
                ->setTimezone('Asia/Makassar')->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir ?? now()->endOfMonth())
                ->setTimezone('Asia/Makassar')->endOfDay();

            $attendances = Attendance::whereBetween('time', [$tanggalAwal, $tanggalAkhir])
                ->orderBy('time', 'desc')
                ->get();

            $pdf = PDF::loadView('admin.daftar_hadir.print', [
                'attendances' => $attendances,
                'tanggalAwal' => $tanggalAwal->format('d/m/Y'),
                'tanggalAkhir' => $tanggalAkhir->format('d/m/Y')
            ]);

            return $pdf->download('rekapitulasi_kehadiran_' . Carbon::now()->format('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Export PDF Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengexport data ke PDF');
        }
    }

    public function printView(Request $request)
    {
        try {
            $tanggalAwal = Carbon::parse($request->tanggal_awal ?? now()->startOfMonth())
                ->setTimezone('Asia/Makassar')->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir ?? now()->endOfMonth())
                ->setTimezone('Asia/Makassar')->endOfDay();

            $attendances = Attendance::whereBetween('time', [$tanggalAwal, $tanggalAkhir])
                ->orderBy('time', 'desc')
                ->get();

            // Tambahkan path logo
            $logoPath = public_path('logo/navlog1.png');
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;

            return view('admin.daftar_hadir.print', [
                'attendances' => $attendances,
                'tanggalAwal' => $tanggalAwal->format('d/m/Y'),
                'tanggalAkhir' => $tanggalAkhir->format('d/m/Y'),
                'logoSrc' => $logoSrc
            ]);
        } catch (\Exception $e) {
            \Log::error('Print View Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat halaman print');
        }
    }

    public function storeBackdate(Request $request)
    {
        try {
            $request->validate([
                'tanggal_absen' => 'required|date|before_or_equal:today',
                'waktu_absen' => 'required',
                'alasan' => 'required|string'
            ]);

            $currentUnit = session('unit', 'mysql');

            DB::beginTransaction();
            try {
                // Generate ID baru
                $lastId = DB::connection($currentUnit)
                           ->table('attendance')
                           ->max('id') ?? 0;
                $newId = $lastId + 1;

                // Gabungkan tanggal dan waktu
                $datetime = Carbon::parse($request->tanggal_absen . ' ' . $request->waktu_absen)
                                 ->setTimezone('Asia/Makassar');

                // Simpan attendance dengan ID manual
                DB::connection($currentUnit)
                  ->table('attendance')
                  ->insert([
                    'id' => $newId,
                    'name' => auth()->user()->name,
                    'position' => auth()->user()->position,
                    'division' => auth()->user()->division,
                    'time' => $datetime,
                    'is_backdate' => true,
                    'backdate_reason' => $request->alasan,
                    'unit_source' => $currentUnit,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::commit();

                return redirect()->back()->with('success', 'Absen mundur berhasil disimpan');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Backdate Store Error:', [
                'message' => $e->getMessage(),
                'session_unit' => session('unit', 'mysql'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan absen mundur')
                ->withInput();
        }
    }
} 