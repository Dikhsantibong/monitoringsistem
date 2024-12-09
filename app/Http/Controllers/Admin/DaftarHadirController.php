<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceToken;
use Carbon\Carbon;

class DaftarHadirController extends Controller
{
    public function index()
    {
        $attendances = Attendance::orderBy('created_at', 'desc')->get();
        return view('admin.daftar_hadir.index', compact('attendances'));
    }

    public function storeToken(Request $request)
    {
        try {
            \Log::info('Received token request:', $request->all());
            
            $token = AttendanceToken::create([
                'token' => $request->token,
                'expires_at' => now()->addDay()
            ]);

            \Log::info('Token stored successfully:', ['token' => $token]);
            
            return response()->json([
                'success' => true,
                'message' => 'Token berhasil disimpan',
                'data' => $token
            ]);
        } catch (\Exception $e) {
            \Log::error('Error storing token: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan token: ' . $e->getMessage()
            ], 500);
        }
    }
}
