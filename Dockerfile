# Production Dockerfile for Laravel 13 on PHP 8.4 (Debian Bookworm)
FROM php:8.4-cli-bookworm

WORKDIR /var/www/html

# Install the official mlocati PHP extension installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install system dependencies including SQLite development libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        zip \
        unzip \
        sqlite3 \
        libsqlite3-dev \
        pkg-config \
    && install-php-extensions \
        bcmath \
        pcntl \
        intl \
        zip \
        pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy application source code
COPY . .

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set directory permissions for SQLite, storage, and framework cache
RUN mkdir -p /var/www/html/database \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/framework/cache \
             /var/www/html/bootstrap/cache \
    && touch /var/www/html/database/database.sqlite \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 8000

# Copy entrypoint script and strip any Windows CRLF line endings
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
