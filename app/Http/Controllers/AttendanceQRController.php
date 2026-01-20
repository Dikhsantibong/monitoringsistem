<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceToken;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AttendanceQRController extends Controller
{
    public function index()
    {
        // Ambil session unit yang aktif
        $connection = session('unit', 'mysql');
        
        // Ambil data absensi hari ini dari database sesuai session
        $attendances = DB::connection($connection)
            ->table('attendance')
            ->whereDate('time', Carbon::today())
            ->orderBy('time', 'desc')
            ->get();
            
        return view('admin.attendance.qr', compact('attendances'));
    }

        public function generate()
    {
        try {
            // Generate token
            $token = 'ATT-' . strtoupper(Str::random(8));
            
            // Gunakan koneksi sesuai session unit yang aktif
            $connection = session('unit', 'mysql');
            
            // Insert token ke database sesuai session
            DB::connection($connection)->table('attendance_tokens')->insert([
                'token' => $token,
                'user_id' => auth()->id(),
                'expires_at' => now()->addMinutes(15),
                'unit_source' => $connection, // Simpan session unit
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // URL untuk QR - **TAMBAHKAN UNIT_SOURCE KE URL**
            $externalUrl = config('services.attendance_external.url');
            if ($externalUrl) {
                $qrUrl = rtrim($externalUrl, '/') . '/scan/' . $token . '?unit=' . $connection;
            } else {
                $qrUrl = url("/attendance/scan/{$token}?unit={$connection}");
            }

            Log::info('QR Code generated', [
                'token' => $token,
                'url' => $qrUrl,
                'user_id' => auth()->id(),
                'unit_source' => $connection
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

    public function pullData(Request $request)
    {
        try {
            // Ambil session unit yang aktif
            $connection = session('unit', 'mysql');
            
            Log::info('=== START PULL DATA ===', [
                'unit_source' => $connection,
                'user_id' => auth()->id()
            ]);
            
            // Panggil API
            $response = Http::timeout(30)->get('https://absen-monday.online/api/attendance');
            
            if (!$response->successful()) {
                Log::error('API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API. Status: ' . $response->status()
                ], 500);
            }

            $responseData = $response->json();
            Log::info('API Response', ['response' => $responseData]);

            // Extract data array dari response
            $data = [];
            
            if (is_array($responseData)) {
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $data = $responseData['data'];
                    Log::info('Data extracted from response.data', ['count' => count($data)]);
                } else {
                    $data = $responseData;
                    Log::info('Using response as data array', ['count' => count($data)]);
                }
            }

            if (empty($data)) {
                Log::warning('No data to import');
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data untuk diimport',
                    'attendance_imported' => 0,
                    'token_imported' => 0
                ]);
            }

            $importedCount = 0;
            $tokenImportedCount = 0;
            $skippedCount = 0;

            DB::connection($connection)->beginTransaction();

            try {
                foreach ($data as $index => $item) {
                    try {
                        if (!is_array($item)) {
                            Log::warning("Item #{$index} is not array, skipping");
                            continue;
                        }

                        // Import Attendance jika ada field 'name'
                        if (isset($item['name']) && !empty($item['name'])) {
                            
                            $timeValue = isset($item['time']) && !empty($item['time'])
                                ? Carbon::parse($item['time'])->setTimezone('Asia/Makassar')
                                : now();

                            // Cek duplikat
                            $exists = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $item['name'])
                                ->where('time', $timeValue)
                                ->exists();

                            if (!$exists) {
                                // Insert data dengan unit_source dari session
                                DB::connection($connection)->table('attendance')->insert([
                                    'name' => $item['name'],
                                    'position' => $item['position'] ?? '',
                                    'division' => $item['division'] ?? '',
                                    'token' => $item['token'] ?? '',
                                    'time' => $timeValue,
                                    'signature' => $item['signature'] ?? null,
                                    'unit_source' => $connection, // Menggunakan session unit
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_reason' => $item['backdate_reason'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                
                                $importedCount++;
                                Log::info("✓ Attendance imported: {$item['name']} to {$connection}");
                            } else {
                                $skippedCount++;
                                Log::info("⊘ Attendance skipped (duplicate): {$item['name']}");
                            }
                        }
                        
                        // Import Token jika ada field 'token' tapi tidak ada 'name'
                        if (isset($item['token']) && !empty($item['token']) && !isset($item['name'])) {
                            
                            $exists = DB::connection($connection)
                                ->table('attendance_tokens')
                                ->where('token', $item['token'])
                                ->exists();

                            if (!$exists) {
                                DB::connection($connection)->table('attendance_tokens')->insert([
                                    'token' => $item['token'],
                                    'user_id' => $item['user_id'] ?? auth()->id(),
                                    'expires_at' => isset($item['expires_at']) && !empty($item['expires_at'])
                                        ? Carbon::parse($item['expires_at'])
                                        : now()->addMinutes(15),
                                    'unit_source' => $connection, // Menggunakan session unit
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_data' => $item['backdate_data'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                
                                $tokenImportedCount++;
                                Log::info("✓ Token imported: {$item['token']} to {$connection}");
                            } else {
                                $skippedCount++;
                                Log::info("⊘ Token skipped (duplicate): {$item['token']}");
                            }
                        }

                    } catch (\Exception $e) {
                        Log::error("Error importing item #{$index}", [
                            'error' => $e->getMessage(),
                            'item' => $item
                        ]);
                        continue;
                    }
                }

                DB::connection($connection)->commit();

                Log::info('=== PULL DATA COMPLETED ===', [
                    'unit_source' => $connection,
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'total_items' => count($data)
                ]);

                $message = "Data berhasil diimport ke {$connection}!\n";
                $message .= "• Attendance: {$importedCount}\n";
                $message .= "• Token: {$tokenImportedCount}\n";
                $message .= "• Dilewati (duplikat): {$skippedCount}";

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'total_processed' => count($data),
                    'unit_source' => $connection
                ]);

            } catch (\Exception $e) {
                DB::connection($connection)->rollBack();
                Log::error('Transaction rolled back', [
                    'unit_source' => $connection,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('=== PULL DATA FAILED ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}