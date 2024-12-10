<?php

namespace App\Http\Controllers;

use App\Models\DailyQrCode;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DailyQrCodeController extends Controller
{
    public function getDailyQrCode()
    {
        $today = Carbon::today();
        
        // Cari atau buat QR code untuk hari ini
        $qrCode = DailyQrCode::firstOrCreate(
            ['valid_date' => $today],
            [
                'code' => Str::random(32),
                'is_active' => true
            ]
        );

        return response()->json(['code' => $qrCode->code]);
    }

    public function scanQrCode(Request $request)
    {
        $code = $request->code;
        $user = auth()->user();

        $qrCode = DailyQrCode::where('code', $code)
            ->where('valid_date', Carbon::today())
            ->where('is_active', true)
            ->first();

        if (!$qrCode) {
            return response()->json(['error' => 'QR Code tidak valid'], 400);
        }

        // Cek apakah user sudah absen hari ini
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('daily_qr_code_id', $qrCode->id)
            ->whereDate('attended_at', Carbon::today())
            ->first();

        if ($existingAttendance) {
            return response()->json(['error' => 'Anda sudah melakukan absensi hari ini'], 400);
        }

        // Catat kehadiran
        Attendance::create([
            'user_id' => $user->id,
            'daily_qr_code_id' => $qrCode->id,
            'attended_at' => Carbon::now()
        ]);

        return response()->json(['message' => 'Absensi berhasil']);
    }
} 