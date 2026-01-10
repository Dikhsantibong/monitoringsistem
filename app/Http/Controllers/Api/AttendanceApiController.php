<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceToken;

class AttendanceApiController extends Controller
{
    public function submit(Request $request)
    {
        if ($request->header('X-API-KEY') !== env('PUBLIC_API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = AttendanceToken::where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return response()->json(['error' => 'Token invalid'], 403);
        }

        Attendance::create([
            'name'        => $request->name,
            'division'    => $request->division,
            'position'    => $request->position,
            'signature'   => $request->signature,
            'token'       => $request->token,
            'unit_source' => $token->unit_source,
            'time'        => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
