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
 * Root route - redirect to dashboard
 */
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/**
 * Route to SHOW the button page
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


/**
 * Route to HANDLE the button click
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
            // Login failed
            return redirect()->route('dashboard')
                ->with('error', 'SSH Login Failed. Check IP, username, and password.');
        }

        // --- 3. RUN THE COMMAND ---
        // This command runs your script and returns *immediately*.
        // It runs the script in the background on the Pi so your website doesn't hang.
        // The ' > /tmp/script.log 2>&1 &' part logs output/errors to a file and runs in the background.
        $non_blocking_command = $command . ' > /tmp/script.log 2>&1 &';
        
        $ssh->exec($non_blocking_command);

        // Success! Redirect back with a success message
        return redirect()->route('dashboard')
            ->with('status', 'Command sent to Pi successfully!');

    } catch (\Exception $e) {
        // Handle connection errors (e.g., Pi is offline)
        return redirect()->route('dashboard')
            ->with('error', 'Error connecting to Pi: ' . $e->getMessage());
    }

})->name('run-script'); // <-- This name fixes the error