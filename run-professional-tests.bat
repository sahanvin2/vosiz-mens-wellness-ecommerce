@echo off
REM VOSIZ Professional Testing Suite - Windows Version
REM Comprehensive automated testing with multiple analysis tools
REM Author: GitHub Copilot & Development Team
REM Version: 1.0.0

echo 🚀 VOSIZ PROFESSIONAL TESTING SUITE
echo ====================================
echo.

REM Test counters
set TOTAL_TESTS=0
set PASSED_TESTS=0
set FAILED_TESTS=0

REM Create test reports directory
if not exist "storage\tests\reports" mkdir "storage\tests\reports"
if not exist "storage\tests\screenshots" mkdir "storage\tests\screenshots"

echo [INFO] Starting Professional Testing Suite...
echo.

REM 1. STATIC CODE ANALYSIS
echo 📊 STATIC CODE ANALYSIS
echo ========================

echo [INFO] Running PHPStan (Static Analysis)...
php vendor\bin\phpstan analyse --memory-limit=1G > storage\tests\reports\phpstan-report.txt 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] PHPStan analysis completed
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] PHPStan found issues (check storage\tests\reports\phpstan-report.txt^)
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo [INFO] Running PHP CodeSniffer (Code Style)...
php vendor\bin\phpcs --report=full --report-file=storage\tests\reports\phpcs-report.txt
if %errorlevel% equ 0 (
    echo [SUCCESS] Code style validation passed
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] Code style issues found (check storage\tests\reports\phpcs-report.txt^)
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 2. UNIT TESTS
echo 🧪 UNIT TESTS
echo =============

echo [INFO] Running PHPUnit Unit Tests...
php vendor\bin\phpunit --testsuite=Unit --log-junit storage\tests\reports\unit-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Unit tests passed
    set /a PASSED_TESTS+=1
) else (
    echo [ERROR] Unit tests failed
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 3. FEATURE TESTS
echo 🎯 FEATURE TESTS
echo ================

echo [INFO] Running Laravel Feature Tests...
php vendor\bin\phpunit --testsuite=Feature --log-junit storage\tests\reports\feature-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Feature tests passed
    set /a PASSED_TESTS+=1
) else (
    echo [ERROR] Feature tests failed
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 4. FUNCTIONAL TESTS
echo 🌐 FUNCTIONAL TESTS
echo ===================

echo [INFO] Running Functional Tests...
php vendor\bin\phpunit --testsuite=Functional --log-junit storage\tests\reports\functional-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Functional tests passed
    set /a PASSED_TESTS+=1
) else (
    echo [ERROR] Functional tests failed
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 5. BROWSER TESTS (Laravel Dusk)
echo 🌍 BROWSER AUTOMATION TESTS
echo ============================

echo [INFO] Running Laravel Dusk Browser Tests...
php artisan dusk --log-junit storage\tests\reports\dusk-tests.xml
if %errorlevel% equ 0 (
    echo [SUCCESS] Browser tests passed
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] Browser tests had issues (check screenshots in storage\tests\screenshots\^)
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 6. SECURITY ANALYSIS
echo 🔒 SECURITY ANALYSIS
echo ====================

echo [INFO] Checking for security vulnerabilities...
composer audit > storage\tests\reports\security-audit.txt 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] No known security vulnerabilities found
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] Security vulnerabilities detected (check storage\tests\reports\security-audit.txt^)
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 7. PERFORMANCE ANALYSIS
echo ⚡ PERFORMANCE ANALYSIS
echo =======================

echo [INFO] Analyzing application performance...

REM Create performance test
echo ^<?php > storage\tests\performance-test.php
echo $startTime = microtime(true); >> storage\tests\performance-test.php
echo $startMemory = memory_get_usage(); >> storage\tests\performance-test.php
echo require_once 'vendor/autoload.php'; >> storage\tests\performance-test.php
echo $endTime = microtime(true); >> storage\tests\performance-test.php
echo $endMemory = memory_get_usage(); >> storage\tests\performance-test.php
echo $loadTime = ($endTime - $startTime) * 1000; >> storage\tests\performance-test.php
echo $memoryUsage = ($endMemory - $startMemory) / 1024 / 1024; >> storage\tests\performance-test.php
echo echo "Bootstrap Time: " . round($loadTime, 2) . "ms\n"; >> storage\tests\performance-test.php
echo echo "Memory Usage: " . round($memoryUsage, 2) . "MB\n"; >> storage\tests\performance-test.php
echo if ($loadTime ^> 500) { echo "WARNING: Bootstrap time exceeds 500ms\n"; exit(1); } >> storage\tests\performance-test.php
echo if ($memoryUsage ^> 50) { echo "WARNING: Memory usage exceeds 50MB\n"; exit(1); } >> storage\tests\performance-test.php
echo echo "Performance test passed\n"; >> storage\tests\performance-test.php

php storage\tests\performance-test.php > storage\tests\reports\performance-report.txt 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] Performance test passed
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] Performance issues detected (check storage\tests\reports\performance-report.txt^)
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM 8. CODE COVERAGE ANALYSIS
echo 📈 CODE COVERAGE ANALYSIS
echo =========================

echo [INFO] Generating code coverage report...
php vendor\bin\phpunit --coverage-html storage\tests\reports\coverage --coverage-clover storage\tests\reports\coverage.xml > storage\tests\reports\coverage-summary.txt 2>&1
if %errorlevel% equ 0 (
    echo [SUCCESS] Code coverage report generated
    set /a PASSED_TESTS+=1
) else (
    echo [WARNING] Code coverage generation had issues
    set /a FAILED_TESTS+=1
)
set /a TOTAL_TESTS+=1

echo.

REM FINAL SUMMARY
echo 📋 TESTING SUMMARY
echo ==================
echo.
echo Total Test Suites: %TOTAL_TESTS%
echo Passed: %PASSED_TESTS%
echo Failed/Warnings: %FAILED_TESTS%
echo.

if %FAILED_TESTS% equ 0 (
    echo [SUCCESS] 🎉 ALL TESTS PASSED! Professional quality achieved!
    echo.
    echo ✅ Static Analysis: Clean
    echo ✅ Code Style: Compliant
    echo ✅ Unit Tests: Passing
    echo ✅ Feature Tests: Passing
    echo ✅ Functional Tests: Passing
    echo ✅ Browser Tests: Passing
    echo ✅ Security: No vulnerabilities
    echo ✅ Performance: Within limits
    echo ✅ Code Coverage: Generated
    exit /b 0
) else (
    echo [WARNING] ⚠️  Some tests failed or have warnings. Check reports in storage\tests\reports\
    echo.
    echo 📁 Report Files:
    echo    - PHPStan: storage\tests\reports\phpstan-report.txt
    echo    - Code Style: storage\tests\reports\phpcs-report.txt
    echo    - Unit Tests: storage\tests\reports\unit-tests.xml
    echo    - Feature Tests: storage\tests\reports\feature-tests.xml
    echo    - Functional Tests: storage\tests\reports\functional-tests.xml
    echo    - Browser Tests: storage\tests\reports\dusk-tests.xml
    echo    - Security: storage\tests\reports\security-audit.txt
    echo    - Performance: storage\tests\reports\performance-report.txt
    echo    - Coverage: storage\tests\reports\coverage\
    echo.
    echo 📸 Screenshots: storage\tests\screenshots\
    exit /b 1
)