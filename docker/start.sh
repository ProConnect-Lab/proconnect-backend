#!/usr/bin/env sh
set -e

php artisan config:cache
php artisan route:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

php artisan app:ensure-admin || true

php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
