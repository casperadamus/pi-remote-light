#!/bin/bash
set -e

echo "=== DIGITALOCEAN DEPLOYMENT ==="

# Set environment
export APP_ENV=production
export APP_DEBUG=false
export SESSION_DRIVER=array
export CACHE_STORE=array

# Create directories
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Generate key if needed
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Clear caches
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "Starting server on port ${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
