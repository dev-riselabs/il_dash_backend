<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            env('FRONTEND_URL', 'http://localhost:5173'),
            'http://localhost:5173',
            'http://127.0.0.1:5173',
            'https://eventsintel.com',
            'https://www.eventsintel.com',
            'https://api.eventsintel.com',
        ];

        $origin = $request->header('Origin');
        $isAllowedOrigin = $origin && in_array($origin, $allowedOrigins);

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            return $this->buildCorsResponse($origin, $isAllowedOrigin);
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to response
        if ($isAllowedOrigin) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token, X-Requested-With, Accept, x-xsrf-token');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '3600');
        }

        return $response;
    }

    /**
     * Build CORS response for preflight requests
     */
    private function buildCorsResponse($origin, $isAllowed)
    {
        $response = response('', 200);

        if ($isAllowed) {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-Token, X-Requested-With, Accept, x-xsrf-token');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '3600');
        }

        return $response;
    }
}
