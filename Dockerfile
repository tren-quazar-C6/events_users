# ============================================================================
# events_users · Dockerfile
# PHP-FPM + Nginx en el mismo container (Alpine)
# ============================================================================

# ---------- Stage 1: dependencias PHP ----------
FROM php:8.4-alpine AS deps
WORKDIR /app

RUN apk add --no-cache \
    git curl unzip \
    libpng-dev libzip-dev oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd opcache iconv \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

# ---------- Stage 2: assets ----------
FROM node:20-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ---------- Stage 3: runtime ----------
FROM php:8.4-alpine AS runtime
WORKDIR /var/www/html

# Instalar PHP-FPM + Nginx + libs runtime
RUN apk add --no-cache \
    nginx \
    php84-fpm \
    libpng libzip oniguruma \
    && mkdir -p /run/nginx /run/php

# Copiar extensiones PHP compiladas
COPY --from=deps /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=deps /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copiar vendor
COPY --from=deps /app/vendor ./vendor

# Copiar assets compilados
COPY --from=assets /app/public/build ./public/build

# Copiar código
COPY . .

# Autoloader optimizado
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev \
    && rm /usr/bin/composer

# Permisos Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /etc/php84/php-fpm.d/www.conf
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000
ENTRYPOINT ["/entrypoint.sh"]