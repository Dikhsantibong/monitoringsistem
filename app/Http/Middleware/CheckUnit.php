<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckUnit
{
    public function handle(Request $request, Closure $next)
    {
        // Jika ada parameter unit di URL
        if ($request->has('unit')) {
            $requestedUnit = $request->unit;
            
            // Update session unit
            session(['unit' => $requestedUnit]);
            
            // Jika tidak terautentikasi, coba login dengan session sebelumnya
            if (!Auth::check()) {
                // Ambil credentials dari session jika ada
                $savedCredentials = session('auth_credentials');
                if ($savedCredentials) {
                    Auth::attempt([
                        'email' => $savedCredentials['email'],
                        'password' => $savedCredentials['password']
                    ]);
                }
            }
            
            Log::debug('Unit and auth check', [
                'unit' => $requestedUnit,
                'is_authenticated' => Auth::check()
            ]);
        }

        if (!session()->has('unit')) {
            Log::warning('No unit in session, redirecting to login');
            return redirect()->route('login')
                           ->with('error', 'Silakan pilih unit terlebih dahulu');
        }

        return $next($request);
    }
} 