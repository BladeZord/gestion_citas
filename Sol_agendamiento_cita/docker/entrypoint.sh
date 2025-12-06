#!/bin/bash
set -e

# Esperar a que MySQL esté listo
echo "Esperando a MySQL..."
until nc -z mysql 3306; do
  echo "Esperando MySQL..."
  sleep 1
done

echo "MySQL está listo!"

# Esperar un poco más para asegurar que MySQL esté completamente listo
sleep 2

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force || true

# Ejecutar seeders (opcional)
echo "Ejecutando seeders..."
php artisan db:seed --force || true

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano
echo "Iniciando servidor..."
nginx -g "daemon off;"
