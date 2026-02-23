<?php

declare(strict_types=1);

namespace CWF\Studio\Plugins;

use CWF\Application;

/**
 * Plugin Manager for extensibility
 *
 * Install, activate, and manage plugins from marketplace
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class PluginManager
{
    private array $plugins = [];
    private string $pluginsDir;
    private string $configFile;

    public function __construct(
        private readonly Application $app
    ) {
        $this->pluginsDir = $app->path('plugins');
        $this->configFile = $app->storagePath('studio/plugins.json');

        if (!is_dir($this->pluginsDir)) {
            mkdir($this->pluginsDir, 0755, true);
        }

        $this->loadPlugins();
    }

    /**
     * Load installed plugins
     */
    private function loadPlugins(): void
    {
        if (file_exists($this->configFile)) {
            $this->plugins = json_decode(file_get_contents($this->configFile), true);
        }

        // Scan plugins directory
        $dirs = glob($this->pluginsDir . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $pluginFile = $dir . '/plugin.json';

            if (file_exists($pluginFile)) {
                $meta = json_decode(file_get_contents($pluginFile), true);
                $slug = basename($dir);

                if (!isset($this->plugins[$slug])) {
                    $this->plugins[$slug] = array_merge($meta, [
                        'active' => false,
                        'installed_at' => time(),
                    ]);
                } else {
                    // Update metadata
                    $this->plugins[$slug] = array_merge(
                        $meta,
                        ['active' => $this->plugins[$slug]['active'] ?? false]
                    );
                }
            }
        }

        $this->save();
    }

    /**
     * Save plugin configuration
     */
    private function save(): void
    {
        $dir = dirname($this->configFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->configFile, json_encode($this->plugins, JSON_PRETTY_PRINT));
    }

    /**
     * Get all plugins
     */
    public function all(): array
    {
        return $this->plugins;
    }

    /**
     * Get active plugins
     */
    public function active(): array
    {
        return array_filter($this->plugins, fn($p) => $p['active'] ?? false);
    }

    /**
     * Get plugin by slug
     */
    public function get(string $slug): ?array
    {
        return $this->plugins[$slug] ?? null;
    }

    /**
     * Install plugin from marketplace
     */
    public function install(string $slug, ?string $source = null): bool
    {
        // Download from marketplace
        $source = $source ?? $this->getMarketplaceUrl($slug);

        $tempFile = sys_get_temp_dir() . '/' . $slug . '.zip';

        // Download plugin
        if (!$this->downloadPlugin($source, $tempFile)) {
            throw new \RuntimeException("Failed to download plugin: {$slug}");
        }

        // Extract to plugins directory
        $targetDir = $this->pluginsDir . '/' . $slug;

        if (!$this->extractZip($tempFile, $targetDir)) {
            throw new \RuntimeException("Failed to extract plugin: {$slug}");
        }

        unlink($tempFile);

        // Load plugin metadata
        $this->loadPlugins();

        app('studio')->logActivity('plugin_installed', ['plugin' => $slug]);

        return true;
    }

    /**
     * Uninstall plugin
     */
    public function uninstall(string $slug): bool
    {
        if (!isset($this->plugins[$slug])) {
            return false;
        }

        // Deactivate first
        if ($this->plugins[$slug]['active']) {
            $this->deactivate($slug);
        }

        // Remove plugin directory
        $pluginDir = $this->pluginsDir . '/' . $slug;

        if (is_dir($pluginDir)) {
            $this->deleteDirectory($pluginDir);
        }

        unset($this->plugins[$slug]);
        $this->save();

        app('studio')->logActivity('plugin_uninstalled', ['plugin' => $slug]);

        return true;
    }

    /**
     * Activate plugin
     */
    public function activate(string $slug): bool
    {
        if (!isset($this->plugins[$slug])) {
            return false;
        }

        // Check dependencies
        $dependencies = $this->plugins[$slug]['dependencies'] ?? [];
        foreach ($dependencies as $dependency) {
            if (!isset($this->plugins[$dependency]) || !$this->plugins[$dependency]['active']) {
                throw new \RuntimeException(
                    "Cannot activate {$slug}: dependency {$dependency} is not active"
                );
            }
        }

        $this->plugins[$slug]['active'] = true;
        $this->save();

        // Run activation hook
        $this->runHook($slug, 'activate');

        app('studio')->logActivity('plugin_activated', ['plugin' => $slug]);

        return true;
    }

    /**
     * Deactivate plugin
     */
    public function deactivate(string $slug): bool
    {
        if (!isset($this->plugins[$slug])) {
            return false;
        }

        // Check if other plugins depend on this
        foreach ($this->plugins as $pluginSlug => $plugin) {
            if ($plugin['active'] && in_array($slug, $plugin['dependencies'] ?? [])) {
                throw new \RuntimeException(
                    "Cannot deactivate {$slug}: {$pluginSlug} depends on it"
                );
            }
        }

        $this->plugins[$slug]['active'] = false;
        $this->save();

        // Run deactivation hook
        $this->runHook($slug, 'deactivate');

        app('studio')->logActivity('plugin_deactivated', ['plugin' => $slug]);

        return true;
    }

    /**
     * Run plugin hook
     */
    private function runHook(string $slug, string $hook): void
    {
        $hookFile = $this->pluginsDir . '/' . $slug . '/hooks/' . $hook . '.php';

        if (file_exists($hookFile)) {
            require_once $hookFile;
        }
    }

    /**
     * Search marketplace
     */
    public function search(string $query): array
    {
        // This would connect to actual marketplace API
        $marketplaceUrl = 'https://marketplace.cwframework.com/api/search';

        $response = file_get_contents($marketplaceUrl . '?q=' . urlencode($query));

        return json_decode($response, true) ?? [];
    }

    /**
     * Get featured plugins from marketplace
     */
    public function featured(): array
    {
        // Mock data - replace with actual marketplace API
        return [
            [
                'slug' => 'cwf-payment',
                'name' => 'Payment Gateway',
                'description' => 'Stripe, PayPal, and Razorpay integration',
                'version' => '1.0.0',
                'author' => 'CWFramework',
                'price' => 'free',
                'downloads' => 1250,
                'rating' => 4.8,
            ],
            [
                'slug' => 'cwf-seo',
                'name' => 'SEO Toolkit',
                'description' => 'Advanced SEO features and sitemap generation',
                'version' => '2.1.0',
                'author' => 'CWFramework',
                'price' => '$29',
                'downloads' => 856,
                'rating' => 4.6,
            ],
            [
                'slug' => 'cwf-analytics',
                'name' => 'Analytics Dashboard',
                'description' => 'Google Analytics and custom analytics',
                'version' => '1.5.2',
                'author' => 'CWFramework',
                'price' => 'free',
                'downloads' => 2100,
                'rating' => 4.9,
            ],
        ];
    }

    /**
     * Get marketplace URL for plugin
     */
    private function getMarketplaceUrl(string $slug): string
    {
        return "https://marketplace.cwframework.com/download/{$slug}";
    }

    /**
     * Download plugin file
     */
    private function downloadPlugin(string $url, string $destination): bool
    {
        $content = file_get_contents($url);

        if ($content === false) {
            return false;
        }

        return file_put_contents($destination, $content) !== false;
    }

    /**
     * Extract ZIP file
     */
    private function extractZip(string $zipFile, string $destination): bool
    {
        $zip = new \ZipArchive();

        if ($zip->open($zipFile) !== true) {
            return false;
        }

        $zip->extractTo($destination);
        $zip->close();

        return true;
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    /**
     * Create plugin template
     */
    public function createTemplate(string $name): string
    {
        $slug = strtolower(str_replace(' ', '-', $name));
        $pluginDir = $this->pluginsDir . '/' . $slug;

        mkdir($pluginDir, 0755, true);
        mkdir($pluginDir . '/hooks', 0755, true);
        mkdir($pluginDir . '/src', 0755, true);

        // Create plugin.json
        $meta = [
            'name' => $name,
            'slug' => $slug,
            'version' => '1.0.0',
            'description' => 'Plugin description',
            'author' => 'Your Name',
            'dependencies' => [],
        ];

        file_put_contents(
            $pluginDir . '/plugin.json',
            json_encode($meta, JSON_PRETTY_PRINT)
        );

        // Create main plugin file
        $code = "<?php\n\nnamespace CWF\\Plugins\\{$name};\n\nclass Plugin {\n    public function init() {\n        // Plugin initialization\n    }\n}\n";

        file_put_contents($pluginDir . '/src/Plugin.php', $code);

        // Create activation hook
        file_put_contents($pluginDir . '/hooks/activate.php', "<?php\n// Activation hook\n");

        // Create deactivation hook
        file_put_contents($pluginDir . '/hooks/deactivate.php', "<?php\n// Deactivation hook\n");

        $this->loadPlugins();

        return $pluginDir;
    }
}
