<?php

declare(strict_types=1);

namespace CWF\Providers;

use CWF\Application;
use CWF\Cache\CacheManager;

/**
 * Cache Service Provider
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class CacheServiceProvider
{
    public function __construct(
        private Application $app
    ) {
    }

    public function register(): void
    {
        $this->app->container->singleton(CacheManager::class, function() {
            $config = require $this->app->configPath('cache.php');
            return new CacheManager($config);
        });
    }
}
