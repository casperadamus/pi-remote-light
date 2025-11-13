#!/bin/bash

# Pi Setup Script - Run this on your Raspberry Pi
# This script sets up the HTTP server for remote light control

echo "🔌 Pi Remote Light Setup"
echo "========================="

# Check if Python 3 is installed
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is not installed. Please install it first:"
    echo "   sudo apt update && sudo apt install python3"
    exit 1
fi

# Check if lighton.py exists
if [ ! -f "lighton.py" ]; then
    echo "⚠️  lighton.py not found in current directory"
    echo "   Make sure your light control script is in the same folder"
    echo "   Current directory: $(pwd)"
    ls -la *.py 2>/dev/null || echo "   No Python files found"
    echo ""
fi

# Check if pi-server.py exists
if [ ! -f "pi-server.py" ]; then
    echo "❌ pi-server.py not found. Please copy it from your project repo."
    exit 1
fi

echo "✅ Setup looks good!"
echo ""
echo "Starting Pi HTTP server..."
echo "  - Server will run on port 3000"
echo "  - Accessible at: http://100.94.110.127:3000"
echo "  - Press Ctrl+C to stop"
echo ""

# Make the server script executable
chmod +x pi-server.py

# Start the server
python3 pi-server.py
