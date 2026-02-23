<?php

declare(strict_types=1);

namespace App\Middleware;

use CWF\Http\Request;
use CWF\Http\Response;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * JWT Authentication Middleware
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return Response::error('Unauthorized', 401);
        }

        try {
            $decoded = JWT::decode(
                $token,
                new Key(env('JWT_SECRET', 'your-secret-key'), 'HS256')
            );

            // Store user data in request
            $request->user = $decoded;

            return $next($request);

        } catch (\Exception $e) {
            return Response::error('Invalid token', 401);
        }
    }
}
