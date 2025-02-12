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
            $query = Attendance::query();

            // Filter berdasarkan tanggal
            $date = $request->date ?? Carbon::today();
            $query->whereDate('time', $date);

            // Ambil data
            $attendances = $query->orderBy('time', 'desc')->get();

            return view('admin.daftar_hadir.index', compact('attendances'));
            
        } catch (\Exception $e) {
            Log::error('Error in attendance index: ' . $e->getMessage());
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
            // Generate token sederhana
            $token = 'ATT-' . strtoupper(Str::random(8));
            
            // Simpan token
            DB::table('attendance_tokens')->insert([
                'token' => $token,
                'user_id' => auth()->id(),
                'expires_at' => now()->addHours(24),
                'unit_source' => session('unit', 'poasia'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // URL untuk QR
            $qrUrl = url("/attendance/scan/{$token}");

            return response()->json([
                'success' => true,
                'qr_url' => $qrUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat QR Code'
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

            DB::beginTransaction();
            
            // Ambil koneksi dari session
            $currentUnit = session('unit', 'mysql');
            
            Log::debug('Storing Attendance', [
                'unit' => $currentUnit,
                'name' => $validated['name']
            ]);

            try {
                // Simpan attendance menggunakan model
                $attendance = new Attendance([
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                    'division' => $validated['division'],
                    'token' => $validated['token'],
                    'signature' => $validated['signature'],
                    'time' => now(),
                    'unit_source' => $currentUnit
                ]);

                $attendance->save();

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
                'unit' => session('unit', 'mysql'),
                'trace' => $e->getTraceAsString()
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

            DB::beginTransaction();
            try {
                // Generate ID baru
                $lastId = DB::connection(session('unit'))
                           ->table('attendance')
                           ->max('id') ?? 0;
                $newId = $lastId + 1;

                // Gabungkan tanggal dan waktu
                $datetime = Carbon::parse($request->tanggal_absen . ' ' . $request->waktu_absen)
                                 ->setTimezone('Asia/Makassar');

                // Simpan attendance dengan ID manual
                DB::connection(session('unit'))
                  ->table('attendance')
                  ->insert([
                    'id' => $newId,
                    'name' => auth()->user()->name,
                    'position' => auth()->user()->position,
                    'division' => auth()->user()->division,
                    'time' => $datetime,
                    'is_backdate' => true,
                    'backdate_reason' => $request->alasan,
                    'unit_source' => session('unit', 'poasia'),
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
            \Log::error('Backdate Store Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan absen mundur')
                ->withInput();
        }
    }
} 