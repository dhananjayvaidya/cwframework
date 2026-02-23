<?php

declare(strict_types=1);

namespace CWF\Cache\Drivers;

use Predis\Client;

/**
 * Redis Cache Driver
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class RedisCache implements CacheInterface
{
    private Client $redis;

    public function __construct(array $config = [])
    {
        $this->redis = new Client([
            'scheme' => $config['scheme'] ?? 'tcp',
            'host' => $config['host'] ?? '127.0.0.1',
            'port' => $config['port'] ?? 6379,
            'password' => $config['password'] ?? null,
            'database' => $config['database'] ?? 0,
        ]);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->redis->get($key);

        if ($value === null) {
            return $default;
        }

        return unserialize($value);
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $serialized = serialize($value);

        if ($ttl) {
            return (bool) $this->redis->setex($key, $ttl, $serialized);
        }

        return (bool) $this->redis->set($key, $serialized);
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->exists($key);
    }

    public function delete(string $key): bool
    {
        return (bool) $this->redis->del($key);
    }

    public function clear(): bool
    {
        return (bool) $this->redis->flushdb();
    }

    public function increment(string $key, int $value = 1): int
    {
        return (int) $this->redis->incrby($key, $value);
    }

    public function decrement(string $key, int $value = 1): int
    {
        return (int) $this->redis->decrby($key, $value);
    }
}
