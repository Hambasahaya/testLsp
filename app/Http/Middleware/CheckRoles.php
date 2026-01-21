<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !$request->user()->role) {
            return response()->json([
                'message' => 'Unauthorized - No role assigned',
            ], 403);
        }

        if (!in_array($request->user()->role->name, $roles)) {
            return response()->json([
                'message' => 'Unauthorized - Insufficient permissions',
            ], 403);
        }

        return $next($request);
    }
}
