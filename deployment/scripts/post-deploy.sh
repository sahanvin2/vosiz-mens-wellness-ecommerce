#!/bin/bash

# Post-deployment script for Vosiz Laravel application
# Run this after successful AWS deployment to complete setup

set -e

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# Check if we're in Elastic Beanstalk environment
check_environment() {
    if [ -f "/opt/elasticbeanstalk/deployment/env" ]; then
        print_status "Running in Elastic Beanstalk environment"
        source /opt/elasticbeanstalk/deployment/env
    else
        print_status "Running in local environment"
    fi
}

# Database setup and migrations
setup_database() {
    print_status "Setting up database..."
    
    # Wait for database connection
    max_attempts=30
    attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if php artisan migrate:status &>/dev/null; then
            print_success "Database connection established"
            break
        fi
        
        print_status "Waiting for database connection... (attempt $attempt/$max_attempts)"
        sleep 10
        ((attempt++))
    done
    
    if [ $attempt -gt $max_attempts ]; then
        print_error "Could not establish database connection"
        exit 1
    fi
    
    # Run migrations
    print_status "Running database migrations..."
    php artisan migrate --force
    
    if [ $? -eq 0 ]; then
        print_success "Database migrations completed"
    else
        print_error "Database migrations failed"
        exit 1
    fi
}

# Seed database with initial data
seed_database() {
    print_status "Seeding database with initial data..."
    
    # Check if users exist
    USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
    
    if [ "$USER_COUNT" -eq 0 ]; then
        print_status "Creating initial admin user..."
        php artisan db:seed --class=UserSeeder --force
        
        # Create MongoDB products if none exist
        PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\MongoDBProduct::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
        
        if [ "$PRODUCT_COUNT" -eq 0 ]; then
            print_status "Seeding MongoDB with products..."
            php artisan db:seed --class=MongoDBProductSeeder --force
        fi
        
        print_success "Database seeding completed"
    else
        print_warning "Users already exist, skipping seeding"
    fi
}

# Configure file storage
setup_storage() {
    print_status "Setting up file storage..."
    
    # Create storage link
    if [ ! -L "public/storage" ]; then
        php artisan storage:link
        print_success "Storage link created"
    fi
    
    # Set proper permissions
    chmod -R 755 storage/
    chmod -R 755 bootstrap/cache/
    
    # Test S3 connection if configured
    if [ "$FILESYSTEM_DISK" = "s3" ]; then
        print_status "Testing S3 connection..."
        php artisan tinker --execute="
            try {
                Storage::disk('s3')->put('test.txt', 'test content');
                Storage::disk('s3')->delete('test.txt');
                echo 'S3 connection successful';
            } catch (Exception \$e) {
                echo 'S3 connection failed: ' . \$e->getMessage();
            }
        " 2>/dev/null
    fi
    
    print_success "Storage setup completed"
}

# Configure caching
setup_cache() {
    print_status "Setting up caching..."
    
    # Clear all caches
    php artisan cache:clear 2>/dev/null || true
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    
    # Cache configurations for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Test Redis connection if configured
    if [ "$CACHE_DRIVER" = "redis" ]; then
        print_status "Testing Redis connection..."
        php artisan tinker --execute="
            try {
                Cache::put('test_key', 'test_value', 60);
                \$value = Cache::get('test_key');
                Cache::forget('test_key');
                echo 'Redis connection successful';
            } catch (Exception \$e) {
                echo 'Redis connection failed: ' . \$e->getMessage();
            }
        " 2>/dev/null
    fi
    
    print_success "Caching setup completed"
}

# Setup queue workers
setup_queue() {
    print_status "Setting up queue system..."
    
    if [ "$QUEUE_CONNECTION" != "sync" ]; then
        # Test queue connection
        php artisan queue:work --stop-when-empty &
        QUEUE_PID=$!
        sleep 5
        
        if kill -0 $QUEUE_PID 2>/dev/null; then
            kill $QUEUE_PID
            print_success "Queue system is working"
        else
            print_warning "Queue system might have issues"
        fi
    fi
}

