#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Criar .env se não existir
if [ ! -f .env ]; then
  cp .env.example .env 2>/dev/null || true
fi

# Gerar APP_KEY se não existir
if [ -f .env ] && ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force 2>/dev/null || true
fi

# Otimizar para produção (se não for local)
if [ "${APP_ENV:-local}" != "local" ]; then
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

exec "$@"
