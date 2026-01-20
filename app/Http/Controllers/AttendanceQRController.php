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
            Log::info('API URL: https://absen-monday.online/api/attendance');
            
            // Panggil API
            $response = Http::timeout(30)->get('https://absen-monday.online/api/attendance');
            
            Log::info('API Response Status: ' . $response->status());
            
            if (!$response->successful()) {
                $errorMsg = 'API request failed with status: ' . $response->status();
                Log::error($errorMsg, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari API. Status: ' . $response->status(),
                    'details' => [
                        'status_code' => $response->status(),
                        'response_body' => $response->body()
                    ]
                ], 500);
            }

            // Get raw response body
            $rawBody = $response->body();
            Log::info('Raw API Response Body', ['body' => $rawBody]);

            // Parse JSON
            try {
                $data = $response->json();
            } catch (\Exception $e) {
                Log::error('JSON Parse Error', [
                    'error' => $e->getMessage(),
                    'raw_body' => $rawBody
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal parsing JSON dari API',
                    'details' => [
                        'error' => $e->getMessage(),
                        'raw_response' => substr($rawBody, 0, 500) // First 500 chars
                    ]
                ], 500);
            }

            // Log data structure
            Log::info('Parsed JSON Data', [
                'data_type' => gettype($data),
                'is_array' => is_array($data),
                'count' => is_array($data) ? count($data) : 'N/A',
                'data_structure' => json_encode($data, JSON_PRETTY_PRINT)
            ]);

            // Validate data type
            if (!is_array($data)) {
                Log::error('Invalid data type from API', [
                    'expected' => 'array',
                    'received' => gettype($data),
                    'data' => $data
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Format data dari API tidak valid. Expected: array, Got: ' . gettype($data),
                    'details' => [
                        'data_type' => gettype($data),
                        'data_preview' => is_string($data) ? substr($data, 0, 200) : json_encode($data)
                    ]
                ], 500);
            }

            // Check if empty
            if (empty($data)) {
                Log::warning('API returned empty array');
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data baru untuk diimport dari API',
                    'attendance_imported' => 0,
                    'token_imported' => 0,
                    'details' => [
                        'api_response' => 'Empty array'
                    ]
                ]);
            }

            // Log first item structure
            if (isset($data[0])) {
                Log::info('First item structure', [
                    'keys' => array_keys($data[0]),
                    'sample' => $data[0]
                ]);
            }

            $connection = session('unit', 'mysql');
            $importedCount = 0;
            $tokenImportedCount = 0;
            $skippedCount = 0;
            $errors = [];

            Log::info('Using database connection: ' . $connection);

            DB::beginTransaction();

            try {
                foreach ($data as $index => $item) {
                    try {
                        Log::info("=== Processing item #{$index} ===", ['item' => $item]);

                        // Validate item is array
                        if (!is_array($item)) {
                            $errors[] = "Item #{$index} is not an array: " . gettype($item);
                            Log::warning("Item #{$index} is not an array", ['type' => gettype($item)]);
                            continue;
                        }

                        // Check if this is attendance data (has name field)
                        if (isset($item['name']) && !empty($item['name'])) {
                            Log::info("Item #{$index} detected as ATTENDANCE data");
                            
                            // Prepare attendance data
                            $attendanceData = [
                                'name' => $item['name'],
                                'position' => $item['position'] ?? null,
                                'division' => $item['division'] ?? null,
                                'token' => $item['token'] ?? null,
                                'time' => isset($item['time']) && !empty($item['time'])
                                    ? Carbon::parse($item['time'])->setTimezone('Asia/Makassar')
                                    : now(),
                                'signature' => $item['signature'] ?? null,
                                'unit_source' => $connection,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];

                            Log::info("Prepared attendance data", $attendanceData);

                            // Check for existing record
                            $existingCheck = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $attendanceData['name'])
                                ->where('time', $attendanceData['time'])
                                ->exists();

                            if ($existingCheck) {
                                Log::info("Attendance already exists, skipping", [
                                    'name' => $attendanceData['name'],
                                    'time' => $attendanceData['time']
                                ]);
                                $skippedCount++;
                                continue;
                            }

                            // Insert attendance
                            DB::connection($connection)->table('attendance')->insert($attendanceData);
                            $importedCount++;
                            Log::info("✓ Attendance inserted successfully", ['total' => $importedCount]);
                        }
                        // Check if this is token data (has token field but no name)
                        elseif (isset($item['token']) && !empty($item['token']) && !isset($item['name'])) {
                            Log::info("Item #{$index} detected as TOKEN data");
                            
                            // Prepare token data
                            $tokenData = [
                                'token' => $item['token'],
                                'user_id' => $item['user_id'] ?? auth()->id(),
                                'expires_at' => isset($item['expires_at']) && !empty($item['expires_at'])
                                    ? Carbon::parse($item['expires_at'])
                                    : now()->addMinutes(15),
                                'unit_source' => $connection,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];

                            Log::info("Prepared token data", $tokenData);

                            // Check for existing token
                            $existingToken = DB::connection($connection)
                                ->table('attendance_tokens')
                                ->where('token', $tokenData['token'])
                                ->exists();

                            if ($existingToken) {
                                Log::info("Token already exists, skipping", ['token' => $tokenData['token']]);
                                $skippedCount++;
                                continue;
                            }

                            // Insert token
                            DB::connection($connection)->table('attendance_tokens')->insert($tokenData);
                            $tokenImportedCount++;
                            Log::info("✓ Token inserted successfully", ['total' => $tokenImportedCount]);
                        }
                        else {
                            $warning = "Item #{$index} has no 'name' or 'token' field, skipping";
                            Log::warning($warning, ['item' => $item]);
                            $errors[] = $warning;
                            $skippedCount++;
                        }

                    } catch (\Exception $e) {
                        $errorMsg = "Error processing item #{$index}: " . $e->getMessage();
                        $errors[] = $errorMsg;
                        Log::error('Error processing item', [
                            'index' => $index,
                            'item' => $item,
                            'error' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }

                DB::commit();

                Log::info('=== PULL DATA COMPLETED ===', [
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'errors_count' => count($errors),
                    'total_items' => count($data)
                ]);

                $message = "Data berhasil diproses!";
                $message .= "\n• Attendance baru: {$importedCount}";
                $message .= "\n• Token baru: {$tokenImportedCount}";
                $message .= "\n• Dilewati (duplikat): {$skippedCount}";
                if (count($errors) > 0) {
                    $message .= "\n• Error: " . count($errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'errors' => $errors,
                    'total_processed' => count($data)
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Transaction failed and rolled back', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('=== PULL DATA FAILED ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'details' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}