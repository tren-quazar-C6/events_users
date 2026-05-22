#!/bin/sh
set -e

echo "→ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan event:cache

echo "→ Enlazando storage..."
php artisan storage:link --force

echo "→ Iniciando PHP-FPM..."
/usr/sbin/php-fpm84 -D -y /etc/php84/php-fpm.conf

echo "→ Iniciando Nginx..."
exec nginx -g "daemon off;"