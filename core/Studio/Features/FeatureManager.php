<?php

declare(strict_types=1);

namespace CWF\Studio\Features;

use CWF\Application;

/**
 * Feature Toggle Manager
 *
 * Enable/disable framework features dynamically without code changes
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class FeatureManager
{
    private array $features = [];
    private string $configFile;

    public function __construct(
        private readonly Application $app
    ) {
        $this->configFile = $app->storagePath('studio/features.json');
        $this->loadFeatures();
    }

    /**
     * Define available features
     */
    private function getAvailableFeatures(): array
    {
        return [
            'ai' => [
                'name' => 'AI Integration',
                'description' => 'OpenAI and Anthropic API integration',
                'category' => 'AI',
                'dependencies' => [],
                'config_file' => 'config/ai.php',
            ],
            'cache' => [
                'name' => 'Caching System',
                'description' => 'Multi-driver caching (File, Redis, Array)',
                'category' => 'Performance',
                'dependencies' => [],
                'config_file' => 'config/cache.php',
            ],
            'database' => [
                'name' => 'Database Layer',
                'description' => 'Query builder and database management',
                'category' => 'Core',
                'dependencies' => [],
                'required' => true,
            ],
            'authentication' => [
                'name' => 'Authentication',
                'description' => 'JWT and session-based authentication',
                'category' => 'Security',
                'dependencies' => [],
            ],
            'api_rate_limiting' => [
                'name' => 'API Rate Limiting',
                'description' => 'Request throttling and rate limits',
                'category' => 'Security',
                'dependencies' => ['cache'],
            ],
            'cors' => [
                'name' => 'CORS Support',
                'description' => 'Cross-Origin Resource Sharing',
                'category' => 'API',
                'dependencies' => [],
            ],
            'file_upload' => [
                'name' => 'File Upload',
                'description' => 'File upload and management system',
                'category' => 'Storage',
                'dependencies' => [],
            ],
            'email' => [
                'name' => 'Email System',
                'description' => 'PHPMailer integration for emails',
                'category' => 'Communication',
                'dependencies' => [],
            ],
            'logging' => [
                'name' => 'Logging System',
                'description' => 'Advanced logging with Monolog',
                'category' => 'Monitoring',
                'dependencies' => [],
            ],
            'queue' => [
                'name' => 'Queue System',
                'description' => 'Background job processing',
                'category' => 'Performance',
                'dependencies' => ['cache'],
            ],
            'real_time' => [
                'name' => 'Real-time Features',
                'description' => 'WebSocket and Server-Sent Events',
                'category' => 'Real-time',
                'dependencies' => [],
            ],
            'graphql' => [
                'name' => 'GraphQL API',
                'description' => 'GraphQL query language support',
                'category' => 'API',
                'dependencies' => [],
            ],
            'multi_tenancy' => [
                'name' => 'Multi-tenancy',
                'description' => 'Multi-tenant application support',
                'category' => 'Architecture',
                'dependencies' => ['database'],
            ],
            'admin_panel' => [
                'name' => 'Admin Panel',
                'description' => 'Built-in administration interface',
                'category' => 'UI',
                'dependencies' => ['authentication'],
            ],
            'api_documentation' => [
                'name' => 'API Documentation',
                'description' => 'Auto-generated API docs (OpenAPI/Swagger)',
                'category' => 'Documentation',
                'dependencies' => [],
            ],
        ];
    }

    /**
     * Load features from storage
     */
    private function loadFeatures(): void
    {
        $available = $this->getAvailableFeatures();

        if (file_exists($this->configFile)) {
            $stored = json_decode(file_get_contents($this->configFile), true);

            foreach ($available as $key => $feature) {
                $this->features[$key] = array_merge(
                    $feature,
                    ['enabled' => $stored[$key]['enabled'] ?? true]
                );
            }
        } else {
            // First time - enable all by default
            foreach ($available as $key => $feature) {
                $this->features[$key] = array_merge(
                    $feature,
                    ['enabled' => true]
                );
            }
            $this->save();
        }
    }

    /**
     * Save features to storage
     */
    private function save(): void
    {
        $dir = dirname($this->configFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $data = [];
        foreach ($this->features as $key => $feature) {
            $data[$key] = ['enabled' => $feature['enabled']];
        }

        file_put_contents($this->configFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Get all features
     */
    public function all(): array
    {
        return $this->features;
    }

    /**
     * Get enabled features
     */
    public function enabled(): array
    {
        return array_filter($this->features, fn($f) => $f['enabled']);
    }

    /**
     * Get disabled features
     */
    public function disabled(): array
    {
        return array_filter($this->features, fn($f) => !$f['enabled']);
    }

    /**
     * Check if feature is enabled
     */
    public function isEnabled(string $feature): bool
    {
        return $this->features[$feature]['enabled'] ?? false;
    }

    /**
     * Enable a feature
     */
    public function enable(string $feature): bool
    {
        if (!isset($this->features[$feature])) {
            return false;
        }

        // Check dependencies
        $dependencies = $this->features[$feature]['dependencies'] ?? [];
        foreach ($dependencies as $dependency) {
            if (!$this->isEnabled($dependency)) {
                throw new \RuntimeException(
                    "Cannot enable {$feature}: dependency {$dependency} is disabled"
                );
            }
        }

        $this->features[$feature]['enabled'] = true;
        $this->save();

        app('studio')->logActivity('feature_enabled', ['feature' => $feature]);
        return true;
    }

    /**
     * Disable a feature
     */
    public function disable(string $feature): bool
    {
        if (!isset($this->features[$feature])) {
            return false;
        }

        // Check if required
        if ($this->features[$feature]['required'] ?? false) {
            throw new \RuntimeException("Cannot disable required feature: {$feature}");
        }

        // Check if other features depend on this
        foreach ($this->features as $key => $f) {
            if ($f['enabled'] && in_array($feature, $f['dependencies'] ?? [])) {
                throw new \RuntimeException(
                    "Cannot disable {$feature}: {$key} depends on it"
                );
            }
        }

        $this->features[$feature]['enabled'] = false;
        $this->save();

        app('studio')->logActivity('feature_disabled', ['feature' => $feature]);
        return true;
    }

    /**
     * Toggle a feature
     */
    public function toggle(string $feature): bool
    {
        if ($this->isEnabled($feature)) {
            return $this->disable($feature);
        } else {
            return $this->enable($feature);
        }
    }

    /**
     * Get features by category
     */
    public function byCategory(string $category): array
    {
        return array_filter(
            $this->features,
            fn($f) => $f['category'] === $category
        );
    }

    /**
     * Get all categories
     */
    public function categories(): array
    {
        $categories = [];
        foreach ($this->features as $feature) {
            $category = $feature['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $feature;
        }
        return $categories;
    }

    /**
     * Get feature middleware
     */
    public function getMiddleware(): array
    {
        $middleware = [];

        if ($this->isEnabled('cors')) {
            $middleware[] = \App\Middleware\CorsMiddleware::class;
        }

        if ($this->isEnabled('api_rate_limiting')) {
            $middleware[] = \App\Middleware\RateLimitMiddleware::class;
        }

        if ($this->isEnabled('authentication')) {
            $middleware[] = \App\Middleware\AuthMiddleware::class;
        }

        return $middleware;
    }

    /**
     * Generate feature config files
     */
    public function generateConfig(string $feature): bool
    {
        if (!isset($this->features[$feature])) {
            return false;
        }

        $configFile = $this->features[$feature]['config_file'] ?? null;
        if (!$configFile) {
            return false;
        }

        $fullPath = $this->app->path($configFile);

        if (file_exists($fullPath)) {
            return true; // Already exists
        }

        // Generate default config based on feature
        $config = $this->getDefaultConfig($feature);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($fullPath, $config) !== false;
    }

    /**
     * Get default config for feature
     */
    private function getDefaultConfig(string $feature): string
    {
        return match($feature) {
            'queue' => $this->getQueueConfig(),
            'real_time' => $this->getRealTimeConfig(),
            'graphql' => $this->getGraphQLConfig(),
            default => "<?php\n\nreturn [];\n"
        };
    }

    private function getQueueConfig(): string
    {
        return <<<'PHP'
<?php

return [
    'default' => env('QUEUE_DRIVER', 'database'),

    'connections' => [
        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'retry_after' => 90,
        ],
    ],
];
PHP;
    }

    private function getRealTimeConfig(): string
    {
        return <<<'PHP'
<?php

return [
    'driver' => env('REALTIME_DRIVER', 'sse'),

    'websocket' => [
        'host' => env('WS_HOST', '0.0.0.0'),
        'port' => env('WS_PORT', 8080),
    ],

    'sse' => [
        'heartbeat_interval' => 30,
    ],
];
PHP;
    }

    private function getGraphQLConfig(): string
    {
        return <<<'PHP'
<?php

return [
    'schema' => base_path('graphql/schema.graphql'),

    'route' => [
        'uri' => '/graphql',
        'middleware' => ['cors'],
    ],

    'playground' => [
        'enabled' => env('GRAPHQL_PLAYGROUND', true),
        'uri' => '/graphql-playground',
    ],
];
PHP;
    }
}
