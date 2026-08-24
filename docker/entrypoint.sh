#!/bin/sh
set -e

# The sqlite file lives on a volume outside /app: a volume mounted over /app/database would
# shadow migrations/, factories/ and seeders/, and migrate would find nothing to run.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/data/database.sqlite}"
    mkdir -p "$(dirname "$DB_PATH")"
    [ -f "$DB_PATH" ] || touch "$DB_PATH"
    chown -R www-data:www-data "$(dirname "$DB_PATH")"
fi

# Only the web service migrates. Two containers racing on `migrate` cannot be resolved with
# --isolated here, because its lock lives in the cache table the first migration creates.
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec docker-php-entrypoint "$@"
