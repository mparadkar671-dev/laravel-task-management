#!/bin/sh
set -e

# Ensure SQLite database exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

# Generate app key if empty
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations and seed baseline roles
php artisan migrate --force --seed

# Cache configurations for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Determine port (Render, Koyeb, and Railway set $PORT automatically)
PORT="${PORT:-8000}"
echo "TaskFlow Enterprise starting on port $PORT..."

exec php artisan serve --host=0.0.0.0 --port="$PORT"
