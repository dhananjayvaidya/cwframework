<?php

declare(strict_types=1);

namespace CWF\Cache;

use CWF\Cache\Drivers\FileCache;
use CWF\Cache\Drivers\RedisCache;
use CWF\Cache\Drivers\ArrayCache;
use CWF\Cache\Drivers\CacheInterface;

/**
 * Cache Manager with multiple driver support
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class CacheManager
{
    private array $drivers = [];
    private ?string $defaultDriver = null;

    public function __construct(array $config = [])
    {
        $this->defaultDriver = $config['default'] ?? 'file';
        $this->initializeDrivers($config);
    }

    /**
     * Initialize cache drivers
     */
    private function initializeDrivers(array $config): void
    {
        // Array cache (in-memory)
        $this->drivers['array'] = new ArrayCache();

        // File cache
        if (isset($config['drivers']['file'])) {
            $this->drivers['file'] = new FileCache(
                $config['drivers']['file']['path'] ?? app()->storagePath('cache')
            );
        }

        // Redis cache
        if (isset($config['drivers']['redis'])) {
            $this->drivers['redis'] = new RedisCache(
                $config['drivers']['redis']
            );
        }
    }

    /**
     * Get cache driver
     */
    public function driver(?string $name = null): CacheInterface
    {
        $name = $name ?? $this->defaultDriver;

        if (!isset($this->drivers[$name])) {
            throw new \InvalidArgumentException("Cache driver {$name} not found");
        }

        return $this->drivers[$name];
    }

    /**
     * Get cached value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver()->get($key, $default);
    }

    /**
     * Set cached value
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        return $this->driver()->set($key, $value, $ttl);
    }

    /**
     * Check if key exists
     */
    public function has(string $key): bool
    {
        return $this->driver()->has($key);
    }

    /**
     * Delete cached value
     */
    public function delete(string $key): bool
    {
        return $this->driver()->delete($key);
    }

    /**
     * Clear all cache
     */
    public function clear(): bool
    {
        return $this->driver()->clear();
    }

    /**
     * Remember (get or set)
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Remember forever
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        return $this->remember($key, 0, $callback);
    }

    /**
     * Increment value
     */
    public function increment(string $key, int $value = 1): int
    {
        return $this->driver()->increment($key, $value);
    }

    /**
     * Decrement value
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $this->driver()->decrement($key, $value);
    }
}
