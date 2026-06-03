<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->user_role !== 'station_owner') {
            if (auth()->user()?->user_role === 'admin') {
                return $next($request); // admin can access owner pages
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}
