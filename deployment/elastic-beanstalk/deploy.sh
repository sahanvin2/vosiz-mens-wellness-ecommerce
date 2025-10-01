#!/bin/bash

# Vosiz AWS Elastic Beanstalk Deployment Script
# This script prepares and deploys your Laravel application to AWS Elastic Beanstalk

echo "🚀 Starting Vosiz AWS Deployment..."

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from your Laravel project root."
    exit 1
fi

print_status "Verifying Laravel project structure..."

# Check for required files
REQUIRED_FILES=("composer.json" "package.json" ".env.example")
for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        print_error "Required file $file not found!"
        exit 1
    fi
done

print_success "Project structure verified!"

# Step 1: Environment Setup
print_status "Setting up deployment environment..."

# Create deployment directory if it doesn't exist
if [ ! -d "deployment" ]; then
    mkdir -p deployment/elastic-beanstalk/.ebextensions
    print_success "Created deployment directory structure"
fi

# Copy .ebextensions if they exist in deployment folder
if [ -d "deployment/elastic-beanstalk/.ebextensions" ]; then
    cp -r deployment/elastic-beanstalk/.ebextensions ./
    print_success "Copied Elastic Beanstalk configuration files"
fi

# Step 2: Dependencies and Build
print_status "Installing production dependencies..."

# Install PHP dependencies
composer install --optimize-autoloader --no-dev --no-interaction
if [ $? -eq 0 ]; then
    print_success "Composer dependencies installed"
else
    print_error "Failed to install Composer dependencies"
    exit 1
fi

# Install Node dependencies and build assets
if [ -f "package.json" ]; then
    print_status "Building frontend assets..."
    npm ci --only=production
    npm run build
    if [ $? -eq 0 ]; then
        print_success "Frontend assets built successfully"
    else
        print_warning "Frontend build failed, continuing without assets"
    fi
fi

# Step 3: Laravel Optimization
print_status "Optimizing Laravel application..."

# Clear all caches first
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Cache configurations for production
php artisan config:cache
php artisan route:cache  
php artisan view:cache

print_success "Laravel optimization completed"

# Step 4: AWS Configuration Check
print_status "Checking AWS configuration..."

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    print_error "AWS CLI not found. Please install AWS CLI v2"
    print_status "Download from: https://aws.amazon.com/cli/"
    exit 1
fi

# Check AWS credentials
aws sts get-caller-identity &> /dev/null
if [ $? -eq 0 ]; then
    print_success "AWS credentials configured"
else
    print_error "AWS credentials not configured. Run 'aws configure'"
    exit 1
fi

# Check if EB CLI is installed
if ! command -v eb &> /dev/null; then
    print_error "EB CLI not found. Installing..."
    pip install awsebcli
    if [ $? -eq 0 ]; then
        print_success "EB CLI installed"
    else
        print_error "Failed to install EB CLI"
        exit 1
    fi
fi

# Step 5: Elastic Beanstalk Initialization
print_status "Initializing Elastic Beanstalk application..."

# Check if already initialized
if [ ! -f ".elasticbeanstalk/config.yml" ]; then
    print_status "Initializing new EB application..."
    eb init vosiz-app --region us-east-1 --platform "php-8.2"
    print_success "EB application initialized"
else
    print_warning "EB application already initialized"
fi

# Step 6: Environment Creation or Deployment
print_status "Checking for existing environments..."

# List environments
ENVIRONMENTS=$(eb list 2>/dev/null | grep -v "Application versions" | grep -v "^$" | wc -l)

if [ "$ENVIRONMENTS" -eq 0 ]; then
    print_status "No environments found. Creating production environment..."
    
    # Create environment with database
    eb create vosiz-production \
        --database.engine mysql \
        --database.size db.t3.micro \
        --database.username vosizadmin \
        --envvars APP_ENV=production,APP_DEBUG=false
        
    if [ $? -eq 0 ]; then
        print_success "Environment created successfully!"
        print_status "Environment URL: $(eb status | grep 'CNAME' | awk '{print $2}')"
    else
        print_error "Failed to create environment"
        exit 1
    fi
else
    print_status "Deploying to existing environment..."
    eb deploy
    
    if [ $? -eq 0 ]; then
        print_success "Deployment completed successfully!"
    else
        print_error "Deployment failed"
        exit 1
    fi
fi

# Step 7: Post-deployment tasks
print_status "Running post-deployment tasks..."

# Set environment variables (this should be done manually in AWS console)
print_warning "Please set the following environment variables in AWS EB Console:"
echo "  - APP_KEY (generate with: php artisan key:generate --show)"
echo "  - DB_PASSWORD (from RDS creation)"
echo "  - MONGODB_DSN (from MongoDB Atlas)"
echo "  - AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY (for S3)"
echo "  - Any other custom environment variables"

# Step 8: Health check
print_status "Performing health check..."
sleep 30  # Wait for deployment to settle

eb health
if [ $? -eq 0 ]; then
    print_success "Health check passed!"
else
    print_warning "Health check issues detected. Check EB console for details."
fi

# Final status
print_success "🎉 Deployment process completed!"
print_status "Next steps:"
echo "  1. Configure environment variables in EB console"
echo "  2. Set up MongoDB Atlas connection"
echo "  3. Configure domain name and SSL certificate"
echo "  4. Run database migrations: eb ssh -c 'php artisan migrate'"
echo "  5. Seed database if needed: eb ssh -c 'php artisan db:seed'"

print_status "Access your application at: $(eb status | grep 'CNAME' | awk '{print $2}')"
print_status "Monitor logs with: eb logs"
print_status "Check application health: eb health"

echo "✅ Deployment script completed successfully!"