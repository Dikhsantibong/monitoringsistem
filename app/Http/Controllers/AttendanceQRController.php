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
            
            if (!is_array($data)) {
                Log::error('Invalid API response format', ['data' => $data]);
                return response()->json([
                    'success' => false,
                    'message' => 'Format data dari API tidak valid'
                ], 500);
            }

            $connection = session('unit', 'mysql');
            $importedCount = 0;
            $tokenImportedCount = 0;
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($data as $item) {
                    try {
                        // Handle Attendance data
                        if (isset($item['name']) || isset($item['attendance'])) {
                            $attendanceData = $item['attendance'] ?? $item;
                            
                            // Cek apakah data sudah ada (berdasarkan kombinasi name, time, token)
                            $existingAttendance = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $attendanceData['name'] ?? null)
                                ->where('time', $attendanceData['time'] ?? null)
                                ->where('token', $attendanceData['token'] ?? null)
                                ->first();

                            if (!$existingAttendance) {
                                DB::connection($connection)->table('attendance')->insert([
                                    'name' => $attendanceData['name'] ?? null,
                                    'position' => $attendanceData['position'] ?? null,
                                    'division' => $attendanceData['division'] ?? null,
                                    'token' => $attendanceData['token'] ?? null,
                                    'time' => isset($attendanceData['time']) 
                                        ? Carbon::parse($attendanceData['time'])->setTimezone('Asia/Makassar')
                                        : now(),
                                    'signature' => $attendanceData['signature'] ?? null,
                                    'unit_source' => $connection,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                $importedCount++;
                            }
                        }

                        // Handle AttendanceToken data
                        if (isset($item['token']) || isset($item['attendance_token'])) {
                            $tokenData = $item['attendance_token'] ?? $item;
                            
                            // Cek apakah token sudah ada
                            $existingToken = DB::connection($connection)
                                ->table('attendance_tokens')
                                ->where('token', $tokenData['token'] ?? null)
                                ->first();

                            if (!$existingToken && isset($tokenData['token'])) {
                                DB::connection($connection)->table('attendance_tokens')->insert([
                                    'token' => $tokenData['token'],
                                    'user_id' => $tokenData['user_id'] ?? auth()->id(),
                                    'expires_at' => isset($tokenData['expires_at']) 
                                        ? Carbon::parse($tokenData['expires_at'])
                                        : now()->addMinutes(15),
                                    'unit_source' => $connection,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                                $tokenImportedCount++;
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = 'Error processing item: ' . $e->getMessage();
                        Log::warning('Error processing attendance item', [
                            'item' => $item,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                DB::commit();

                Log::info('Attendance data pulled successfully', [
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'errors' => count($errors)
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