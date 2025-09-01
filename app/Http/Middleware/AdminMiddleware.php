<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return ApiResponse::response(null, 'Authentication required', 401, false);
        }

        if (!auth()->user()->isAdmin()) {
            return ApiResponse::response(null, 'Admin access required', 403, false);
        }

        return $next($request);
    }
}
