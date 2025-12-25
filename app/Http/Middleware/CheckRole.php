<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return ResponseHelper::unauthorized('Unauthenticated');
        }

        if (!$request->user()->hasAnyRole($roles)) {
            return ResponseHelper::error(
                'Forbidden: You do not have permission to access this resource',
                null,
                403
            );
        }

        return $next($request);
    }
}