# ---------- Stage 1: BUILD ----------
FROM php:8.4-alpine AS build
WORKDIR /app

RUN apk add --no-cache git curl unzip nodejs npm libpng-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --no-autoloader --prefer-dist \
    && npm ci

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && npm run build

# ---------- Stage 2: RUNTIME ----------
FROM php:8.4-alpine AS runtime
WORKDIR /var/www/html

RUN apk add --no-cache libpng libzip oniguruma

# Copiar extensiones compiladas desde build
COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY --from=build /app .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]