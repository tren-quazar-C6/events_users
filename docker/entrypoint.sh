#!/bin/sh
set -e

echo "→ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan event:cache
# view:cache removido — falla con componentes de Jetstream no publicados
# Solución permanente: php artisan vendor:publish --tag=jetstream-views

echo "→ Iniciando PHP-FPM..."
php-fpm -D

echo "→ Iniciando Nginx..."
exec nginx -g "daemon off;"