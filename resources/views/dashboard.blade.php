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

        @if (session('status'))
            <div class="message status">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('run-script') }}" method="POST" style="margin-top: 2rem;">
            @csrf
            <button type="submit" class="btn">
                Turn On/Off
            </button>
        </form>
    </div>
</body>
</html>