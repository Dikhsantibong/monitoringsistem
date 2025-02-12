<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckUnit
{
    public function handle(Request $request, Closure $next)
    {
        // Jika ada parameter unit di URL, update session
        if ($request->has('unit')) {
            session(['unit' => $request->unit]);
            Log::debug('Unit updated from URL', ['unit' => $request->unit]);
        }

        // Pastikan unit ada di session
        if (!session()->has('unit')) {
            Log::warning('No unit in session, redirecting to login');
            return redirect()->route('login')
                           ->with('error', 'Silakan pilih unit terlebih dahulu');
        }

        return $next($request);
    }
} 