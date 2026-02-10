<?php

declare(strict_types=1);

namespace CWF\Container;

use Closure;
use ReflectionClass;
use ReflectionParameter;
use ReflectionNamedType;
use Psr\Container\ContainerInterface;
use CWF\Container\Exceptions\ContainerException;
use CWF\Container\Exceptions\NotFoundException;

/**
 * Dependency Injection Container with PHP 8.5 features
 *
 * Implements PSR-11 Container Interface with auto-wiring,
 * singleton support, and contextual binding
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Container implements ContainerInterface
{
    private array $bindings = [];
    private array $instances = [];
    private array $aliases = [];
    private array $contextual = [];

    /**
     * Bind a type to the container
     */
    public function bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared
        ];
    }

    /**
     * Bind a singleton to the container
     */
    public function singleton(string $abstract, Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Alias a type
     */
    public function alias(string $abstract, string $alias): void
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Get an entry from the container (PSR-11)
     */
    public function get(string $id): mixed
    {
        return $this->make($id);
    }

    /**
     * Check if container has an entry (PSR-11)
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) ||
               isset($this->instances[$id]) ||
               isset($this->aliases[$id]);
    }

    /**
     * Resolve and return an instance
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        // Check for alias
        $abstract = $this->aliases[$abstract] ?? $abstract;

        // Return existing instance if singleton
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Get concrete implementation
        $concrete = $this->bindings[$abstract]['concrete'] ?? $abstract;

        // Build the instance
        $object = $this->build($concrete, $parameters);

        // Store if singleton
        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    /**
     * Build an instance of the given type
     */
    private function build(Closure|string $concrete, array $parameters = []): mixed
    {
        // If closure, resolve it
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }

        // Reflect on the class
        try {
            $reflector = new ReflectionClass($concrete);
        } catch (\ReflectionException $e) {
            throw new NotFoundException("Class {$concrete} not found", 0, $e);
        }

        // Check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new ContainerException("Class {$concrete} is not instantiable");
        }

        // Get constructor
        $constructor = $reflector->getConstructor();

        // If no constructor, return new instance
        if ($constructor === null) {
            return new $concrete();
        }

        // Get constructor parameters
        $dependencies = $constructor->getParameters();

        // Resolve dependencies
        $instances = $this->resolveDependencies($dependencies, $parameters);

        // Create instance
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Resolve dependencies using PHP 8.5 features
     */
    private function resolveDependencies(array $dependencies, array $parameters = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            // Check if parameter was explicitly passed
            if (isset($parameters[$dependency->getName()])) {
                $results[] = $parameters[$dependency->getName()];
                continue;
            }

            // Get the type
            $type = $dependency->getType();

            // Handle no type or built-in types
            if ($type === null || !$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                // Check for default value
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                } else {
                    throw new ContainerException(
                        "Cannot resolve parameter {$dependency->getName()}"
                    );
                }
                continue;
            }

            // Resolve class dependency
            $className = $type->getName();

            try {
                $results[] = $this->make($className);
            } catch (ContainerException $e) {
                if ($dependency->isDefaultValueAvailable()) {
                    $results[] = $dependency->getDefaultValue();
                } elseif ($type->allowsNull()) {
                    $results[] = null;
                } else {
                    throw $e;
                }
            }
        }

        return $results;
    }

    /**
     * Call a callback with dependency injection
     */
    public function call(callable|array $callback, array $parameters = []): mixed
    {
        if (is_array($callback)) {
            [$class, $method] = $callback;

            $instance = is_object($class) ? $class : $this->make($class);

            $reflector = new \ReflectionMethod($instance, $method);
            $dependencies = $reflector->getParameters();

            $instances = $this->resolveDependencies($dependencies, $parameters);

            return $reflector->invokeArgs($instance, $instances);
        }

        if ($callback instanceof Closure) {
            $reflector = new \ReflectionFunction($callback);
            $dependencies = $reflector->getParameters();

            $instances = $this->resolveDependencies($dependencies, $parameters);

            return $reflector->invokeArgs($instances);
        }

        return call_user_func($callback, $parameters);
    }

    /**
     * Flush the container
     */
    public function flush(): void
    {
        $this->bindings = [];
        $this->instances = [];
        $this->aliases = [];
    }
}
