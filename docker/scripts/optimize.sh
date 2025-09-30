#!/bin/bash

echo "🚀 Optimizing Vosiz for Production..."

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate optimized files
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Seed database if needed
# php artisan db:seed --force

# Generate app key if not exists
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Create storage link
php artisan storage:link

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Vosiz optimization completed!"