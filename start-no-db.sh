#!/bin/bash
set -e

echo "=== NO DATABASE RAILWAY SETUP ==="

# Basic environment setup - NO DATABASE
export APP_ENV=production
export APP_DEBUG=false
export LOG_LEVEL=error
export SESSION_DRIVER=array
export CACHE_STORE=array
export QUEUE_CONNECTION=sync

# Create directories (but no database)
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "=== LARAVEL INFO ==="
echo "PHP: $(php -v | head -1)"
echo "Laravel: $(php artisan --version)"
echo "Directory: $(pwd)"
echo "Session Driver: array (no database)"
echo "Cache Store: array (no database)"

# Simple config clear (no database operations)
php artisan config:clear --quiet || echo "Config clear failed"
php artisan view:clear --quiet || echo "View clear failed"

# Skip migrations completely - no database needed!
echo "Skipping database migrations - using array drivers"

echo "=== STARTING SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
