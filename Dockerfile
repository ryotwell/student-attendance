# =========================================================
# 1. FRONTEND BUILD
# =========================================================
FROM node:22-bookworm-slim AS frontend

WORKDIR /app

# Enable Corepack untuk menggunakan pnpm
RUN corepack enable

# Copy dependency files terlebih dahulu agar Docker cache efektif
COPY package.json pnpm-lock.yaml pnpm-workspace.yaml ./

# Install dependency berdasarkan lockfile
RUN pnpm install --frozen-lockfile

# Copy source code
COPY . .

# Build Vite
RUN pnpm run build


# =========================================================
# 2. PHP COMPOSER DEPENDENCIES
# =========================================================
FROM php:8.4-cli-alpine AS vendor

WORKDIR /app

# Install dependencies yang dibutuhkan untuk PHP GD
RUN apk add --no-cache \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency files
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader


# =========================================================
# 3. RUNTIME
# Nginx + PHP-FPM + Supervisor
# =========================================================
FROM php:8.4-fpm-alpine

WORKDIR /var/www/html

# Install system packages + PHP extensions
RUN apk add --no-cache \
        nginx \
        supervisor \
        freetype \
        libjpeg-turbo \
        libpng \
    && apk add --no-cache --virtual .build-deps \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo_mysql \
        bcmath \
        opcache \
    && apk del .build-deps

# Copy Laravel application
COPY . .

# Copy frontend build
COPY --from=frontend /app/public/build /var/www/html/public/build

# Copy Composer dependencies
COPY --from=vendor /app/vendor /var/www/html/vendor

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisord.conf

# Laravel permissions
RUN chown -R www-data:www-data \
        storage \
        bootstrap/cache

# PHP production configuration
RUN { \
        echo "opcache.enable=1"; \
        echo "opcache.enable_cli=1"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.interned_strings_buffer=8"; \
        echo "opcache.max_accelerated_files=20000"; \
        echo "opcache.validate_timestamps=0"; \
        echo "opcache.revalidate_freq=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 80

# Start Laravel
CMD ["sh", "-c", "php artisan migrate --force && php artisan optimize && php artisan storage:link && supervisord -c /etc/supervisord.conf"]