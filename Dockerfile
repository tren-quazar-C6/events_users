# ---------- Stage 1: PHP deps ----------
FROM php:8.4-fpm-alpine AS php-deps
WORKDIR /app

RUN apk add --no-cache \
    git curl unzip libpng-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------- Stage 2: Node / Vite build ----------
FROM node:20-alpine AS node-build
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
COPY --from=php-deps /app/vendor ./vendor
RUN npm run build

# ---------- Stage 3: Runtime (Nginx + PHP-FPM) ----------
FROM php:8.4-fpm-alpine AS runtime
WORKDIR /var/www/html

RUN apk add --no-cache nginx libpng libzip oniguruma \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd

# Código + vendor + assets compilados
COPY --from=php-deps /app .
COPY --from=node-build /app/public/build ./public/build

# Config Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]