<?php

declare(strict_types=1);

namespace CWF\Http\Middleware;

use CWF\Container\Container;
use CWF\Http\Request;
use CWF\Http\Response;
use Closure;

/**
 * Middleware Pipeline for processing requests
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class MiddlewarePipeline
{
    private array $pipes = [];

    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * Add middleware to pipeline
     */
    public function pipe(string|Closure $middleware): self
    {
        $this->pipes[] = $middleware;
        return $this;
    }

    /**
     * Handle request through pipeline
     */
    public function handle(Request $request, Closure $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $destination
        );

        return $pipeline($request);
    }

    /**
     * Create middleware layer
     */
    private function carry(): Closure
    {
        return function ($stack, $pipe) {
            return function ($request) use ($stack, $pipe) {
                if ($pipe instanceof Closure) {
                    return $pipe($request, $stack);
                }

                // Resolve middleware from container
                $middleware = $this->container->make($pipe);

                return $middleware->handle($request, $stack);
            };
        };
    }
}
