<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || !$request->user()->role) {
            return response()->json([
                'message' => 'Unauthorized - No role assigned',
            ], 403);
        }

        if ($request->user()->role->name !== $role) {
            return response()->json([
                'message' => 'Unauthorized - Insufficient permissions',
            ], 403);
        }

        return $next($request);
    }
}
