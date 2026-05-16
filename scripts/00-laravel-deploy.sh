#!/usr/bin/env bash
set -e

echo "[deploy] Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --working-dir=/var/www/html

echo "[deploy] Clearing old cache..."
php artisan optimize:clear

echo "[deploy] Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[deploy] Running migrations..."
php artisan migrate --force

echo "[deploy] Seeding reference data..."
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=EnumValeurSeeder --force

echo "[deploy] Correction des entités fournisseur manquantes..."
php artisan fournisseurs:fix-entites --no-interaction
