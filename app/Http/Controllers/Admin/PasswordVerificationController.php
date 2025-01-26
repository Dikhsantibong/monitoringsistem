<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password tidak valid'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Password valid'
            ]);
        } catch (\Exception $e) {
            \Log::error('Password verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi'
            ], 500);
        }
    }
}