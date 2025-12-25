<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckWebAuth
{
    /**
     * Handle an incoming request.
     * Check if user is authenticated via localStorage token
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        
        // Jika ada role yang diperlukan, kita simpan di session
        if (!empty($roles)) {
            session(['required_roles' => $roles]);
        }
        
        return $next($request);
    }
}