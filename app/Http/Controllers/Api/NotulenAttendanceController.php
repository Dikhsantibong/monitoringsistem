<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotulenAttendance;
use App\Models\Notulen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NotulenAttendanceController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'position' => 'required|string',
                'signature' => 'required|string',
                'temp_notulen_id' => 'required|string',
                'division' => 'required|string'
            ]);

            // Generate a session ID for this attendance
            $sessionId = Str::uuid()->toString();

            // Save attendance directly to database
            $attendance = NotulenAttendance::create([
                'session_id' => $sessionId,
                'name' => $validated['name'],
                'position' => $validated['position'],
                'division' => $validated['division'],
                'signature' => $validated['signature']
            ]);

            // Also store in cache for the notulen form
            $attendances = Cache::get("notulen_attendances_{$validated['temp_notulen_id']}", []);
            $attendances[] = [
                'id' => $attendance->id,
                'session_id' => $sessionId,
                'name' => $validated['name'],
                'position' => $validated['position'],
                'division' => $validated['division'],
                'signature' => $validated['signature']
            ];
            Cache::put("notulen_attendances_{$validated['temp_notulen_id']}", $attendances, now()->addHours(2));

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan',
                'attendance' => end($attendances)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showAttendanceForm($tempNotulenId)
    {
        return view('notulen.attendance-form', [
            'temp_notulen_id' => $tempNotulenId
        ]);
    }

    /**
     * Handle late attendance via QR code scan
     */
    public function storeLateAttendance(Request $request, Notulen $notulen)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'position' => 'required|string',
                'division' => 'required|string',
                'signature' => 'required|string'
            ]);

            // Create attendance record with late status
            $attendance = NotulenAttendance::create([
                'notulen_id' => $notulen->id,
                'session_id' => Str::uuid()->toString(),
                'name' => $validated['name'],
                'position' => $validated['position'],
                'division' => $validated['division'],
                'signature' => $validated['signature'],
                'is_late' => true,
                'attended_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi terlambat berhasil dicatat',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
