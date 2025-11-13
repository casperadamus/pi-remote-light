<?php

use Illuminate\Support\Facades\Route;

/**
 * Ultra-simple test route
 */
Route::get('/test', function () {
    return 'Laravel is working! Time: ' . date('Y-m-d H:i:s');
});

/**
 * Minimal button page - WORKING VERSION
 */
Route::get('/', function () {
    return '<!DOCTYPE html>
<html>
<head>
    <title>Pi Light Control</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: #f5f5f5; 
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 0 auto;
        }
        .btn { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 20px 40px; 
            font-size: 18px; 
            border-radius: 5px; 
            cursor: pointer;
            margin: 20px 0;
        }
        .btn:hover { background: #0056b3; }
        .btn:disabled { background: #ccc; }
        .message { 
            margin: 20px 0; 
            padding: 15px; 
            border-radius: 5px; 
            display: none;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #b6d4d9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Pi Remote Light</h1>
        <p>Control your Raspberry Pi light switch</p>
        
        <div id="message" class="message"></div>
        
        <button id="controlBtn" class="btn" onclick="controlLight()">
            Turn On/Off Light
        </button>
        
        <p><small>Status: <span id="status">Ready</span></small></p>
    </div>
    
    <script>
        function controlLight() {
            const btn = document.getElementById("controlBtn");
            const msg = document.getElementById("message");
            const status = document.getElementById("status");
            
            // Show loading
            btn.disabled = true;
            btn.textContent = "Connecting...";
            status.textContent = "Sending command...";
            msg.style.display = "none";
            
            // Call the control endpoint
            fetch("/control")
                .then(response => response.json())
                .then(data => {
                    msg.textContent = data.message;
                    msg.className = "message " + (data.success ? "success" : "error");
                    msg.style.display = "block";
                    status.textContent = data.success ? "Success!" : "Failed";
                })
                .catch(error => {
                    msg.textContent = "Network error: " + error.message;
                    msg.className = "message error";
                    msg.style.display = "block";
                    status.textContent = "Connection failed";
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = "Turn On/Off Light";
                    setTimeout(() => {
                        status.textContent = "Ready";
                    }, 3000);
                });
        }
    </script>
</body>
</html>';
});

/**
 * Simple control endpoint - TEST VERSION
 */
Route::get('/control', function () {
    // For now, just return success to test if the basic app works
    return response()->json([
        'success' => true,
        'message' => '✅ App is working! Railway deployment successful.',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

/**
 * Pi control endpoint - REAL VERSION (will add SSH later)
 */
Route::get('/pi-control', function () {
    // TODO: Add SSH logic here once basic app is working
    return response()->json([
        'success' => false,
        'message' => 'Pi control not implemented yet - but the app works!'
    ]);
});
