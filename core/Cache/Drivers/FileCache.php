<?php

declare(strict_types=1);

namespace CWF\Cache\Drivers;

/**
 * File-based Cache Driver
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class FileCache implements CacheInterface
{
    public function __construct(
        private readonly string $path
    ) {
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires_at'] !== null && time() > $data['expires_at']) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        $file = $this->getFilePath($key);
        $expiresAt = $ttl ? time() + $ttl : null;

        $data = serialize([
            'value' => $value,
            'expires_at' => $expiresAt
        ]);

        return file_put_contents($file, $data, LOCK_EX) !== false;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function delete(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    public function clear(): bool
    {
        $files = glob($this->path . '/*.cache');

        foreach ($files as $file) {
            unlink($file);
        }

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

    private function getFilePath(string $key): string
    {
        return $this->path . '/' . md5($key) . '.cache';
    }
}
