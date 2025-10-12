<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OptimizeResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add performance headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Enable gzip compression for JSON responses
        if ($response->headers->get('Content-Type') === 'application/json') {
            $response->headers->set('Content-Encoding', 'gzip');
        }

        return $response;
    }
}

