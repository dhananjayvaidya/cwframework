<?php

declare(strict_types=1);

/**
 * CWFramework Bootstrap File
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */

// Autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Create application instance
$app = new \CWF\Application(__DIR__);

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Set error reporting based on environment
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Set timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Register service providers
$app->register(\CWF\Providers\DatabaseServiceProvider::class);
$app->register(\CWF\Providers\CacheServiceProvider::class);
$app->register(\CWF\Providers\AIServiceProvider::class);

// Load routes
require_once __DIR__ . '/config/routes.php';

return $app;
