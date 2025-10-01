@echo off
REM Vosiz AWS Deployment Wizard for Windows
REM This script guides you through deploying Vosiz to AWS

title Vosiz AWS Deployment Wizard

echo.
echo ============================================
echo    Vosiz Men's Wellness - AWS Deployment
echo ============================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo [ERROR] Laravel artisan file not found!
    echo Please run this script from your Laravel project root directory.
    pause
    exit /b 1
)

echo [INFO] Laravel project detected...
echo.

REM Display deployment options
echo Choose your deployment method:
echo.
echo 1. Elastic Beanstalk (Recommended)
echo 2. Docker with ECS
echo 3. Manual EC2 Setup
echo 4. View deployment guide only
echo.
set /p choice="Enter your choice (1-4): "

if "%choice%"=="1" goto elastic_beanstalk
if "%choice%"=="2" goto docker_ecs
if "%choice%"=="3" goto manual_ec2
if "%choice%"=="4" goto view_guide
goto invalid_choice

:elastic_beanstalk
echo.
echo ============================================
echo    Elastic Beanstalk Deployment
echo ============================================
echo.

REM Check prerequisites
echo [INFO] Checking prerequisites...

REM Check AWS CLI
aws --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] AWS CLI not found!
    echo Please install AWS CLI v2 from: https://aws.amazon.com/cli/
    pause
    exit /b 1
)
echo [SUCCESS] AWS CLI found

REM Check EB CLI
eb --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] EB CLI not found. Installing...
    pip install awsebcli
    if %errorlevel% neq 0 (
        echo [ERROR] Failed to install EB CLI
        echo Please install Python and pip first
        pause
        exit /b 1
    )
)
echo [SUCCESS] EB CLI ready

REM Check AWS credentials
aws sts get-caller-identity >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] AWS credentials not configured!
    echo Please run: aws configure
    pause
    exit /b 1
)
echo [SUCCESS] AWS credentials configured

echo.
echo [INFO] Preparing application for deployment...

REM Install production dependencies
echo [INFO] Installing production dependencies...
composer install --optimize-autoloader --no-dev --no-interaction
if %errorlevel% neq 0 (
    echo [ERROR] Failed to install dependencies
    pause
    exit /b 1
)

REM Build frontend assets
if exist "package.json" (
    echo [INFO] Building frontend assets...
    npm ci --only=production
    npm run build
)

REM Copy Elastic Beanstalk configuration
if exist "deployment\elastic-beanstalk\.ebextensions" (
    echo [INFO] Copying EB configuration...
    if not exist ".ebextensions" mkdir .ebextensions
    xcopy "deployment\elastic-beanstalk\.ebextensions\*" ".ebextensions\" /Y /Q
    echo [SUCCESS] EB configuration copied
)

REM Laravel optimization
echo [INFO] Optimizing Laravel application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [INFO] Initializing Elastic Beanstalk...

REM Initialize EB if not already done
if not exist ".elasticbeanstalk\config.yml" (
    eb init vosiz-app --region us-east-1 --platform "php-8.2"
    echo [SUCCESS] EB application initialized
) else (
    echo [INFO] EB application already initialized
)

echo.
echo Choose deployment option:
echo 1. Create new environment
echo 2. Deploy to existing environment
echo.
set /p deploy_choice="Enter your choice (1-2): "

if "%deploy_choice%"=="1" (
    echo [INFO] Creating new production environment...
    eb create vosiz-production --database.engine mysql --database.size db.t3.micro --database.username vosizadmin
) else (
    echo [INFO] Deploying to existing environment...
    eb deploy
)

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Deployment completed successfully!
    echo.
    echo Your application is now available at:
    eb status | findstr "CNAME"
    echo.
    echo Next steps:
    echo 1. Set environment variables in AWS EB Console
    echo 2. Configure MongoDB Atlas connection
    echo 3. Set up domain name and SSL certificate
) else (
    echo [ERROR] Deployment failed
    echo Check the logs for more information: eb logs
)

goto end

:docker_ecs
echo.
echo ============================================
echo    Docker ECS Deployment
echo ============================================
echo.
echo [INFO] Docker ECS deployment requires additional setup.
echo Please refer to the deployment guide for detailed instructions.
echo File: deployment\docker\README.md
goto end

:manual_ec2
echo.
echo ============================================
echo    Manual EC2 Setup
echo ============================================
echo.
echo [INFO] Manual EC2 setup requires AWS console configuration.
echo Please refer to the deployment guide for step-by-step instructions.
echo File: AWS_DEPLOYMENT_GUIDE.md
goto end

:view_guide
echo.
echo ============================================
echo    Deployment Guide
echo ============================================
echo.
echo [INFO] Opening deployment guides...
if exist "COMPREHENSIVE_AWS_DEPLOYMENT.md" (
    start "" "COMPREHENSIVE_AWS_DEPLOYMENT.md"
) else (
    echo Comprehensive deployment guide not found
)
if exist "AWS_DEPLOYMENT_GUIDE.md" (
    start "" "AWS_DEPLOYMENT_GUIDE.md"
) else (
    echo AWS deployment guide not found
)
goto end

:invalid_choice
echo [ERROR] Invalid choice. Please select 1-4.
goto end

:end
echo.
echo ============================================
echo    Deployment Complete
echo ============================================
echo.
echo Thank you for using the Vosiz deployment wizard!
echo.
echo For support and documentation:
echo - GitHub: https://github.com/sahanvin2/vosiz-mens-wellness-ecommerce
echo - Documentation: Check the deployment folder for guides
echo.
pause