# ---------- frontend build ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- composer deps ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

# ---------- runtime: nginx + php-fpm ----------
FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx supervisor && \
    docker-php-ext-install pdo_mysql bcmath opcache

WORKDIR /var/www/html
COPY . .
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY --from=vendor /app/vendor /var/www/html/vendor

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["sh", "-c", "export APP_KEY=\"${APP_KEY:-$(php -r 'echo \"base64:\".base64_encode(random_bytes(32));')}\" && php artisan migrate --force && supervisord -c /etc/supervisord.conf"]
