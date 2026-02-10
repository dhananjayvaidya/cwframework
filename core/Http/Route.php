<?php

declare(strict_types=1);

namespace CWF\Http;

use Closure;

/**
 * Route class representing a single route
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Route
{
    private array $middleware = [];
    private ?string $name = null;

    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly Closure|array|string $action
    ) {
    }

    /**
     * Add middleware to route
     */
    public function middleware(string|array $middleware): self
    {
        $this->middleware = array_merge(
            $this->middleware,
            is_array($middleware) ? $middleware : [$middleware]
        );

        return $this;
    }

    /**
     * Set route name
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get route method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get route URI
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get route action
     */
    public function getAction(): Closure|array|string
    {
        return $this->action;
    }

    /**
     * Get route middleware
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get route name
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
