<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pi Control</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: grid;
            place-items: center;
            min-height: 90vh;
            background-color: #f4f7f6;
        }
        .container {
            text-align: center;
            background: #ffffff;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 20px 40px;
            font-size: 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 1rem;
            margin-top: 1.5rem;
            border-radius: 5px;
            font-size: 1.1rem;
        }
        .status {
            background: #e0fde0;
            border: 1px solid #a0e0a0;
            color: #006400;
        }
        .error {
            background: #fde0e0;
            border: 1px solid #e0a0a0;
            color: #a00000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pi Remote Control</h1>

        <!-- Message display area -->
        <div id="message" class="message" style="display: none; margin-top: 1.5rem;"></div>

        <form id="piForm" style="margin-top: 2rem;">
            <button type="submit" class="btn" id="piButton">
                Turn On/Off
            </button>
        </form>

        <script>
            document.getElementById('piForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const button = document.getElementById('piButton');
                const messageDiv = document.getElementById('message');
                
                // Disable button and show loading
                button.disabled = true;
                button.textContent = 'Sending...';
                messageDiv.style.display = 'none';
                
                try {
                    const response = await fetch('{{ route("run-script") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    
                    const data = await response.json();
                    
                    // Show message
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'message ' + (data.success ? 'status' : 'error');
                    messageDiv.style.display = 'block';
                    
                } catch (error) {
                    messageDiv.textContent = 'Network error: ' + error.message;
                    messageDiv.className = 'message error';
                    messageDiv.style.display = 'block';
                } finally {
                    // Re-enable button
                    button.disabled = false;
                    button.textContent = 'Turn On/Off';
                }
            });
        </script>
    </div>
</body>
</html>