<?php

declare(strict_types=1);

namespace CWF\Cache\Drivers;

/**
 * In-Memory Array Cache Driver
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class ArrayCache implements CacheInterface
{
    private array $cache = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache[$key] ?? $default;
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $this->cache[$key] = $value;
        return true;
    }

    public function has(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    public function delete(string $key): bool
    {
        unset($this->cache[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->cache = [];
        return true;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }
}
