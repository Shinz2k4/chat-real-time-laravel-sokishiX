<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | These settings help optimize the performance of your Laravel application
    | by configuring various caching and optimization features.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Query Cache Settings
    |--------------------------------------------------------------------------
    |
    | Enable query result caching to reduce database load
    |
    */
    'query_cache' => env('QUERY_CACHE_ENABLED', true),
    'query_cache_ttl' => env('QUERY_CACHE_TTL', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Model Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache frequently accessed models
    |
    */
    'model_cache' => env('MODEL_CACHE_ENABLED', true),
    'model_cache_ttl' => env('MODEL_CACHE_TTL', 600), // 10 minutes

    /*
    |--------------------------------------------------------------------------
    | Response Compression
    |--------------------------------------------------------------------------
    |
    | Enable response compression for better performance
    |
    */
    'response_compression' => env('RESPONSE_COMPRESSION', true),

    /*
    |--------------------------------------------------------------------------
    | Database Connection Pooling
    |--------------------------------------------------------------------------
    |
    | Configure database connection pooling for better performance
    |
    */
    'db_pool_size' => env('DB_POOL_SIZE', 10),
    'db_max_connections' => env('DB_MAX_CONNECTIONS', 100),

    /*
    |--------------------------------------------------------------------------
    | Memory Optimization
    |--------------------------------------------------------------------------
    |
    | Configure memory usage optimization
    |
    */
    'memory_limit' => env('MEMORY_LIMIT', '256M'),
    'max_execution_time' => env('MAX_EXECUTION_TIME', 30),
];

