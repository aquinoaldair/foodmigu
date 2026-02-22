#!/bin/sh
set -e

# Ajustar permisos para Laravel (storage y bootstrap/cache)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

exec "$@"
