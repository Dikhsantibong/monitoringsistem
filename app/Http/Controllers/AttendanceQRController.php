<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceQRController extends Controller
{
    public function generate()
    {
        $token = 'ATT-'.Str::random(8);

        DB::connection('internet')
            ->table('attendance_tokens')
            ->insert([
              'token'=>$token,
              'expires_at'=>now()->addMinutes(10)
            ]);

        return view('admin.attendance.qr', compact('token'));
    }
}
