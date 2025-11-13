# Pi Remote Light - Setup Instructions

## 🚀 Your Netlify site is working! Now let's connect it to your Pi.

### Step 1: Copy files to your Raspberry Pi

```bash
# SSH to your Pi
ssh casperadamus@100.94.110.127

# Copy these files to your Pi (same folder as lighton.py):
# - pi-server.py
# - setup-pi.sh
```

### Step 2: Run the setup script on your Pi

```bash
# Make the setup script executable
chmod +x setup-pi.sh

# Run the setup
./setup-pi.sh
```

### Step 3: Test the connection

The server will start and show:
```
🔌 Pi Remote Light Setup
=========================
✅ Setup looks good!

Starting Pi HTTP server...
  - Server will run on port 3000
  - Accessible at: http://100.94.110.127:3000
  - Press Ctrl+C to stop

INFO:root:Starting Pi HTTP server on port 3000
INFO:root:Endpoints available:
INFO:root:  POST /toggle-light - Toggle the light
INFO:root:  GET  /status       - Server status
INFO:root:Press Ctrl+C to stop the server
```

### Step 4: Test your Netlify site

Go back to your Netlify site and click the "Turn On/Off Light" button. It should now:
- ✅ Connect to your Pi via HTTP
- ✅ Execute the `python3 lighton.py` script
- ✅ Show success message

## 🔧 Troubleshooting

### If the button shows connection errors:

1. **Check Pi is online:**
   ```bash
   ping 100.94.110.127
   ```

2. **Check HTTP server is running:**
   ```bash
   # On your Pi:
   curl http://localhost:3000/status
   ```

3. **Check firewall (if needed):**
   ```bash
   # On your Pi, if port 3000 is blocked:
   sudo ufw allow 3000
   ```

4. **Check the Pi server logs** - they'll show when requests come in

### Manual testing:

You can test the Pi server directly:
```bash
# Test from your computer:
curl -X POST http://100.94.110.127:3000/toggle-light \
  -H "Content-Type: application/json" \
  -d '{"action":"toggle","timestamp":"2025-11-13T06:00:00.000Z"}'
```

## 🎯 That's it!

Once the Pi HTTP server is running, your Netlify site will be able to control your Pi light remotely with instant response times!
