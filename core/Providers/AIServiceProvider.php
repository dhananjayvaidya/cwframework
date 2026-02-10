<?php

declare(strict_types=1);

namespace CWF\Providers;

use CWF\Application;
use CWF\AI\AIManager;

/**
 * AI Service Provider
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class AIServiceProvider
{
    public function __construct(
        private Application $app
    ) {
    }

    public function register(): void
    {
        $this->app->container->singleton(AIManager::class, function() {
            $config = require $this->app->configPath('ai.php');
            return new AIManager($config);
        });
    }
}
