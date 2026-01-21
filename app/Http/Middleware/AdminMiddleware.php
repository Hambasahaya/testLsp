<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user() || !auth()->user()->role || auth()->user()->role->name !== 'Admin') {
            abort(403, 'Unauthorized - Admin access required');
        }

        return $next($request);
    }
}
