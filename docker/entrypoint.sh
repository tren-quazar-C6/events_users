#!/bin/sh
set -e

echo "→ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

echo "→ Iniciando PHP-FPM..."
php-fpm -D

echo "→ Iniciando Nginx..."
exec nginx -g "daemon off;"