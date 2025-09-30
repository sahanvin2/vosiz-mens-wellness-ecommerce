#!/bin/bash

echo "🐳 Setting up Vosiz Docker Environment..."

# Create necessary directories
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    cp .env.docker .env
    echo "✅ Environment file created"
fi

# Generate application key
php artisan key:generate

echo "🚀 Building and starting Docker containers..."

# Build and start containers
docker-compose up -d --build

echo "⏳ Waiting for services to be ready..."
sleep 30

echo "📦 Installing dependencies and optimizing..."

# Run optimizations inside container
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo "🎉 Vosiz Docker setup completed successfully!"
echo ""
echo "🌐 Your application is now running at:"
echo "   - Main App: http://localhost:8000"
echo "   - phpMyAdmin: http://localhost:8080"
echo "   - Mongo Express: http://localhost:8081"
echo ""
echo "📋 Database Credentials:"
echo "   MySQL:"
echo "     - Host: localhost:3307"
echo "     - Database: vosiz_db"
echo "     - Username: vosiz_user"
echo "     - Password: vosiz_password"
echo ""
echo "   MongoDB:"
echo "     - Host: localhost:27018"
echo "     - Database: vosiz_mongo"
echo "     - Username: admin"
echo "     - Password: adminpassword"
echo ""
echo "🔧 Useful Commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop services: docker-compose down"
echo "   - Restart: docker-compose restart"
echo "   - Shell access: docker-compose exec app bash"