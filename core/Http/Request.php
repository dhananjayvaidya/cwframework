<?php

declare(strict_types=1);

namespace CWF\Http;

/**
 * HTTP Request wrapper with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Request
{
    // PHP 8.5 property hooks for lazy initialization
    public array $query {
        get => $this->query ??= $_GET;
    }

    public array $request {
        get => $this->request ??= $_POST;
    }

    public array $server {
        get => $this->server ??= $_SERVER;
    }

    public array $cookies {
        get => $this->cookies ??= $_COOKIE;
    }

    public array $files {
        get => $this->files ??= $_FILES;
    }

    public array $headers {
        get => $this->headers ??= $this->parseHeaders();
    }

    private ?array $json = null;
    private ?string $content = null;

    public function __construct(
        private array $queryParams = [],
        private array $requestParams = [],
        private array $serverParams = [],
        private array $cookieParams = [],
        private array $fileParams = [],
        private ?string $contentData = null
    ) {
        if ($queryParams) $this->query = $queryParams;
        if ($requestParams) $this->request = $requestParams;
        if ($serverParams) $this->server = $serverParams;
        if ($cookieParams) $this->cookies = $cookieParams;
        if ($fileParams) $this->files = $fileParams;
        if ($contentData) $this->content = $contentData;
    }

    /**
     * Create request from globals
     */
    public static function capture(): self
    {
        return new self(
            $_GET,
            $_POST,
            $_SERVER,
            $_COOKIE,
            $_FILES,
            file_get_contents('php://input')
        );
    }

    /**
     * Get request method
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get request URI
     */
    public function uri(): string
    {
        return strtok($this->server['REQUEST_URI'] ?? '/', '?');
    }

    /**
     * Get full URL
     */
    public function url(): string
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->server['REQUEST_URI'] ?? '/';

        return "{$protocol}://{$host}{$uri}";
    }

    /**
     * Check if request is secure
     */
    public function isSecure(): bool
    {
        return isset($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off';
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) &&
               strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request is JSON
     */
    public function isJson(): bool
    {
        return str_contains($this->header('Content-Type', ''), 'application/json');
    }

    /**
     * Get request input (query + post)
     */
    public function input(string $key = null, mixed $default = null): mixed
    {
        $all = array_merge($this->query, $this->request);

        if ($key === null) {
            return $all;
        }

        return $all[$key] ?? $default;
    }

    /**
     * Get JSON payload
     */
    public function json(string $key = null, mixed $default = null): mixed
    {
        if ($this->json === null) {
            $this->json = json_decode($this->content(), true) ?? [];
        }

        if ($key === null) {
            return $this->json;
        }

        return $this->json[$key] ?? $default;
    }

    /**
     * Get all input data
     */
    public function all(): array
    {
        return $this->isJson() ? $this->json() : $this->input();
    }

    /**
     * Get only specified keys
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get all except specified keys
     */
    public function except(array $keys): array
    {
        $all = $this->all();
        return array_diff_key($all, array_flip($keys));
    }

    /**
     * Check if input has key
     */
    public function has(string ...$keys): bool
    {
        $all = $this->all();

        foreach ($keys as $key) {
            if (!isset($all[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get header value
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    /**
     * Get bearer token
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization', '');

        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Get request IP address
     */
    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Get raw content
     */
    public function content(): string
    {
        if ($this->content === null) {
            $this->content = file_get_contents('php://input') ?: '';
        }

        return $this->content;
    }

    /**
     * Parse headers from server vars
     */
    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[strtolower($header)] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $header = str_replace('_', '-', $key);
                $headers[strtolower($header)] = $value;
            }
        }

        return $headers;
    }

    /**
     * Validate input using PHP 8.5 features
     */
    public function validate(array $rules): array
    {
        $validated = [];
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $this->input($field);

            // Simple validation (can be extended)
            if (str_contains($rule, 'required') && empty($value)) {
                $errors[$field] = "{$field} is required";
                continue;
            }

            if (str_contains($rule, 'email') && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "{$field} must be a valid email";
                continue;
            }

            $validated[$field] = $value;
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        return $validated;
    }
}
