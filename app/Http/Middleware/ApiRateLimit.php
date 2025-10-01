<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'api:' . $request->ip();
        
        // Rate limit: 1000 requests per hour
        if (RateLimiter::tooManyAttempts($key, 1000)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter
            ], 429);
        }
        
        RateLimiter::hit($key, 3600); // 1 hour
        
        return $next($request);
    }
}
