# Tailscale Setup (Easiest & Most Secure)

## Access your Pi from anywhere without port forwarding

### Step 1: Install Tailscale on your Raspberry Pi
```bash
curl -fsSL https://tailscale.com/install.sh | sh
sudo tailscale up
```

### Step 2: Install Tailscale on your phone/computer
- Download from tailscale.com
- Sign in with same account

### Step 3: Run Laravel on the Pi
```bash
cd ~/pi-remote-light
php artisan serve --host=0.0.0.0 --port=8000
```

### Step 4: Access from anywhere!
Your Pi will have a tailscale IP like `100.x.x.x`

Visit: `http://100.x.x.x:8000` from ANY device with Tailscale installed

**Benefits:**
- ✅ Free forever
- ✅ Secure (encrypted VPN)
- ✅ No port forwarding needed
- ✅ Works from anywhere
- ✅ Share access with friends easily
- ✅ No public exposure

**To share with others:**
They just need to install Tailscale and you can share your Pi with them in the Tailscale admin panel.
