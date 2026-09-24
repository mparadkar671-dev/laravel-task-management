#!/bin/sh
set -e

# Ensure .env exists
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Ensure storage and database directories exist with write permissions
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run database migrations and seed baseline roles
php artisan migrate --force --seed

# Optimize route, view, and config caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP built-in web server on the assigned port ($PORT from cloud provider, default 8000)
PORT="${PORT:-8000}"
echo "TaskFlow Enterprise initialized successfully! Serving on port $PORT..."

exec php artisan serve --host=0.0.0.0 --port="$PORT"
