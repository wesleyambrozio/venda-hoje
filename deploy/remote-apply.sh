#!/usr/bin/env bash
set -euo pipefail
APP=/var/www/venda_hoje
cp "$APP/.env" /tmp/venda_hoje.env.bak
tar -xzf /tmp/venda_hoje_deploy.tgz -C "$APP"
cp /tmp/venda_hoje.env.bak "$APP/.env"
cd "$APP"
composer install --no-dev --optimize-autoloader
mkdir -p storage/cache storage/logs storage/sessions public/assets/uploads
chown -R www-data:www-data storage public/assets/uploads
sed -i 's|^APP_URL=.*|APP_URL=https://vendahoje.com.br|' .env
sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env
sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env
php database/migrate.php
ls -la public/assets/rq public/assets/js/rq-boot.js public/assets/css/theme.css
php -r 'require "vendor/autoload.php"; echo "OK\n";'
grep -E 'APP_URL|APP_DEBUG|APP_ENV' .env
