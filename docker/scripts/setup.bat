@echo off
echo 🐳 Setting up Vosiz Docker Environment...

REM Create necessary directories
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\framework\cache" mkdir storage\framework\cache
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\testing" mkdir storage\framework\testing
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\logs" mkdir storage\logs
if not exist "bootstrap\cache" mkdir bootstrap\cache

REM Copy environment file if it doesn't exist
if not exist ".env" (
    copy .env.docker .env
    echo ✅ Environment file created
)

echo 🚀 Building and starting Docker containers...

REM Build and start containers
docker-compose up -d --build

echo ⏳ Waiting for services to be ready...
timeout /t 30 /nobreak > nul

echo 📦 Installing dependencies and optimizing...

REM Run optimizations inside container
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo 🎉 Vosiz Docker setup completed successfully!
echo.
echo 🌐 Your application is now running at:
echo    - Main App: http://localhost:8000
echo    - phpMyAdmin: http://localhost:8080
echo    - Mongo Express: http://localhost:8081
echo.
echo 📋 Database Credentials:
echo    MySQL:
echo      - Host: localhost:3307
echo      - Database: vosiz_db
echo      - Username: vosiz_user
echo      - Password: vosiz_password
echo.
echo    MongoDB:
echo      - Host: localhost:27018
echo      - Database: vosiz_mongo
echo      - Username: admin
echo      - Password: adminpassword
echo.
echo 🔧 Useful Commands:
echo    - View logs: docker-compose logs -f
echo    - Stop services: docker-compose down
echo    - Restart: docker-compose restart
echo    - Shell access: docker-compose exec app bash

pause