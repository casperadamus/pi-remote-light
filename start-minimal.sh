#!/bin/bash
set -e

echo "MINIMAL RAILWAY TEST"

# Absolute minimum setup
export APP_ENV=production
export APP_DEBUG=false

# Create basic directories
mkdir -p storage/logs
mkdir -p database
touch database/database.sqlite
chmod -R 777 storage database

# Generate key if needed
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
