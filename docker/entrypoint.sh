#!/usr/bin/env bash
set -e

envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force --no-interaction

php-fpm -D

nginx -g "daemon off;"
