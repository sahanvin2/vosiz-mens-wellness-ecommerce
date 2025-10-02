@echo off
REM VOSIZ E-commerce Automated Testing Script for Windows
REM =====================================================

echo 🚀 VOSIZ E-commerce Automated Testing Suite
echo =============================================
echo.

REM Configuration
set BASE_URL=http://localhost:8000
set SELENIUM_URL=http://localhost:4444
set TEST_ENV=testing

echo [INFO] Checking prerequisites...

REM Check PHP
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    pause
    exit /b 1
)
echo [SUCCESS] PHP found

REM Check Composer
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer is not installed or not in PATH
    pause
    exit /b 1
)
echo [SUCCESS] Composer found

REM Check Node.js
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is not installed or not in PATH
    pause
    exit /b 1
)
echo [SUCCESS] Node.js found

echo.
echo [INFO] Installing dependencies...

REM Install PHP dependencies
echo [INFO] Installing PHP dependencies...
composer install --dev --no-interaction
if %errorlevel% neq 0 (
    echo [ERROR] Failed to install PHP dependencies
    pause
    exit /b 1
)

REM Install Selenium WebDriver if not present
composer show facebook/webdriver >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Installing Selenium WebDriver...
    composer require --dev facebook/webdriver
)

REM Install PHPUnit if not present
composer show phpunit/phpunit >nul 2>&1
if %errorlevel% neq 0 (
    echo [INFO] Installing PHPUnit...
    composer require --dev phpunit/phpunit
)

REM Install Node.js dependencies
echo [INFO] Installing Node.js dependencies...
npm ci
npm run build

echo [SUCCESS] Dependencies installed successfully
echo.

echo [INFO] Setting up test environment...

REM Create necessary directories
if not exist "tests\\reports" mkdir tests\\reports
if not exist "tests\\screenshots" mkdir tests\\screenshots
if not exist "storage\\logs" mkdir storage\\logs

REM Copy environment file if needed
if not exist ".env.testing" (
    copy .env .env.testing
    echo [INFO] Created .env.testing file
)

echo [SUCCESS] Test environment setup completed
echo.

echo [INFO] Checking Laravel application...

REM Check if Laravel server is running
curl -f %BASE_URL% >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Laravel application is not running. Starting it...
    start /b php artisan serve --host=localhost --port=8000
    timeout /t 5 >nul
    
    curl -f %BASE_URL% >nul 2>&1
    if %errorlevel% neq 0 (
        echo [ERROR] Failed to start Laravel application
        pause
        exit /b 1
    )
    echo [SUCCESS] Laravel application started successfully
) else (
    echo [SUCCESS] Laravel application is running
)

echo.
echo [INFO] Setting up test database...

REM Import test products
if exist "import-real-products-simple.php" (
    php import-real-products-simple.php
    echo [SUCCESS] Test products imported
) else (
    echo [WARNING] Product import script not found
)

echo.
echo [INFO] Checking Selenium server...

REM Check if Selenium is running
curl -f %SELENIUM_URL%/status >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Selenium server is not running
    echo [INFO] Please start Selenium manually or use Docker:
    echo   docker run -d --name selenium-chrome -p 4444:4444 -v /dev/shm:/dev/shm selenium/standalone-chrome:latest
    echo.
    echo Press any key to continue with tests (Selenium tests will be skipped)...
    pause >nul
) else (
    echo [SUCCESS] Selenium server is running
)

echo.
echo [INFO] Running automated tests...
echo.

REM Unit Tests
echo [INFO] Running Unit Tests...
vendor\\bin\\phpunit --testsuite=Unit --log-junit tests\\reports\\unit-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Unit tests passed
) else (
    echo [WARNING] Some unit tests failed
)
echo.

REM Feature Tests
echo [INFO] Running Feature Tests...
vendor\\bin\\phpunit --testsuite=Feature --log-junit tests\\reports\\feature-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Feature tests passed
) else (
    echo [WARNING] Some feature tests failed
)
echo.

REM Functional Tests (if Selenium is available)
curl -f %SELENIUM_URL%/status >nul 2>&1
if %errorlevel% equ 0 (
    echo [INFO] Running Functional Tests (Selenium)...
    vendor\\bin\\phpunit --testsuite=Functional --log-junit tests\\reports\\functional-tests.xml --testdox-html tests\\reports\\functional-report.html
    if %errorlevel% equ 0 (
        echo [SUCCESS] Functional tests passed
    ) else (
        echo [WARNING] Some functional tests failed
    )
) else (
    echo [WARNING] Selenium not available, skipping functional tests
)

echo.
echo [INFO] Generating test reports...

REM Create HTML report
echo ^<!DOCTYPE html^> > tests\\reports\\test-summary.html
echo ^<html^> >> tests\\reports\\test-summary.html
echo ^<head^> >> tests\\reports\\test-summary.html
echo     ^<title^>VOSIZ E-commerce Test Results^</title^> >> tests\\reports\\test-summary.html
echo     ^<style^> >> tests\\reports\\test-summary.html
echo         body { font-family: Arial, sans-serif; margin: 40px; } >> tests\\reports\\test-summary.html
echo         .header { background: #1f2937; color: white; padding: 20px; border-radius: 8px; } >> tests\\reports\\test-summary.html
echo         .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; } >> tests\\reports\\test-summary.html
echo         .success { background-color: #f0fdf4; border-color: #16a34a; } >> tests\\reports\\test-summary.html
echo     ^</style^> >> tests\\reports\\test-summary.html
echo ^</head^> >> tests\\reports\\test-summary.html
echo ^<body^> >> tests\\reports\\test-summary.html
echo     ^<div class="header"^> >> tests\\reports\\test-summary.html
echo         ^<h1^>🚀 VOSIZ E-commerce Test Results^</h1^> >> tests\\reports\\test-summary.html
echo         ^<p^>Generated on: %date% %time%^</p^> >> tests\\reports\\test-summary.html
echo     ^</div^> >> tests\\reports\\test-summary.html
echo     ^<div class="section success"^> >> tests\\reports\\test-summary.html
echo         ^<h2^>✅ Test Suite Completed^</h2^> >> tests\\reports\\test-summary.html
echo         ^<p^>Automated testing has been completed for the VOSIZ Men's Wellness E-commerce platform.^</p^> >> tests\\reports\\test-summary.html
echo     ^</div^> >> tests\\reports\\test-summary.html
echo ^</body^> >> tests\\reports\\test-summary.html
echo ^</html^> >> tests\\reports\\test-summary.html

echo [SUCCESS] Test reports generated in tests\\reports\\
echo.

echo 🎉 Testing completed! Check the following:
echo    📁 Test Reports: tests\\reports\\
echo    📸 Screenshots: tests\\screenshots\\
echo    🌐 Open tests\\reports\\test-summary.html in your browser
echo.

pause