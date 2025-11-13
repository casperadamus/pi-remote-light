#!/bin/bash
set -e

echo "=== SIMPLE RAILWAY SETUP ==="

# Basic environment setup
export APP_ENV=production
export APP_DEBUG=false
export LOG_LEVEL=error

# Create directories
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database

# Create SQLite database
touch database/database.sqlite
chmod 666 database/database.sqlite

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
echo "Database: $(ls -la database/database.sqlite 2>/dev/null || echo 'Not found')"

# Simple config clear
php artisan config:clear --quiet || echo "Config clear failed"
php artisan cache:clear --quiet || echo "Cache clear failed"

# Try migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force || echo "Migrations failed but continuing..."

echo "=== STARTING SERVER ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
