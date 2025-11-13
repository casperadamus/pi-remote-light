# Deploy to Render.com - Simple Guide

## Step 1: Push to GitHub
```bash
git add .
git commit -m "Add Render deployment configuration"
git push origin main
```

## Step 2: Create Render Account
1. Go to https://render.com
2. Sign up with your GitHub account (it's FREE)

## Step 3: Deploy
1. Click **"New +"** → **"Web Service"**
2. Connect your GitHub and select `pi-remote-light` repository
3. Render will auto-detect the configuration from `render.yaml`

## Step 4: Set Environment Variables
In the Render dashboard, add these environment variables:

**REQUIRED:**
- `APP_KEY` = `base64:EnPxedJVAHAXM/Wp3GaborUdy5N7nylq7sh66w3G4nw=`
- `APP_URL` = (Render will give you a URL like `https://pi-remote-light.onrender.com`)

That's it! Render handles everything else automatically.

## After Deployment:
1. Render will give you a URL like: `https://pi-remote-light.onrender.com`
2. Update the `APP_URL` environment variable with this URL
3. The service will auto-restart

## Free Tier Notes:
- ✅ Always free (unlike Railway which can crash)
- ✅ Automatic HTTPS
- ✅ Auto-deploys when you push to GitHub
- ⚠️ Sleeps after 15 min of inactivity (first load takes ~30 seconds)
- ⚠️ 750 hours/month free (plenty for a light switch)

## Troubleshooting:
Check logs in Render dashboard → Your service → Logs tab

The app should work perfectly on Render!
