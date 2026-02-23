<?php

declare(strict_types=1);

namespace CWF\Http;

use CWF\Container\Container;
use CWF\Http\Middleware\MiddlewarePipeline;
use Closure;

/**
 * Modern Router with middleware support and PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
        'OPTIONS' => []
    ];

    private array $middleware = [];
    private array $routeMiddleware = [];
    private ?string $currentGroupPrefix = null;
    private array $currentGroupMiddleware = [];

    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * Register GET route
     */
    public function get(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('GET', $uri, $action);
    }

    /**
     * Register POST route
     */
    public function post(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register PUT route
     */
    public function put(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register PATCH route
     */
    public function patch(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register DELETE route
     */
    public function delete(string $uri, Closure|array|string $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register route for any method
     */
    public function any(string $uri, Closure|array|string $action): array
    {
        $routes = [];
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $routes[] = $this->addRoute($method, $uri, $action);
        }
        return $routes;
    }

    /**
     * Register API resource routes
     */
    public function resource(string $name, string $controller): void
    {
        $this->get("/{$name}", "{$controller}@index");
        $this->post("/{$name}", "{$controller}@store");
        $this->get("/{$name}/{id}", "{$controller}@show");
        $this->put("/{$name}/{id}", "{$controller}@update");
        $this->patch("/{$name}/{id}", "{$controller}@update");
        $this->delete("/{$name}/{id}", "{$controller}@destroy");
    }

    /**
     * Register CRUD routes (legacy support)
     */
    public function crud(string $uri, string $controller): void
    {
        $this->get("{$uri}/view", "{$controller}@view");
        $this->get("{$uri}/add", "{$controller}@add");
        $this->post("{$uri}/add", "{$controller}@add");
        $this->get("{$uri}/update/{id}", "{$controller}@update");
        $this->post("{$uri}/update/{id}", "{$controller}@update");
        $this->post("{$uri}/delete/{id}", "{$controller}@delete");
    }

    /**
     * Create route group
     */
    public function group(array $attributes, Closure $callback): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddleware = $this->currentGroupMiddleware;

        $this->currentGroupPrefix = ($previousPrefix ?? '') . ($attributes['prefix'] ?? '');
        $this->currentGroupMiddleware = array_merge(
            $previousMiddleware,
            $attributes['middleware'] ?? []
        );

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddleware = $previousMiddleware;
    }

    /**
     * Add route to collection
     */
    private function addRoute(string $method, string $uri, Closure|array|string $action): Route
    {
        $uri = $this->currentGroupPrefix . $uri;
        $uri = '/' . trim($uri, '/');

        $route = new Route($method, $uri, $action);
        $route->middleware($this->currentGroupMiddleware);

        $this->routes[$method][$uri] = $route;

        return $route;
    }

    /**
     * Dispatch request
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = $request->uri();

        // Find matching route
        $route = $this->findRoute($method, $uri);

        if (!$route) {
            return Response::error('Route not found', 404);
        }

        // Extract route parameters
        $parameters = $this->extractParameters($route, $uri);

        // Build middleware pipeline
        $pipeline = new MiddlewarePipeline($this->container);

        // Add route-specific middleware
        foreach ($route->getMiddleware() as $middleware) {
            $pipeline->pipe($middleware);
        }

        // Execute through middleware pipeline
        return $pipeline->handle($request, function ($request) use ($route, $parameters) {
            return $this->runRoute($route, $request, $parameters);
        });
    }

    /**
     * Find matching route
     */
    private function findRoute(string $method, string $uri): ?Route
    {
        // Exact match
        if (isset($this->routes[$method][$uri])) {
            return $this->routes[$method][$uri];
        }

        // Pattern match
        foreach ($this->routes[$method] as $routeUri => $route) {
            if ($this->matchRoute($routeUri, $uri)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Check if route matches URI
     */
    private function matchRoute(string $routeUri, string $uri): bool
    {
        // Convert route parameters to regex
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        return (bool) preg_match($pattern, $uri);
    }

    /**
     * Extract parameters from URI
     */
    private function extractParameters(Route $route, string $uri): array
    {
        $routeUri = $route->getUri();

        // Get parameter names
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routeUri, $names);
        $names = $names[1] ?? [];

        // Get parameter values
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $routeUri);
        $pattern = '#^' . $pattern . '$#';

        preg_match($pattern, $uri, $values);
        array_shift($values); // Remove full match

        // Combine names and values
        return array_combine($names, $values) ?: [];
    }

    /**
     * Run route action
     */
    private function runRoute(Route $route, Request $request, array $parameters): Response
    {
        $action = $route->getAction();

        // Handle closure
        if ($action instanceof Closure) {
            $result = $this->container->call($action, array_merge(['request' => $request], $parameters));
            return $this->toResponse($result);
        }

        // Handle controller@method
        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action, 2);

            // Support both namespaced and legacy controller paths
            if (!str_contains($controller, '\\')) {
                $controller = "App\\Controllers\\{$controller}";
            }

            $controllerInstance = $this->container->make($controller);
            $result = $this->container->call(
                [$controllerInstance, $method],
                array_merge(['request' => $request], $parameters)
            );

            return $this->toResponse($result);
        }

        // Handle legacy controller->method format
        if (is_string($action) && str_contains($action, '->')) {
            [$controller, $method] = explode('->', $action, 2);

            // Legacy controller path
            $file = app()->path("http/controllers/{$controller}.php");
            if (file_exists($file)) {
                require_once $file;
            }

            $controllerInstance = new $controller();
            $result = call_user_func_array([$controllerInstance, $method], array_values($parameters));

            return $this->toResponse($result);
        }

        throw new \RuntimeException('Invalid route action');
    }

    /**
     * Convert result to Response
     */
    private function toResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }

        return new Response((string) $result);
    }

    /**
     * Get all routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
