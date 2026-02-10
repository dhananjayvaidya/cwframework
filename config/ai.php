<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "openai", "anthropic"
    |
    */
    'default' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configurations
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),
            'max_tokens' => 2000,
            'temperature' => 0.7,
        ],

        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
            'max_tokens' => 4096,
            'temperature' => 0.7,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vector Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for vector databases used in RAG (Retrieval-Augmented Generation)
    |
    */
    'vector_db' => [
        'driver' => env('VECTOR_DB_DRIVER', 'pgvector'),

        'pgvector' => [
            'connection' => env('VECTOR_DB_CONNECTION', 'pgsql'),
            'table' => 'embeddings',
            'dimensions' => 1536, // OpenAI ada-002 dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Features
    |--------------------------------------------------------------------------
    */
    'features' => [
        'embeddings' => true,
        'vision' => true,
        'function_calling' => true,
        'streaming' => true,
    ],
];
