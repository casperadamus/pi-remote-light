# Railway Deployment Checklist

## Before Deploying:

1. **Generate APP_KEY locally:**
   ```bash
   php artisan key:generate --show
   ```
   Copy the output (e.g., `base64:xxxxxxxxxxxxx...`)

2. **Push changes to GitHub:**
   ```bash
   git add .
   git commit -m "Fix Railway deployment configuration"
   git push origin main
   ```

## In Railway Dashboard:

### Required Environment Variables:
Go to your project → Variables → Add these:

```
APP_KEY=base64:xxxxxxxxxxxxx... (from step 1 above)
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app (get this after generating domain)
DB_CONNECTION=sqlite
SESSION_DRIVER=database
```

### Generate Domain:
1. Go to Settings → Networking
2. Click "Generate Domain"
3. Copy the URL and update `APP_URL` variable above

## Common Issues:

### "Application key not set"
- Make sure `APP_KEY` is set in Railway variables
- Must start with `base64:`

### "500 Server Error" or blank page
- Check Railway logs: Click on "Deployments" → Latest deployment → "View Logs"
- Look for errors about permissions, storage, or database

### Page still loading forever
- Check if the domain is generated correctly
- Look at the logs for errors
- Make sure the PORT variable is being used (Railway sets this automatically)

### Database errors
- SQLite works but data won't persist between deployments
- Consider using Railway's MySQL/PostgreSQL plugin for production

## To Check Logs:
1. Go to your Railway project
2. Click on your service
3. Click "Deployments"
4. Click on the latest deployment
5. Click "View Logs" or "View Build Logs"

## Alternative: Use Railway CLI
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link to your project
railway link

# Check logs
railway logs

# Set variables via CLI
railway variables set APP_KEY="base64:your-key-here"
```