# Health checks
run_health_checks() {
    print_status "Running health checks..."
    
    # Test application routes
    ROUTES_COUNT=$(php artisan route:list --json | jq length 2>/dev/null || echo "0")
    print_status "Loaded routes: $ROUTES_COUNT"
    
    # Test MongoDB connection
    print_status "Testing MongoDB connection..."
    php artisan tinker --execute="
        try {
            \$count = App\Models\MongoDBProduct::count();
            echo 'MongoDB connected successfully. Products: ' . \$count;
        } catch (Exception \$e) {
            echo 'MongoDB connection failed: ' . \$e->getMessage();
        }
    " 2>/dev/null
    
    # Test MySQL connection
    print_status "Testing MySQL connection..."
    php artisan tinker --execute="
        try {
            \$count = App\Models\User::count();
            echo 'MySQL connected successfully. Users: ' . \$count;
        } catch (Exception \$e) {
            echo 'MySQL connection failed: ' . \$e->getMessage();
        }
    " 2>/dev/null
    
    print_success "Health checks completed"
}

# Create application health endpoint
create_health_endpoint() {
    print_status "Creating health check endpoint..."
    
    cat > public/health.php << 'EOF'
<?php
// Simple health check endpoint
header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'checks' => []
];

// Check if Laravel is responding
try {
    require_once __DIR__ . '/../bootstrap/app.php';
    $app = $app ?? null;
    $health['checks']['laravel'] = 'ok';
} catch (Exception $e) {
    $health['checks']['laravel'] = 'error: ' . $e->getMessage();
    $health['status'] = 'unhealthy';
}

// Check storage directory
if (is_writable(__DIR__ . '/../storage')) {
    $health['checks']['storage'] = 'ok';
} else {
    $health['checks']['storage'] = 'error: not writable';
    $health['status'] = 'unhealthy';
}

// Return status
http_response_code($health['status'] === 'healthy' ? 200 : 503);
echo json_encode($health, JSON_PRETTY_PRINT);
EOF

    print_success "Health endpoint created at /health.php"
}

# Display deployment summary
display_summary() {
    print_success "🎉 Post-deployment setup completed!"
    
    print_status "Application Summary:"
    echo "  📱 Application: Vosiz Men's Wellness Ecommerce"
    echo "  🌍 Environment: $(php artisan env 2>/dev/null || echo 'production')"
    echo "  🔗 Health Check: $(php artisan route:list | grep health | awk '{print $4}' || echo '/health.php')"
    
    print_status "Database Status:"
    USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
    PRODUCT_COUNT=$(php artisan tinker --execute="echo App\Models\MongoDBProduct::count();" 2>/dev/null | grep -o '[0-9]*' | tail -1)
    echo "  👥 Users: $USER_COUNT"
    echo "  🛍️ Products: $PRODUCT_COUNT"
    
    print_status "Next Steps:"
    echo "  1. Access your application via the provided URL"
    echo "  2. Test user registration and login"
    echo "  3. Verify product catalog functionality"
    echo "  4. Test order placement process"
    echo "  5. Configure domain name and SSL certificate"
    echo "  6. Set up monitoring and alerts"
    
    print_warning "Important:"
    echo "  - Monitor application logs regularly"
    echo "  - Set up automated backups"
    echo "  - Configure error tracking (Sentry, Bugsnag, etc.)"
    echo "  - Review security settings"
}

# Main execution
main() {
    echo "🚀 Starting post-deployment setup for Vosiz..."
    
    check_environment
    setup_database
    seed_database
    setup_storage
    setup_cache
    setup_queue
    create_health_endpoint
    run_health_checks
    display_summary
    
    print_success "✅ Post-deployment setup completed successfully!"
}

# Run main function
main "$@"