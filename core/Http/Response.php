<?php

declare(strict_types=1);

namespace CWF\Http;

/**
 * HTTP Response with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Response
{
    private static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    public function __construct(
        private mixed $content = '',
        private int $statusCode = 200,
        private array $headers = []
    ) {
    }

    /**
     * Set response content
     */
    public function setContent(mixed $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get response content
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

    /**
     * Set status code
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set header
     */
    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set headers
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * Get headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Send response
     */
    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * Send headers
     */
    private function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        // Status
        $statusText = self::$statusTexts[$this->statusCode] ?? 'Unknown';
        header("HTTP/1.1 {$this->statusCode} {$statusText}");

        // Headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }

    /**
     * Send content
     */
    private function sendContent(): void
    {
        echo $this->content;
    }

    /**
     * Create JSON response
     */
    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json';

        return new self(
            json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $status,
            $headers
        );
    }

    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $status = 302): self
    {
        return new self('', $status, ['Location' => $url]);
    }

    /**
     * Create view response
     */
    public static function view(string $view, array $data = []): self
    {
        // Extract data for view
        extract($data);

        ob_start();
        $viewFile = app()->path("resources/views/{$view}.php");

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \RuntimeException("View {$view} not found");
        }

        $content = ob_get_clean();

        return new self($content, 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Download response
     */
    public static function download(string $file, ?string $name = null): self
    {
        if (!file_exists($file)) {
            throw new \RuntimeException("File {$file} not found");
        }

        $name = $name ?? basename($file);
        $content = file_get_contents($file);

        return new self($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$name}\"",
            'Content-Length' => (string) strlen($content)
        ]);
    }

    /**
     * No content response
     */
    public static function noContent(): self
    {
        return new self('', 204);
    }

    /**
     * Error response
     */
    public static function error(string $message, int $status = 500): self
    {
        return self::json([
            'error' => true,
            'message' => $message
        ], $status);
    }
}
