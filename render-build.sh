#!/usr/bin/env bash
# Render.com build script

echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Setting up storage directories..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p database

echo "Creating SQLite database..."
touch database/database.sqlite

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache database

echo "Build complete!"
