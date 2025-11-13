#!/bin/bash
set -e

echo "=== RAILWAY LARAVEL SETUP ==="

# Critical environment setup
export APP_ENV=production
export APP_DEBUG=false
export LOG_LEVEL=error
export DB_CONNECTION=sqlite
export DB_DATABASE=/app/database/database.sqlite
export SESSION_DRIVER=database
export CACHE_STORE=database
export QUEUE_CONNECTION=database

# Create directories with proper permissions
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database

# Create SQLite database
touch database/database.sqlite
chmod 666 database/database.sqlite

# Set broad permissions
chmod -R 777 storage bootstrap/cache database

# Generate app key if missing (critical!)
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY missing - generating new one..."
    php artisan key:generate --force --no-interaction
    echo "✅ APP_KEY generated"
else
    echo "✅ APP_KEY already set"
fi

echo "=== LARAVEL INFO ==="
echo "PHP: $(php -v | head -1)"
echo "Laravel: $(php artisan --version)"
echo "Directory: $(pwd)"
echo "Database: $(ls -la database/database.sqlite 2>/dev/null || echo 'Not found')"

# Simple config clear
php artisan config:clear --quiet || echo "Config clear failed"
php artisan cache:clear --quiet || echo "Cache clear failed"

# Clear caches
echo "=== CLEARING CACHES ==="
php artisan config:clear || echo "Config clear failed"
php artisan cache:clear || echo "Cache clear failed"
php artisan view:clear || echo "View clear failed"

# Run migrations
echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force || echo "⚠️  Migrations failed but continuing..."

echo "=== STARTING SERVER ==="
echo "🚀 Starting Laravel on port ${PORT:-8000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
