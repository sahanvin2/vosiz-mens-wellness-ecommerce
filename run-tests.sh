#!/bin/bash

# VOSIZ E-commerce Automated Testing Script
# ===========================================

echo "🚀 VOSIZ E-commerce Automated Testing Suite"
echo "============================================="
echo ""

# Configuration
BASE_URL=${BASE_URL:-"http://localhost:8000"}
SELENIUM_URL=${SELENIUM_URL:-"http://localhost:4444"}
TEST_ENV=${TEST_ENV:-"testing"}

# Colors for output
RED='\\033[0;31m'
GREEN='\\033[0;32m'
YELLOW='\\033[1;33m'
BLUE='\\033[0;34m'
NC='\\033[0m' # No Color

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

# Check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed"
        exit 1
    fi
    print_success "PHP found: $(php --version | head -n1)"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer is not installed"
        exit 1
    fi
    print_success "Composer found: $(composer --version)"
    
    # Check Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js is not installed"
        exit 1
    fi
    print_success "Node.js found: $(node --version)"
    
    # Check if Laravel server is running
    if curl -f "$BASE_URL" > /dev/null 2>&1; then
        print_success "Laravel application is running at $BASE_URL"
    else
        print_warning "Laravel application is not running. Starting it..."
        php artisan serve --host=localhost --port=8000 &
        LARAVEL_PID=$!
        sleep 5
        
        if curl -f "$BASE_URL" > /dev/null 2>&1; then
            print_success "Laravel application started successfully"
        else
            print_error "Failed to start Laravel application"
            exit 1
        fi
    fi
    
    echo ""
}

# Install dependencies
install_dependencies() {
    print_status "Installing dependencies..."
    
    # Install PHP dependencies
    print_status "Installing PHP dependencies..."
    composer install --dev --no-interaction
    
    # Install additional testing packages if not present
    if ! composer show facebook/webdriver > /dev/null 2>&1; then
        print_status "Installing Selenium WebDriver..."
        composer require --dev facebook/webdriver
    fi
    
    if ! composer show phpunit/phpunit > /dev/null 2>&1; then
        print_status "Installing PHPUnit..."
        composer require --dev phpunit/phpunit
    fi
    
    # Install Node.js dependencies
    print_status "Installing Node.js dependencies..."
    npm ci
    npm run build
    
    print_success "Dependencies installed successfully"
    echo ""
}

# Setup test environment
setup_test_environment() {
    print_status "Setting up test environment..."
    
    # Create necessary directories
    mkdir -p tests/reports
    mkdir -p tests/screenshots
    mkdir -p storage/logs
    
    # Set permissions
    chmod -R 775 tests/reports tests/screenshots storage
    
    # Copy environment file if needed
    if [ ! -f .env.testing ]; then
        cp .env .env.testing
        print_status "Created .env.testing file"
    fi
    
    print_success "Test environment setup completed"
    echo ""
}

# Start Selenium server
start_selenium() {
    print_status "Checking Selenium server..."
    
    if curl -f "$SELENIUM_URL/status" > /dev/null 2>&1; then
        print_success "Selenium server is already running at $SELENIUM_URL"
    else
        print_status "Starting Selenium server with Docker..."
        
        # Check if Docker is available
        if command -v docker &> /dev/null; then
            # Stop any existing selenium container
            docker stop selenium-chrome 2>/dev/null || true
            docker rm selenium-chrome 2>/dev/null || true
            
            # Start new Selenium container
            docker run -d --name selenium-chrome \\
                -p 4444:4444 \\
                -v /dev/shm:/dev/shm \\
                selenium/standalone-chrome:latest
            
            # Wait for Selenium to be ready
            print_status "Waiting for Selenium to be ready..."
            for i in {1..30}; do
                if curl -f "$SELENIUM_URL/status" > /dev/null 2>&1; then
                    print_success "Selenium server started successfully"
                    break
                fi
                sleep 2
                if [ $i -eq 30 ]; then
                    print_error "Failed to start Selenium server"
                    exit 1
                fi
            done
        else
            print_warning "Docker not found. Please install Docker and Selenium manually"
            print_warning "Or download Selenium standalone server and run:"
            print_warning "java -jar selenium-server-standalone-*.jar"
        fi
    fi
    echo ""
}

