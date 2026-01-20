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

    public function pullData(Request $request)
    {
        try {
            Log::info('=== START PULL DATA ===');
            
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
            // API bisa return format: {"success": true, "data": [...]} atau langsung [...]
            $data = [];
            
            if (is_array($responseData)) {
                // Cek apakah ada key 'data'
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $data = $responseData['data'];
                    Log::info('Data extracted from response.data', ['count' => count($data)]);
                } else {
                    // Response langsung array
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

            $connection = session('unit', 'mysql');
            $importedCount = 0;
            $tokenImportedCount = 0;
            $skippedCount = 0;

            DB::beginTransaction();

            try {
                foreach ($data as $index => $item) {
                    try {
                        if (!is_array($item)) {
                            Log::warning("Item #{$index} is not array, skipping");
                            continue;
                        }

                        // Import Attendance jika ada field 'name'
                        if (isset($item['name']) && !empty($item['name'])) {
                            
                            // Cek duplikat berdasarkan name dan time
                            $timeValue = isset($item['time']) && !empty($item['time'])
                                ? Carbon::parse($item['time'])->setTimezone('Asia/Makassar')
                                : now();

                            $exists = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $item['name'])
                                ->where('time', $timeValue)
                                ->exists();

                            if (!$exists) {
                                // Insert data
                                DB::connection($connection)->table('attendance')->insert([
                                    'name' => $item['name'],
                                    'position' => $item['position'] ?? '',
                                    'division' => $item['division'] ?? '',
                                    'token' => $item['token'] ?? '',
                                    'time' => $timeValue,
                                    'signature' => $item['signature'] ?? null,
                                    'unit_source' => $connection,
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_reason' => $item['backdate_reason'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                
                                $importedCount++;
                                Log::info("✓ Attendance imported: {$item['name']}");
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
                                    'unit_source' => $connection,
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_data' => $item['backdate_data'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                
                                $tokenImportedCount++;
                                Log::info("✓ Token imported: {$item['token']}");
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
                        // Continue dengan item berikutnya
                        continue;
                    }
                }

                DB::commit();

                Log::info('=== PULL DATA COMPLETED ===', [
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'total_items' => count($data)
                ]);

                $message = "Data berhasil diimport!\n";
                $message .= "• Attendance: {$importedCount}\n";
                $message .= "• Token: {$tokenImportedCount}\n";
                $message .= "• Dilewati (duplikat): {$skippedCount}";

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'total_processed' => count($data)
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction rolled back', [
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