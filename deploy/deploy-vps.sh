#!/usr/bin/env bash
set -euo pipefail

# Deploy Venda Hoje para VPS (rsync + migrate)
# Uso local (Git Bash / WSL):
#   bash deploy/deploy-vps.sh
# Ou do PowerShell via SSH com arquivos jÃ¡ sincronizados.

APP_DIR="${APP_DIR:-/var/www/venda_hoje}"
REMOTE="${REMOTE:-root@fleetway.com.br}"
LOCAL_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo "==> Sync para ${REMOTE}:${APP_DIR}"
rsync -az --delete \
  --exclude '.git' \
  --exclude '.env' \
  --exclude 'storage/sessions/*' \
  --exclude 'storage/logs/*' \
  --exclude 'storage/cache/*' \
  --exclude 'vendor' \
  "${LOCAL_DIR}/" "${REMOTE}:${APP_DIR}/"

ssh "${REMOTE}" "cd ${APP_DIR} && composer install --no-dev --optimize-autoloader && \
  mkdir -p storage/cache storage/logs storage/sessions public/assets/uploads && \
  chown -R www-data:www-data storage public/assets/uploads && \
  php database/migrate.php && \
  grep -q 'APP_URL=https://vendahoje.com.br' .env || sed -i 's|^APP_URL=.*|APP_URL=https://vendahoje.com.br|' .env && \
  grep -q 'APP_DEBUG=false' .env || sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env && \
  grep -q 'APP_ENV=production' .env || sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env && \
  php -r \"require 'vendor/autoload.php'; echo 'Venda Hoje OK', PHP_EOL;\""

echo "Deploy finalizado: https://vendahoje.com.br"
