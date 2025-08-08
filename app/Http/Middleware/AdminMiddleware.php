<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin' || auth()->user()->role === 'pemeliharaan')) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'Unauthorized access');
    }
}
