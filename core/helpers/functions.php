<?php

declare(strict_types=1);

use CWF\Application;
use CWF\Http\Response;

if (!function_exists('app')) {
    /**
     * Get application instance
     */
    function app(?string $abstract = null): mixed
    {
        $app = Application::getInstance();

        if ($abstract === null) {
            return $app;
        }

        return $app->container->make($abstract);
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        // Convert string booleans
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value
        };
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $config = [];

        if (empty($config)) {
            $configFiles = glob(app()->configPath('*.php'));
            foreach ($configFiles as $file) {
                $name = basename($file, '.php');
                $config[$name] = require $file;
            }
        }

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}

if (!function_exists('base_url')) {
    /**
     * Get base URL
     */
    function base_url(string $path = ''): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

        $url = "{$protocol}://{$host}{$basePath}";

        if ($path) {
            $url .= '/' . ltrim($path, '/');
        }

        return $url;
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL for path
     */
    function url(string $path = ''): string
    {
        return base_url($path);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create redirect response
     */
    function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }
}

if (!function_exists('json')) {
    /**
     * Create JSON response
     */
    function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
}

if (!function_exists('view')) {
    /**
     * Create view response
     */
    function view(string $view, array $data = []): Response
    {
        return Response::view($view, $data);
    }
}

if (!function_exists('cache')) {
    /**
     * Get cache manager instance
     */
    function cache(?string $driver = null): \CWF\Cache\CacheManager
    {
        $manager = app(\CWF\Cache\CacheManager::class);
        return $driver ? $manager->driver($driver) : $manager;
    }
}

if (!function_exists('db')) {
    /**
     * Get database manager instance
     */
    function db(?string $table = null): \CWF\Database\DatabaseManager|\CWF\Database\QueryBuilder
    {
        $manager = app(\CWF\Database\DatabaseManager::class);
        return $table ? $manager->table($table) : $manager;
    }
}

if (!function_exists('logger')) {
    /**
     * Get logger instance
     */
    function logger(): \CWF\Logging\Logger
    {
        return app(\CWF\Logging\Logger::class);
    }
}

if (!function_exists('ai')) {
    /**
     * Get AI manager instance
     */
    function ai(?string $provider = null): \CWF\AI\AIManager
    {
        $manager = app(\CWF\AI\AIManager::class);
        return $provider ? $manager->provider($provider) : $manager;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     */
    function dd(mixed ...$vars): never
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variable
     */
    function dump(mixed ...$vars): void
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
    }
}

if (!function_exists('now')) {
    /**
     * Get current timestamp
     */
    function now(): int
    {
        return time();
    }
}

if (!function_exists('today')) {
    /**
     * Get today's date
     */
    function today(string $format = 'Y-m-d'): string
    {
        return date($format);
    }
}

if (!function_exists('str_random')) {
    /**
     * Generate random string
     */
    function str_random(int $length = 16): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Hash password using bcrypt
     */
    function bcrypt(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}

if (!function_exists('verify_password')) {
    /**
     * Verify password hash
     */
    function verify_password(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with error
     */
    function abort(int $code = 404, string $message = ''): never
    {
        http_response_code($code);
        echo $message ?: "Error {$code}";
        die();
    }
}

if (!function_exists('session')) {
    /**
     * Get/set session value
     */
    function session(?string $key = null, mixed $default = null): mixed
    {
        if (!session_id()) {
            session_start();
        }

        if ($key === null) {
            return $_SESSION;
        }

        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $_SESSION;

            foreach ($keys as $segment) {
                if (!isset($value[$segment])) {
                    return $default;
                }
                $value = $value[$segment];
            }

            return $value;
        }

        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token(): string
    {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF hidden field
     */
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}
