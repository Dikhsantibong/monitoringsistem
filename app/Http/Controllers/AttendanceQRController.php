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
            Log::info('Starting to pull attendance data from API');
            
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

            $data = $response->json();
            
            Log::info('API Response received', [
                'data_type' => gettype($data),
                'data_count' => is_array($data) ? count($data) : 0,
                'sample_data' => is_array($data) && count($data) > 0 ? $data[0] : null
            ]);

            if (!is_array($data)) {
                Log::error('Invalid API response format', ['data' => $data]);
                return response()->json([
                    'success' => false,
                    'message' => 'Format data dari API tidak valid'
                ], 500);
            }

            if (empty($data)) {
                Log::warning('API returned empty array');
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data baru untuk diimport',
                    'attendance_imported' => 0,
                    'token_imported' => 0
                ]);
            }

            $connection = session('unit', 'mysql');
            $importedCount = 0;
            $tokenImportedCount = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($data as $index => $item) {
                    try {
                        Log::info("Processing item {$index}", ['item' => $item]);

                        // Deteksi struktur data
                        $isAttendanceData = isset($item['name']) || isset($item['position']) || isset($item['division']);
                        $isTokenData = isset($item['token']) && !$isAttendanceData;

                        // Handle Attendance data
                        if ($isAttendanceData) {
                            // Cek apakah data sudah ada
                            $existingAttendance = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $item['name'] ?? '')
                                ->where('time', $item['time'] ?? null)
                                ->first();

                            if (!$existingAttendance) {
                                $insertData = [
                                    'name' => $item['name'] ?? null,
                                    'position' => $item['position'] ?? null,
                                    'division' => $item['division'] ?? null,
                                    'token' => $item['token'] ?? null,
                                    'time' => isset($item['time']) 
                                        ? Carbon::parse($item['time'])->setTimezone('Asia/Makassar')
                                        : now(),
                                    'signature' => $item['signature'] ?? null,
                                    'unit_source' => $connection,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];

                                Log::info("Inserting attendance data", ['data' => $insertData]);

                                DB::connection($connection)->table('attendance')->insert($insertData);
                                $importedCount++;
                                
                                Log::info("Attendance inserted successfully", ['count' => $importedCount]);
                            } else {
                                Log::info("Attendance already exists, skipping", ['name' => $item['name']]);
                            }
                        }

                        // Handle Token data
                        if ($isTokenData) {
                            // Cek apakah token sudah ada
                            $existingToken = DB::connection($connection)
                                ->table('attendance_tokens')
                                ->where('token', $item['token'])
                                ->first();

                            if (!$existingToken) {
                                $tokenInsertData = [
                                    'token' => $item['token'],
                                    'user_id' => $item['user_id'] ?? auth()->id(),
                                    'expires_at' => isset($item['expires_at']) 
                                        ? Carbon::parse($item['expires_at'])
                                        : now()->addMinutes(15),
                                    'unit_source' => $connection,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];

                                Log::info("Inserting token data", ['data' => $tokenInsertData]);

                                DB::connection($connection)->table('attendance_tokens')->insert($tokenInsertData);
                                $tokenImportedCount++;
                                
                                Log::info("Token inserted successfully", ['count' => $tokenImportedCount]);
                            } else {
                                Log::info("Token already exists, skipping", ['token' => $item['token']]);
                            }
                        }

                    } catch (\Exception $e) {
                        $errorMsg = "Error processing item {$index}: " . $e->getMessage();
                        $errors[] = $errorMsg;
                        Log::warning('Error processing item', [
                            'index' => $index,
                            'item' => $item,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }

                DB::commit();

                Log::info('Attendance data pulled successfully', [
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'errors' => count($errors),
                    'error_details' => $errors
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Data berhasil diimport. Attendance: {$importedCount}, Token: {$tokenImportedCount}",
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'errors' => $errors
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
            Log::error('Error pulling attendance data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}