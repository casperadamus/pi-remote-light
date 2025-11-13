<?php

use Illuminate\Support\Facades\Route;

/**
 * Simple debug route that doesn't depend on sessions or database
 */
Route::get('/debug', function () {
    $info = [
        'status' => 'Laravel is working',
        'timestamp' => date('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'app_env' => env('APP_ENV', 'not set'),
        'app_debug' => env('APP_DEBUG', 'not set'),
        'app_key' => env('APP_KEY') ? 'SET' : 'NOT SET',
        'db_connection' => env('DB_CONNECTION', 'not set'),
        'current_path' => __DIR__,
        'storage_writable' => is_writable(storage_path()),
        'database_exists' => file_exists(database_path('database.sqlite')),
    ];
    
    return response()->json($info);
});

/**
 * Ultra-simple route with no dependencies
 */
Route::get('/simple', function () {
    return 'Laravel is working! Time: ' . date('Y-m-d H:i:s');
});
