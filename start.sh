#!/bin/bash
set -e

echo "Setting up Laravel for Railway..."

# Set critical environment variables for Railway
export APP_ENV=production
export APP_DEBUG=false
export DB_CONNECTION=sqlite
export DB_DATABASE=/app/database/database.sqlite
export SESSION_DRIVER=database
export CACHE_STORE=database
export QUEUE_CONNECTION=database
export LOG_LEVEL=error

# Ensure storage directories exist and are writable
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database

# Create SQLite database if it doesn't exist
touch database/database.sqlite

# Set permissions
chmod -R 775 storage bootstrap/cache database

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating new APP_KEY..."
    php artisan key:generate --force
fi

# Debug: Show environment info
echo "PHP Version: $(php -v | head -n 1)"
echo "Laravel Version: $(php artisan --version)"
echo "Current directory: $(pwd)"
echo "Database file exists: $(ls -la database/ 2>/dev/null || echo 'database directory not found')"
echo "APP_KEY set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')"
echo "Environment: $APP_ENV"

# Clear any cached config (important for Railway)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Test database connection
echo "Testing database connection..."
php artisan migrate:status || echo "Migration status check failed"

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Test if routes are working
echo "Testing route cache..."
php artisan route:list || echo "Route list failed"

echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
