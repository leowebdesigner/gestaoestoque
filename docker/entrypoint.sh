#!/usr/bin/env bash
set -euo pipefail

if [ ! -f /var/www/html/artisan ]; then
  echo "Laravel not found. Please bootstrap the project with: make init"
  exit 1
fi

if [ ! -d /var/www/html/vendor ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

if ! grep -q "^APP_KEY=" /var/www/html/.env; then
  php /var/www/html/artisan key:generate --force
fi

exec "$@"
