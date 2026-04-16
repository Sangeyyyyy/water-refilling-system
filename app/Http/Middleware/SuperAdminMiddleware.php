<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for elevated roles
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'director', 'manager'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized action. High-level access required.');
    }
}
