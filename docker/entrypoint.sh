#!/bin/sh
set -e

cd /var/www/html

# Storage symlink
php artisan storage:link --quiet 2>/dev/null || true

# Database migrations (--force voor production)
php artisan migrate --force

# Cache voor snelheid
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Rechten na cache
chown -R www-data:www-data storage bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisord.conf
