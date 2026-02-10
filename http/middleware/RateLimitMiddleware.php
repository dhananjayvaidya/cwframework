<?php

declare(strict_types=1);

namespace App\Middleware;

use CWF\Http\Request;
use CWF\Http\Response;
use Closure;

/**
 * Rate Limiting Middleware
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(
        int $maxAttempts = 60,
        int $decayMinutes = 1
    ) {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        $attempts = (int) cache()->get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            return Response::error('Too Many Requests', 429);
        }

        cache()->set($key, $attempts + 1, $this->decayMinutes * 60);

        $response = $next($request);

        $response->setHeaders([
            'X-RateLimit-Limit' => (string) $this->maxAttempts,
            'X-RateLimit-Remaining' => (string) max(0, $this->maxAttempts - $attempts - 1),
        ]);

        return $response;
    }

    private function resolveRequestSignature(Request $request): string
    {
        return 'rate_limit:' . sha1($request->ip() . '|' . $request->uri());
    }
}
