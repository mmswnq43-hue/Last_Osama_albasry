<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->user_role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'غير مصرح'], 403);
            }
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
