<?php

declare(strict_types=1);

namespace CWF\Providers;

use CWF\Application;
use CWF\Database\DatabaseManager;

/**
 * Database Service Provider
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class DatabaseServiceProvider
{
    public function __construct(
        private Application $app
    ) {
    }

    public function register(): void
    {
        $this->app->container->singleton(DatabaseManager::class, function() {
            return new DatabaseManager();
        });
    }
}
