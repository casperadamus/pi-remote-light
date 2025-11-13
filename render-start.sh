#!/usr/bin/env bash
# Render.com start script

echo "Starting Laravel application..."

# Clear any cached configs
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate --force

# Cache configurations for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the application
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
