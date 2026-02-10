<?php

declare(strict_types=1);

namespace CWF\Cache\Drivers;

/**
 * Cache Driver Interface
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
interface CacheInterface
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value, ?int $ttl = null): bool;
    public function has(string $key): bool;
    public function delete(string $key): bool;
    public function clear(): bool;
    public function increment(string $key, int $value = 1): int;
    public function decrement(string $key, int $value = 1): int;
}
