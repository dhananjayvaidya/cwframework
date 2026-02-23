<?php

declare(strict_types=1);

namespace CWF;

use CWF\Container\Container;
use CWF\Http\Router;
use CWF\Http\Request;
use CWF\Http\Response;
use CWF\Database\DatabaseManager;
use CWF\Cache\CacheManager;
use CWF\Logging\Logger;
use Throwable;

/**
 * CWFramework Application Core
 *
 * Modern PHP 8.5 Application class with dependency injection,
 * middleware support, and AI integration capabilities
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Application
{
    private static ?Application $instance = null;

    // PHP 8.5 property hooks with lazy initialization
    public string $basePath {
        get => $this->basePath ??= realpath(__DIR__ . '/..');
    }

    public Container $container {
        get => $this->container ??= new Container();
    }

    public Router $router {
        get => $this->router ??= $this->container->make(Router::class);
    }

    private array $serviceProviders = [];
    private array $middleware = [];
    private bool $booted = false;

    public function __construct(
        private readonly ?string $basePath = null
    ) {
        if ($basePath) {
            $this->basePath = $basePath;
        }

        self::$instance = $this;
        $this->registerBaseBindings();
        $this->registerBaseProviders();
    }

    /**
     * Get application instance (Singleton)
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register base bindings in the container
     */
    private function registerBaseBindings(): void
    {
        $this->container->singleton(Application::class, fn() => $this);
        $this->container->singleton('app', fn() => $this);
        $this->container->singleton('path', fn() => $this->basePath);
    }

    /**
     * Register base service providers
     */
    private function registerBaseProviders(): void
    {
        // Core services will be registered here
        $this->container->singleton(DatabaseManager::class);
        $this->container->singleton(CacheManager::class);
        $this->container->singleton(Logger::class);
    }

    /**
     * Boot the application
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        // Load environment variables
        $this->loadEnvironment();

        // Boot service providers
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $this->container->call([$provider, 'boot']);
            }
        }

        $this->booted = true;
    }

    /**
     * Load environment variables
     */
    private function loadEnvironment(): void
    {
        $envFile = $this->basePath . '/.env';
        if (file_exists($envFile)) {
            $dotenv = \Dotenv\Dotenv::createImmutable($this->basePath);
            $dotenv->load();
        }
    }

    /**
     * Handle incoming HTTP request
     */
    public function handle(Request $request): Response
    {
        try {
            $this->boot();

            // Process middleware pipeline
            $response = $this->router->dispatch($request);

            return $response;

        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $request = Request::capture();
        $response = $this->handle($request);
        $response->send();
    }

    /**
     * Handle exceptions
     */
    private function handleException(Throwable $e): Response
    {
        // Log the exception
        $logger = $this->container->make(Logger::class);
        $logger->error($e->getMessage(), [
            'exception' => $e,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return error response
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return new Response(
            json_encode([
                'error' => true,
                'message' => env('APP_DEBUG') ? $e->getMessage() : 'Internal Server Error',
                'file' => env('APP_DEBUG') ? $e->getFile() : null,
                'line' => env('APP_DEBUG') ? $e->getLine() : null,
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ]),
            $statusCode,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Register a service provider
     */
    public function register(string $provider): void
    {
        $instance = new $provider($this);

        if (method_exists($instance, 'register')) {
            $instance->register();
        }

        $this->serviceProviders[] = $instance;
    }

    /**
     * Add global middleware
     */
    public function middleware(string|array $middleware): void
    {
        $this->middleware = array_merge(
            $this->middleware,
            is_array($middleware) ? $middleware : [$middleware]
        );
    }

    /**
     * Get registered middleware
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get base path
     */
    public function path(string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get config path
     */
    public function configPath(string $path = ''): string
    {
        return $this->path('config') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get storage path
     */
    public function storagePath(string $path = ''): string
    {
        return $this->path('storage') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}
