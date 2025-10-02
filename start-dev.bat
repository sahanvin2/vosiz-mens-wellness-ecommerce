@echo off
echo Starting Vosiz Men's Wellness E-commerce Application...
echo.

echo [1/4] Building CSS assets...
call npm run build
timeout /t 2 /nobreak >nul

echo [2/4] Starting Laravel Development Server...
start cmd /k "cd /d %~dp0 && php artisan serve --host=localhost --port=8000"
timeout /t 3 /nobreak >nul

echo [3/4] Starting Vite Development Server...
start cmd /k "cd /d %~dp0 && npm run dev"
timeout /t 3 /nobreak >nul

echo [4/4] Opening Application in Browser...
timeout /t 5 /nobreak >nul
start http://localhost:8000

echo.
echo ========================================
echo   VOSIZ APPLICATION STARTED!
echo ========================================
echo.
echo Laravel Server: http://localhost:8000
echo Vite Server:    http://localhost:5173
echo Style Check:    http://localhost:8000/style-check.html
echo.
echo Admin Login:
echo Email: admin@vosiz.com
echo Password: admin123
echo.
echo ========================================
echo   FEATURES READY:
echo ========================================
echo - Enhanced Vosiz styling with custom colors
echo - Gradient backgrounds and animations
echo - Responsive design with Tailwind CSS
echo - MongoDB product management
echo - Image upload system
echo - Admin dashboard
echo - Authentication with Laravel Jetstream
echo.
echo Press any key to close this window...
pause >nul