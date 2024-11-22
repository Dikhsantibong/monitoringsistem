<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Lanjutkan ke halaman admin
        }
        return redirect('/home')->with('error', 'Unauthorized Access'); // Redirect ke halaman user
    }
}
