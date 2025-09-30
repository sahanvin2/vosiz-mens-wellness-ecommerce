# Vosiz Laravel Server Starter
Write-Host "Starting Vosiz Laravel Development Server..." -ForegroundColor Green
Write-Host "Server will be available at: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Cyan
Write-Host ""

Set-Location "c:\xampp\htdocs\SSP\Lara"

# Clear caches first
Write-Host "Clearing caches..." -ForegroundColor Blue
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null  
php artisan view:clear | Out-Null

# Start server
Write-Host "Server starting..." -ForegroundColor Green
php artisan serve --host=127.0.0.1 --port=8000