# Run database setup
setup_database() {
    print_status "Setting up test database..."
    
    # Import test products
    if [ -f "import-real-products-simple.php" ]; then
        php import-real-products-simple.php
        print_success "Test products imported"
    else
        print_warning "Product import script not found"
    fi
    
    echo ""
}

# Run tests
run_tests() {
    print_status "Running automated tests..."
    echo ""
    
    # Unit Tests
    print_status "Running Unit Tests..."
    if vendor/bin/phpunit --testsuite=Unit --log-junit tests/reports/unit-tests.xml; then
        print_success "Unit tests passed"
    else
        print_warning "Some unit tests failed"
    fi
    echo ""
    
    # Feature Tests
    print_status "Running Feature Tests..."
    if vendor/bin/phpunit --testsuite=Feature --log-junit tests/reports/feature-tests.xml; then
        print_success "Feature tests passed"
    else
        print_warning "Some feature tests failed"
    fi
    echo ""
    
    # Functional Tests (Selenium)
    print_status "Running Functional Tests (Selenium)..."
    if vendor/bin/phpunit --testsuite=Functional --log-junit tests/reports/functional-tests.xml --testdox-html tests/reports/functional-report.html; then
        print_success "Functional tests passed"
    else
        print_warning "Some functional tests failed"
    fi
    echo ""
}

# Generate reports
generate_reports() {
    print_status "Generating test reports..."
    
    # Create HTML report
    cat > tests/reports/test-summary.html << EOF
<!DOCTYPE html>
<html>
<head>
    <title>VOSIZ E-commerce Test Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { background: #1f2937; color: white; padding: 20px; border-radius: 8px; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .success { background-color: #f0fdf4; border-color: #16a34a; }
        .warning { background-color: #fffbeb; border-color: #d97706; }
        .error { background-color: #fef2f2; border-color: #dc2626; }
        .timestamp { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 VOSIZ E-commerce Test Results</h1>
        <p class="timestamp">Generated on: $(date)</p>
    </div>
    
    <div class="section success">
        <h2>✅ Test Suite Completed</h2>
        <p>Automated testing has been completed for the VOSIZ Men's Wellness E-commerce platform.</p>
        <ul>
            <li>Unit Tests: $(ls tests/reports/unit-tests.xml >/dev/null 2>&1 && echo "✅ Completed" || echo "❌ Failed")</li>
            <li>Feature Tests: $(ls tests/reports/feature-tests.xml >/dev/null 2>&1 && echo "✅ Completed" || echo "❌ Failed")</li>
            <li>Functional Tests: $(ls tests/reports/functional-tests.xml >/dev/null 2>&1 && echo "✅ Completed" || echo "❌ Failed")</li>
        </ul>
    </div>
    
    <div class="section">
        <h2>📊 Test Coverage</h2>
        <p>Test coverage reports are available in the reports directory.</p>
    </div>
    
    <div class="section">
        <h2>📸 Screenshots</h2>
        <p>Screenshots from functional tests are available in: tests/screenshots/</p>
    </div>
</body>
</html>
EOF
    
    print_success "Test reports generated in tests/reports/"
    echo ""
}

# Cleanup function
cleanup() {
    print_status "Cleaning up..."
    
    # Stop Laravel server if we started it
    if [ ! -z "$LARAVEL_PID" ]; then
        kill $LARAVEL_PID 2>/dev/null || true
        print_status "Stopped Laravel development server"
    fi
    
    # Stop Selenium container
    if command -v docker &> /dev/null; then
        docker stop selenium-chrome 2>/dev/null || true
        docker rm selenium-chrome 2>/dev/null || true
        print_status "Stopped Selenium container"
    fi
    
    print_success "Cleanup completed"
}

# Main execution
main() {
    echo "Starting automated testing at $(date)"
    echo ""
    
    # Set trap for cleanup
    trap cleanup EXIT
    
    # Run all steps
    check_prerequisites
    install_dependencies
    setup_test_environment
    start_selenium
    setup_database
    run_tests
    generate_reports
    
    echo ""
    echo "🎉 Testing completed! Check the following:"
    echo "   📁 Test Reports: tests/reports/"
    echo "   📸 Screenshots: tests/screenshots/"
    echo "   🌐 Open tests/reports/test-summary.html in your browser"
    echo ""
}

# Run if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi