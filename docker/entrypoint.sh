#!/usr/bin/env bash
set -euo pipefail

if [ ! -f /var/www/html/artisan ]; then
  echo "Laravel not found. Please bootstrap the project with: make init"
  exit 1
fi

if [ ! -f /var/www/html/vendor/autoload.php ]; then
  git config --global --add safe.directory /var/www/html || true
  lock_dir="/var/www/html/.composer-install.lock"
  if mkdir "$lock_dir" 2>/dev/null; then
    trap 'rmdir "$lock_dir"' EXIT
    if [ ! -f /var/www/html/vendor/autoload.php ]; then
      composer install --no-interaction --prefer-dist
    fi
    rmdir "$lock_dir"
    trap - EXIT
  else
    echo "Waiting for composer install lock..."
    while [ -d "$lock_dir" ]; do
      sleep 2
    done
    if [ ! -f /var/www/html/vendor/autoload.php ]; then
      composer install --no-interaction --prefer-dist
    fi
  fi
fi

if [ ! -f /var/www/html/.env ]; then
  cp /var/www/html/.env.example /var/www/html/.env
fi

if ! grep -q "^APP_KEY=" /var/www/html/.env; then
  php /var/www/html/artisan key:generate --force
fi

exec "$@"
