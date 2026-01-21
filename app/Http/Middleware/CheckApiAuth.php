<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiAuth
{
    public function handle(Request $request, Closure $next)
    {

        if (auth()->check()) {
            return $next($request);
        }


        if (auth('sanctum')->check()) {
            auth()->setUser(auth('sanctum')->user());
            return $next($request);
        }


        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
