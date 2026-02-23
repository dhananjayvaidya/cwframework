<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Framework Studio
    |--------------------------------------------------------------------------
    |
    | Visual interface for managing the framework, generating code,
    | and building production releases
    |
    */

    'enabled' => env('STUDIO_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Studio URL Path
    |--------------------------------------------------------------------------
    */

    'path' => env('STUDIO_PATH', '/studio'),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'enabled' => true,
        'username' => env('STUDIO_USERNAME', 'admin'),
        'password' => env('STUDIO_PASSWORD', bcrypt('admin123')),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Allowed IP addresses. Use '*' to allow all IPs (not recommended for production)
    |
    */

    'allowed_ips' => env('STUDIO_ALLOWED_IPS', '*'),

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */

    'features' => [
        'code_generator' => true,
        'build_system' => true,
        'plugin_manager' => true,
        'ai_assistant' => true,
        'database_manager' => true,
        'api_tester' => true,
        'log_viewer' => true,
        'file_manager' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Build Configuration
    |--------------------------------------------------------------------------
    */

    'build' => [
        'default_encryption' => env('BUILD_ENCRYPTION', 'base64'),
        'minify_default' => true,
        'obfuscate_default' => false,
        'exclude_dirs' => [
            'storage/logs',
            'storage/cache',
            'storage/builds',
            '.git',
            'node_modules',
            'tests',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugin Marketplace
    |--------------------------------------------------------------------------
    */

    'marketplace' => [
        'url' => env('MARKETPLACE_URL', 'https://marketplace.cwframework.com'),
        'api_key' => env('MARKETPLACE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    */

    'session_timeout' => 7200, // 2 hours
];
