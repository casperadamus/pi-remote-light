<?php

use Illuminate\Support\Facades\Route;

/**
 * Ultra-simple test route
 */
Route::get('/test', function () {
    return 'Laravel is working! Time: ' . date('Y-m-d H:i:s');
});

/**
 * Minimal button page
 */
Route::get('/', function () {
    return '<!DOCTYPE html>
<html>
<head>
    <title>Pi Light Control</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .btn { background: #007bff; color: white; border: none; padding: 20px 40px; font-size: 18px; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .message { margin: 20px 0; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>🔌 Pi Remote Light</h1>
    <div id="message"></div>
    <button class="btn" onclick="controlLight()">Turn On/Off Light</button>
    
    <script>
        function controlLight() {
            const msg = document.getElementById("message");
            msg.innerHTML = "Connecting...";
            
            fetch("/control")
                .then(response => response.json())
                .then(data => {
                    msg.innerHTML = data.message;
                    msg.className = "message " + (data.success ? "success" : "error");
                })
                .catch(error => {
                    msg.innerHTML = "Error: " + error.message;
                    msg.className = "message error";
                });
        }
    </script>
</body>
</html>';
});

/**
 * Simple control endpoint
 */
Route::get('/control', function () {
    try {
        // For now, just return success to test if the app works
        return response()->json([
            'success' => true,
            'message' => 'App is working! (Pi connection not tested yet)'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
});
