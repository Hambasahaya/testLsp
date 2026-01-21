<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Try web session first
        if (auth()->check()) {
            return $next($request);
        }

        // Try Sanctum token
        if (auth('sanctum')->check()) {
            auth()->setUser(auth('sanctum')->user());
            return $next($request);
        }

        // If not authenticated, return 401
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
