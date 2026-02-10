<?php

declare(strict_types=1);

namespace App\Middleware;

use CWF\Http\Request;
use CWF\Http\Response;
use Closure;

/**
 * CORS Middleware
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->setHeaders([
            'Access-Control-Allow-Origin' => env('CORS_ALLOWED_ORIGINS', '*'),
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Max-Age' => '3600',
        ]);

        return $response;
    }
}
