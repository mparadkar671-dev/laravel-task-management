# Production Multi-Stage Dockerfile for Laravel 13 on PHP 8.4
FROM php:8.4-cli-alpine AS builder

WORKDIR /var/www/html

# Install build dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite-dev

RUN docker-php-ext-install pdo pdo_sqlite pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install production dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# -------------------------------------------------------------
# Runtime Stage
# -------------------------------------------------------------
FROM php:8.4-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    sqlite \
    sqlite-libs \
    curl

RUN docker-php-ext-install pdo pdo_sqlite pcntl bcmath

# Copy vendor and code from builder
COPY --from=builder /var/www/html /var/www/html

# Prepare storage, cache, and database directories
RUN mkdir -p /var/www/html/database /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/framework/cache /var/www/html/bootstrap/cache \
    && touch /var/www/html/database/database.sqlite \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

EXPOSE 8000

# Copy and set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
