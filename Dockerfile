# ============================================================================
# events_users · Dockerfile
# PHP-FPM + Nginx en el mismo container
# ============================================================================

# ---------- Stage 1: dependencias PHP (cacheado) ----------
FROM php:8.4-alpine AS deps
WORKDIR /app

RUN apk add --no-cache \
    git curl unzip \
    libpng-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar solo los archivos de dependencias primero
# Si composer.json no cambia, este layer se cachea y no reinstala
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-autoloader \
    --no-scripts \
    --prefer-dist

# ---------- Stage 2: build de assets (cacheado) ----------
FROM node:20-alpine AS assets

WORKDIR /app

# Copiar solo package.json primero para cachear npm install
COPY package.json package-lock.json ./
RUN npm ci

# Copiar código y compilar con Vite
COPY . .
RUN npm run build

# ---------- Stage 3: runtime PHP-FPM + Nginx ----------
FROM php:8.4-alpine AS runtime
WORKDIR /var/www/html

# Instalar Nginx y librerías runtime (no de compilación)
RUN apk add --no-cache \
    nginx \
    libpng libzip oniguruma \
    && mkdir -p /run/nginx

# Copiar extensiones PHP compiladas en stage deps
COPY --from=deps /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=deps /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copiar vendor (dependencias PHP)
COPY --from=deps /app/vendor ./vendor

# Copiar assets compilados
COPY --from=assets /app/public/build ./public/build

# Copiar el resto del código
COPY . .

# Regenerar autoloader optimizado con el código completo
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev \
    && rm /usr/bin/composer

# Permisos de Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configuración de PHP-FPM
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configuración de Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configuración de OPcache
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]