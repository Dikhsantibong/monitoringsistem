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
            // Ambil session unit yang aktif - HARUS EXACT MATCH
            $connection = session('unit', 'mysql');
            
            // PENTING: Mapping unit session ke unit_source yang benar
            // Sesuaikan dengan data di database Anda
            $unitSourceMap = [
                'mysql' => 'mysql_wua_wua',           // Contoh: session mysql -> unit_source mysql_wua_wua
                'mysql2' => 'mysql_bau_bau',          // Contoh: session mysql2 -> unit_source mysql_bau_bau
                'pgsql' => 'pgsql_kendari',           // dst...
                // Tambahkan mapping lainnya sesuai kebutuhan
            ];
            
            // Dapatkan unit_source yang sesuai
            $unitSource = $unitSourceMap[$connection] ?? $connection;
            
            Log::info('=== START PULL DATA ===', [
                'session_connection' => $connection,
                'unit_source' => $unitSource,
                'user_id' => auth()->id()
            ]);
            
            // Panggil API dengan filter unit_source
            $response = Http::timeout(90)
                ->connectTimeout(30)
                ->retry(3, 200)
                ->withOptions([
                    'verify' => false,
                    'http_errors' => false,
                ])
                ->withHeaders([
                    'Accept' => 'application/json'
                ])
                ->get('https://absen-monday.online/api/attendance', [
                    'unit_source' => $unitSource // Filter di API
                ]);
            
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
            Log::info('API Response received', [
                'has_success' => isset($responseData['success']),
                'has_data' => isset($responseData['data'])
            ]);
    
            // Extract data array dari response
            $data = [];
            
            if (isset($responseData['success']) && $responseData['success'] === true) {
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $data = $responseData['data'];
                }
            } elseif (is_array($responseData)) {
                if (isset($responseData['data']) && is_array($responseData['data'])) {
                    $data = $responseData['data'];
                } elseif (isset($responseData[0])) {
                    $data = $responseData;
                }
            }
    
            Log::info('Data extracted', ['count' => count($data)]);
    
            if (empty($data)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada data untuk diimport',
                    'attendance_imported' => 0,
                    'token_imported' => 0,
                    'unit_source' => $unitSource
                ]);
            }
    
            // **FILTER DATA - EXACT MATCH UNIT_SOURCE & HARI INI**
            $today = Carbon::today()->toDateString();
            
            $filteredData = array_filter($data, function($item) use ($unitSource, $today) {
                if (!is_array($item)) {
                    return false;
                }
                
                // EXACT MATCH - unit_source harus sama persis
                $unitMatch = isset($item['unit_source']) && 
                            $item['unit_source'] === $unitSource;
                
                // Cek apakah data adalah data hari ini
                $isToday = true;
                if (isset($item['time']) && !empty($item['time'])) {
                    try {
                        $itemDate = Carbon::parse($item['time'])->toDateString();
                        $isToday = $itemDate === $today;
                    } catch (\Exception $e) {
                        Log::warning('Invalid date format', ['time' => $item['time']]);
                        $isToday = false;
                    }
                }
                
                return $unitMatch && $isToday;
            });
    
            Log::info('Data filtered', [
                'unit_source' => $unitSource,
                'date' => $today,
                'total_from_api' => count($data),
                'filtered_count' => count($filteredData)
            ]);
    
            if (empty($filteredData)) {
                // Debug: tampilkan unit_source yang ada di data
                $availableUnits = array_values(array_unique(array_filter(array_column($data, 'unit_source'))));
                
                return response()->json([
                    'success' => true,
                    'message' => "Tidak ada data untuk unit {$unitSource} pada hari ini",
                    'attendance_imported' => 0,
                    'token_imported' => 0,
                    'debug_info' => [
                        'your_session' => $connection,
                        'your_unit_source' => $unitSource,
                        'total_data_from_api' => count($data),
                        'available_units_in_api' => $availableUnits,
                        'suggestion' => 'Pastikan mapping unit_source sudah benar di controller'
                    ]
                ]);
            }
    
            $importedCount = 0;
            $tokenImportedCount = 0;
            $skippedCount = 0;
    
            DB::connection($connection)->beginTransaction();
    
            try {
                // Batch insert untuk performa
                $attendanceRecords = [];
                $tokenRecords = [];
                
                foreach ($filteredData as $index => $item) {
                    try {
                        if (!is_array($item)) {
                            continue;
                        }
    
                        // Import Attendance
                        if (isset($item['name']) && !empty($item['name'])) {
                            
                            $timeValue = isset($item['time']) && !empty($item['time'])
                                ? Carbon::parse($item['time'])->setTimezone('Asia/Makassar')
                                : now();
    
                            // Cek duplikat - EXACT MATCH unit_source
                            $exists = DB::connection($connection)
                                ->table('attendance')
                                ->where('name', $item['name'])
                                ->where('time', $timeValue)
                                ->where('unit_source', $unitSource) // EXACT MATCH
                                ->exists();
    
                            if (!$exists) {
                                $attendanceRecords[] = [
                                    'name' => $item['name'],
                                    'position' => $item['position'] ?? '',
                                    'division' => $item['division'] ?? '',
                                    'token' => $item['token'] ?? '',
                                    'time' => $timeValue,
                                    'signature' => $item['signature'] ?? null,
                                    'unit_source' => $unitSource, // Simpan unit_source yang konsisten
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_reason' => $item['backdate_reason'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                
                                $importedCount++;
                            } else {
                                $skippedCount++;
                            }
                            
                            // Batch insert per 100 record
                            if (count($attendanceRecords) >= 100) {
                                DB::connection($connection)->table('attendance')->insert($attendanceRecords);
                                Log::info("✓ Batch inserted " . count($attendanceRecords) . " attendance records");
                                $attendanceRecords = [];
                            }
                        }
                        
                        // Import Token
                        if (isset($item['token']) && !empty($item['token']) && !isset($item['name'])) {
                            
                            $exists = DB::connection($connection)
                                ->table('attendance_tokens')
                                ->where('token', $item['token'])
                                ->where('unit_source', $unitSource) // EXACT MATCH
                                ->exists();
    
                            if (!$exists) {
                                $tokenRecords[] = [
                                    'token' => $item['token'],
                                    'user_id' => $item['user_id'] ?? auth()->id(),
                                    'expires_at' => isset($item['expires_at']) && !empty($item['expires_at'])
                                        ? Carbon::parse($item['expires_at'])
                                        : now()->addMinutes(15),
                                    'unit_source' => $unitSource,
                                    'is_backdate' => $item['is_backdate'] ?? 0,
                                    'backdate_data' => $item['backdate_data'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ];
                                
                                $tokenImportedCount++;
                            } else {
                                $skippedCount++;
                            }
                            
                            // Batch insert per 100 record
                            if (count($tokenRecords) >= 100) {
                                DB::connection($connection)->table('attendance_tokens')->insert($tokenRecords);
                                Log::info("✓ Batch inserted " . count($tokenRecords) . " token records");
                                $tokenRecords = [];
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
    
                // Insert sisa records
                if (!empty($attendanceRecords)) {
                    DB::connection($connection)->table('attendance')->insert($attendanceRecords);
                    Log::info("✓ Final batch inserted " . count($attendanceRecords) . " attendance records");
                }
                
                if (!empty($tokenRecords)) {
                    DB::connection($connection)->table('attendance_tokens')->insert($tokenRecords);
                    Log::info("✓ Final batch inserted " . count($tokenRecords) . " token records");
                }
    
                DB::connection($connection)->commit();
    
                Log::info('=== PULL DATA COMPLETED ===', [
                    'session' => $connection,
                    'unit_source' => $unitSource,
                    'date' => $today,
                    'imported' => $importedCount,
                    'tokens' => $tokenImportedCount,
                    'skipped' => $skippedCount
                ]);
    
                $message = "Data berhasil diimport ke {$unitSource} untuk hari ini!\n";
                $message .= "• Attendance: {$importedCount}\n";
                $message .= "• Token: {$tokenImportedCount}\n";
                $message .= "• Dilewati (duplikat): {$skippedCount}";
    
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'attendance_imported' => $importedCount,
                    'token_imported' => $tokenImportedCount,
                    'skipped' => $skippedCount,
                    'total_processed' => count($filteredData),
                    'unit_source' => $unitSource,
                    'date' => $today
                ]);
    
            } catch (\Exception $e) {
                DB::connection($connection)->rollBack();
                Log::error('Transaction rolled back', [
                    'error' => $e->getMessage()
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