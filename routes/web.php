<?php

use Illuminate\Support\Facades\Route;
use phpseclib3\Net\SSH2;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Debug routes (load from separate file)
 */
include __DIR__ . '/debug.php';

/**
 * Ultra-simple test route
 */
Route::get('/test', function () {
    return 'App is working at ' . date('Y-m-d H:i:s');
});

/**
 * Root route - simple version
 */
Route::get('/', function () {
    try {
        return view('dashboard');
    } catch (\Exception $e) {
        return 'Error loading dashboard: ' . $e->getMessage();
    }
});

/**
 * Dashboard route
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


/**
 * Route to HANDLE the button click - NO DATABASE VERSION
 */
Route::post('/run-script', function () {
    
    // --- 1. CONFIGURE YOUR PI'S DETAILS ---
    $pi_ip   = '100.94.110.127'; // e.g., '100.123.45.67'
    $pi_user = 'casperadamus';                   // Or whatever your username is
    $pi_pass = '1016';
    $command = 'python3 lighton.py'; // The script you want to run
    // ----------------------------------------

    // --- 2. THE SSH LOGIC ---
    try {
        $ssh = new SSH2($pi_ip);
        $ssh->setTimeout(5); // 5-second timeout

        if (!$ssh->login($pi_user, $pi_pass)) {
            // Login failed - return JSON instead of session redirect
            return response()->json([
                'success' => false,
                'message' => 'SSH Login Failed. Check IP, username, and password.'
            ], 400);
        }

        // --- 3. RUN THE COMMAND ---
        $non_blocking_command = $command . ' > /tmp/script.log 2>&1 &';
        
        $ssh->exec($non_blocking_command);

        // Success! Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Command sent to Pi successfully!'
        ]);

    } catch (\Exception $e) {
        // Handle connection errors (e.g., Pi is offline)
        return response()->json([
            'success' => false,
            'message' => 'Error connecting to Pi: ' . $e->getMessage()
        ], 500);
    }

})->name('run-script');