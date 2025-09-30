@echo off
title Vosiz Laravel Server
cd /d "c:\xampp\htdocs\SSP\Lara"
echo Starting Vosiz Laravel Development Server...
echo Server will be available at: http://127.0.0.1:8000
echo Press Ctrl+C to stop the server
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause