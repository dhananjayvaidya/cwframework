<?php

declare(strict_types=1);

namespace CWF\Studio\Generator;

use CWF\Application;

/**
 * Visual Code Generator
 *
 * Generate controllers, models, middleware through UI
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class CodeGenerator
{
    private array $templates = [];
    private int $filesGenerated = 0;

    public function __construct(
        private readonly Application $app
    ) {
        $this->loadTemplates();
    }

    /**
     * Load code templates
     */
    private function loadTemplates(): void
    {
        $this->templates = [
            'controller' => new ControllerTemplate(),
            'model' => new ModelTemplate(),
            'middleware' => new MiddlewareTemplate(),
            'migration' => new MigrationTemplate(),
            'api_controller' => new ApiControllerTemplate(),
            'crud_controller' => new CrudControllerTemplate(),
        ];
    }

    /**
     * Generate controller
     */
    public function generateController(array $config): string
    {
        $name = $config['name'];
        $namespace = $config['namespace'] ?? 'App\\Controllers';
        $methods = $config['methods'] ?? ['index'];
        $useAI = $config['use_ai'] ?? false;

        $code = $this->templates['controller']->generate([
            'name' => $name,
            'namespace' => $namespace,
            'methods' => $methods,
            'use_ai' => $useAI,
        ]);

        $file = $this->app->path("http/controllers/{$name}.php");

        if (file_exists($file) && !($config['overwrite'] ?? false)) {
            throw new \RuntimeException("Controller {$name} already exists");
        }

        file_put_contents($file, $code);
        $this->filesGenerated++;

        app('studio')->logActivity('controller_generated', ['name' => $name]);

        return $file;
    }

    /**
     * Generate model
     */
    public function generateModel(array $config): string
    {
        $name = $config['name'];
        $table = $config['table'] ?? strtolower($name) . 's';
        $fields = $config['fields'] ?? [];

        $code = $this->templates['model']->generate([
            'name' => $name,
            'table' => $table,
            'fields' => $fields,
        ]);

        $file = $this->app->path("http/models/{$name}.php");

        if (file_exists($file) && !($config['overwrite'] ?? false)) {
            throw new \RuntimeException("Model {$name} already exists");
        }

        file_put_contents($file, $code);
        $this->filesGenerated++;

        app('studio')->logActivity('model_generated', ['name' => $name, 'table' => $table]);

        return $file;
    }

    /**
     * Generate middleware
     */
    public function generateMiddleware(array $config): string
    {
        $name = $config['name'];
        $logic = $config['logic'] ?? '';

        $code = $this->templates['middleware']->generate([
            'name' => $name,
            'logic' => $logic,
        ]);

        $file = $this->app->path("http/middleware/{$name}.php");

        if (file_exists($file) && !($config['overwrite'] ?? false)) {
            throw new \RuntimeException("Middleware {$name} already exists");
        }

        file_put_contents($file, $code);
        $this->filesGenerated++;

        app('studio')->logActivity('middleware_generated', ['name' => $name]);

        return $file;
    }

    /**
     * Generate CRUD (Controller + Model + Routes)
     */
    public function generateCrud(array $config): array
    {
        $name = $config['name'];
        $fields = $config['fields'] ?? [];

        $generated = [];

        // Generate Model
        $generated['model'] = $this->generateModel([
            'name' => $name . 'Model',
            'table' => strtolower($name) . 's',
            'fields' => $fields,
        ]);

        // Generate Controller
        $generated['controller'] = $this->generateController([
            'name' => $name . 'Controller',
            'methods' => ['index', 'create', 'store', 'edit', 'update', 'destroy'],
        ]);

        // Generate Routes
        $generated['routes'] = $this->generateRoutes([
            'resource' => strtolower($name),
            'controller' => $name . 'Controller',
        ]);

        return $generated;
    }

    /**
     * Generate routes
     */
    private function generateRoutes(array $config): string
    {
        $resource = $config['resource'];
        $controller = $config['controller'];

        $routes = "\n// Auto-generated routes for {$resource}\n";
        $routes .= "\$router->resource('{$resource}', '{$controller}');\n";

        $routesFile = $this->app->configPath('routes.php');
        file_put_contents($routesFile, $routes, FILE_APPEND);

        return $routesFile;
    }

    /**
     * Generate API endpoint
     */
    public function generateApiEndpoint(array $config): array
    {
        $name = $config['name'];
        $version = $config['version'] ?? 'v1';

        $generated = [];

        // Generate API Controller
        $generated['controller'] = $this->generateController([
            'name' => "Api\\{$version}\\{$name}Controller",
            'namespace' => "App\\Controllers\\Api\\{$version}",
            'methods' => ['index', 'show', 'store', 'update', 'destroy'],
        ]);

        // Generate API Routes
        $routes = "\n// API routes for {$name} ({$version})\n";
        $routes .= "\$router->group(['prefix' => '/api/{$version}'], function(\$router) {\n";
        $routes .= "    \$router->resource('" . strtolower($name) . "', 'Api\\{$version}\\{$name}Controller');\n";
        $routes .= "});\n";

        $routesFile = $this->app->configPath('routes.php');
        file_put_contents($routesFile, $routes, FILE_APPEND);
        $generated['routes'] = $routesFile;

        return $generated;
    }

    /**
     * Generate with AI assistance
     */
    public function generateWithAI(array $config): string
    {
        $type = $config['type']; // controller, model, etc.
        $description = $config['description'];

        $prompt = "Generate a PHP 8.5 {$type} for CWFramework based on this description:\n\n{$description}\n\n";
        $prompt .= "Use strict types, dependency injection, and modern PHP practices.";

        $response = ai()->chat([
            ['role' => 'system', 'content' => 'You are an expert PHP developer. Generate clean, modern PHP 8.5 code.'],
            ['role' => 'user', 'content' => $prompt]
        ]);

        $code = $response->getContent();

        // Extract code from markdown if present
        if (preg_match('/```php\n(.*?)```/s', $code, $matches)) {
            $code = $matches[1];
        }

        return $code;
    }

    /**
     * Get generation statistics
     */
    public function getStats(): array
    {
        $controllersDir = $this->app->path('http/controllers');
        $modelsDir = $this->app->path('http/models');
        $middlewareDir = $this->app->path('http/middleware');

        return [
            'controllers' => count(glob($controllersDir . '/*.php')),
            'models' => count(glob($modelsDir . '/*.php')),
            'middleware' => count(glob($middlewareDir . '/*.php')),
            'total_generated' => $this->filesGenerated,
        ];
    }

    /**
     * Validate generated code
     */
    public function validate(string $code): array
    {
        $errors = [];
        $warnings = [];

        // Check for syntax errors
        if (!$this->isValidPHP($code)) {
            $errors[] = 'Syntax error in generated code';
        }

        // Check for security issues
        if (strpos($code, 'eval(') !== false) {
            $errors[] = 'Security issue: eval() detected';
        }

        if (strpos($code, 'exec(') !== false) {
            $warnings[] = 'Warning: exec() function detected';
        }

        // Check for SQL injection vulnerabilities
        if (preg_match('/\$_(GET|POST|REQUEST)\s*\[.*?\]/', $code) &&
            !strpos($code, 'prepare(')) {
            $warnings[] = 'Potential SQL injection vulnerability';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if code is valid PHP
     */
    private function isValidPHP(string $code): bool
    {
        $tempFile = sys_get_temp_dir() . '/cwf_validate_' . uniqid() . '.php';
        file_put_contents($tempFile, $code);

        exec("php -l {$tempFile} 2>&1", $output, $return);
        unlink($tempFile);

        return $return === 0;
    }
}
