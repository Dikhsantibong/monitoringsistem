<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InventoryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'inventory') {
            return redirect('/')->with('error', 'Unauthorized access');
        }
        return $next($request);
    }
}
