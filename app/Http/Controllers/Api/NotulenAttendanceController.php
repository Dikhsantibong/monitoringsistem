<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotulenAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class NotulenAttendanceController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'position' => 'required|string',
                'signature' => 'required|string',
                'temp_notulen_id' => 'required|string'
            ]);

            // Store attendance in cache temporarily
            $attendances = Cache::get("notulen_attendances_{$validated['temp_notulen_id']}", []);
            $attendances[] = [
                'name' => $validated['name'],
                'position' => $validated['position'],
                'signature' => $validated['signature']
            ];
            Cache::put("notulen_attendances_{$validated['temp_notulen_id']}", $attendances, now()->addHours(2));

            // Store in session for later use when saving notulen
            Session::put("temp_attendances_{$validated['temp_notulen_id']}", $attendances);

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil disimpan',
                'attendance' => end($attendances),
                'redirect_url' => route('homepage')
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
}
