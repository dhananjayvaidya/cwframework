<?php

declare(strict_types=1);

namespace CWF\Studio;

use CWF\Application;
use CWF\Studio\Features\FeatureManager;
use CWF\Studio\Generator\CodeGenerator;
use CWF\Studio\Builder\BuildManager;
use CWF\Studio\Plugins\PluginManager;

/**
 * CWFramework Studio Manager
 *
 * Visual interface for framework management, code generation,
 * feature toggling, and build processes
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class StudioManager
{
    private FeatureManager $features;
    private CodeGenerator $generator;
    private BuildManager $builder;
    private PluginManager $plugins;

    // PHP 8.5 property hooks
    public array $config {
        get => $this->config ??= $this->loadConfig();
    }

    public bool $isEnabled {
        get => $this->isEnabled ?? env('STUDIO_ENABLED', false);
    }

    public function __construct(
        private readonly Application $app
    ) {
        $this->features = new FeatureManager($app);
        $this->generator = new CodeGenerator($app);
        $this->builder = new BuildManager($app);
        $this->plugins = new PluginManager($app);
    }

    /**
     * Load studio configuration
     */
    private function loadConfig(): array
    {
        $configFile = $this->app->configPath('studio.php');

        if (!file_exists($configFile)) {
            return $this->getDefaultConfig();
        }

        return require $configFile;
    }

    /**
     * Get default studio configuration
     */
    private function getDefaultConfig(): array
    {
        return [
            'enabled' => env('STUDIO_ENABLED', true),
            'path' => env('STUDIO_PATH', '/studio'),
            'auth' => [
                'enabled' => true,
                'username' => env('STUDIO_USERNAME', 'admin'),
                'password' => env('STUDIO_PASSWORD', bcrypt('admin123')),
            ],
            'features' => [
                'code_generator' => true,
                'build_system' => true,
                'plugin_manager' => true,
                'ai_assistant' => true,
                'database_manager' => true,
                'api_tester' => true,
                'log_viewer' => true,
            ],
            'allowed_ips' => env('STUDIO_ALLOWED_IPS', '*'),
            'session_timeout' => 7200, // 2 hours
        ];
    }

    /**
     * Get feature manager
     */
    public function features(): FeatureManager
    {
        return $this->features;
    }

    /**
     * Get code generator
     */
    public function generator(): CodeGenerator
    {
        return $this->generator;
    }

    /**
     * Get build manager
     */
    public function builder(): BuildManager
    {
        return $this->builder;
    }

    /**
     * Get plugin manager
     */
    public function plugins(): PluginManager
    {
        return $this->plugins;
    }

    /**
     * Authenticate studio user
     */
    public function authenticate(string $username, string $password): bool
    {
        if (!$this->config['auth']['enabled']) {
            return true;
        }

        $validUsername = $this->config['auth']['username'];
        $validPassword = $this->config['auth']['password'];

        return $username === $validUsername &&
               verify_password($password, $validPassword);
    }

    /**
     * Check if IP is allowed
     */
    public function isIpAllowed(string $ip): bool
    {
        $allowed = $this->config['allowed_ips'];

        if ($allowed === '*') {
            return true;
        }

        $allowedIps = is_array($allowed) ? $allowed : explode(',', $allowed);
        return in_array($ip, $allowedIps);
    }

    /**
     * Get studio statistics
     */
    public function getStatistics(): array
    {
        return [
            'features' => [
                'total' => count($this->features->all()),
                'enabled' => count($this->features->enabled()),
                'disabled' => count($this->features->disabled()),
            ],
            'plugins' => [
                'total' => count($this->plugins->all()),
                'active' => count($this->plugins->active()),
            ],
            'generated_files' => $this->generator->getStats(),
            'builds' => $this->builder->getStats(),
            'framework' => [
                'version' => '3.0.0',
                'php_version' => PHP_VERSION,
                'environment' => env('APP_ENV', 'production'),
            ],
        ];
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $limit = 10): array
    {
        $activityLog = $this->app->storagePath('studio/activity.json');

        if (!file_exists($activityLog)) {
            return [];
        }

        $activities = json_decode(file_get_contents($activityLog), true);
        return array_slice($activities, 0, $limit);
    }

    /**
     * Log studio activity
     */
    public function logActivity(string $action, array $data = []): void
    {
        $activityLog = $this->app->storagePath('studio/activity.json');
        $logDir = dirname($activityLog);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $activities = file_exists($activityLog)
            ? json_decode(file_get_contents($activityLog), true)
            : [];

        array_unshift($activities, [
            'action' => $action,
            'data' => $data,
            'timestamp' => time(),
            'user' => session('studio_user', 'system'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        // Keep only last 1000 activities
        $activities = array_slice($activities, 0, 1000);

        file_put_contents($activityLog, json_encode($activities, JSON_PRETTY_PRINT));
    }

    /**
     * Export studio configuration
     */
    public function exportConfig(): string
    {
        return json_encode([
            'features' => $this->features->all(),
            'plugins' => $this->plugins->all(),
            'config' => $this->config,
            'exported_at' => date('Y-m-d H:i:s'),
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Import studio configuration
     */
    public function importConfig(string $json): bool
    {
        try {
            $data = json_decode($json, true);

            if (isset($data['features'])) {
                foreach ($data['features'] as $feature => $enabled) {
                    if ($enabled) {
                        $this->features->enable($feature);
                    } else {
                        $this->features->disable($feature);
                    }
                }
            }

            $this->logActivity('config_imported', ['timestamp' => time()]);
            return true;

        } catch (\Exception $e) {
            logger()->error('Studio config import failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
