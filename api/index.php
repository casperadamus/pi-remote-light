<?php

// Set basic environment for Vercel
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');
putenv('SESSION_DRIVER=array');
putenv('CACHE_STORE=array');
putenv('APP_KEY=base64:9s59lxqmZoucucRUK5sQeKppy9lx7JQMbjQ4eISK1r8=');

// Simple routing for Vercel serverless
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle different routes
switch ($uri) {
    case '/':
        echo getMainPage();
        break;
    case '/test':
        echo 'Laravel is working on Vercel! Time: ' . date('Y-m-d H:i:s');
        break;
    case '/control':
    case '/pi-control':
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => '✅ Vercel deployment working! (Pi control coming soon)',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
}

function getMainPage() {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Pi Remote Light</h1>
        <p>Control your Raspberry Pi light switch</p>
        <p><small>🚀 Running on Vercel!</small></p>
        
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
            
            btn.disabled = true;
            btn.textContent = "Connecting...";
            status.textContent = "Sending command...";
            msg.style.display = "none";
            
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
}
