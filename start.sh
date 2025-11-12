#!/bin/bash
set -e

echo "Setting up Laravel for Railway..."

# Ensure storage directories exist and are writable
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database

# Create SQLite database if it doesn't exist
touch database/database.sqlite

# Set permissions
chmod -R 775 storage bootstrap/cache database

# Run migrations
php artisan migrate --force

echo "Starting Laravel server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
