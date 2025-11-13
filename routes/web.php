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
 * Root route - HTML button directly in route (no view file needed)
 */
Route::get('/', function () {
    return '<!DOCTYPE html>
<html>
<head>
    <title>Pi Remote Light</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            background: #f0f0f0; 
        }
        .container { 
            text-align: center; 
            background: white; 
            padding: 2rem; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .btn { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 15px 30px; 
            font-size: 18px; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .btn:hover { background: #0056b3; }
        .btn:disabled { background: #ccc; cursor: not-allowed; }
        .message { 
            margin: 15px 0; 
            padding: 10px; 
            border-radius: 5px; 
            display: none; 
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Pi Remote Light</h1>
        <p>Control your Raspberry Pi light switch</p>
        
        <div id="message" class="message"></div>
        
        <button id="controlBtn" class="btn" onclick="toggleLight()">
            Turn On/Off Light
        </button>
        
        <p><small>Status: <span id="status">Ready</span></small></p>
    </div>

    <script>
        async function toggleLight() {
            const btn = document.getElementById("controlBtn");
            const msg = document.getElementById("message");
            const status = document.getElementById("status");
            
            // Disable button and show loading
            btn.disabled = true;
            btn.textContent = "Sending command...";
            status.textContent = "Connecting to Pi...";
            msg.style.display = "none";
            
            try {
                const response = await fetch("/run-script", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "' . csrf_token() . '"
                    }
                });
                
                const data = await response.json();
                
                // Show result
                msg.textContent = data.message;
                msg.className = "message " + (data.success ? "success" : "error");
                msg.style.display = "block";
                
                status.textContent = data.success ? "Command sent!" : "Error occurred";
                
            } catch (error) {
                msg.textContent = "Network error: " + error.message;
                msg.className = "message error";
                msg.style.display = "block";
                status.textContent = "Connection failed";
            }
            
            // Re-enable button
            btn.disabled = false;
            btn.textContent = "Turn On/Off Light";
            
            // Reset status after 3 seconds
            setTimeout(() => {
                status.textContent = "Ready";
            }, 3000);
        }
    </script>
</body>
</html>';
});

/**
 * Dashboard route - same as root
 */
Route::get('/dashboard', function () {
    return redirect('/');
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