pipeline {
    agent any
    
    environment {
        // Application settings
        APP_ENV = 'testing'
        BASE_URL = 'http://localhost:8000'
        SELENIUM_DRIVER_URL = 'http://selenium:4444'
        
        // Database settings for testing
        DB_CONNECTION = 'sqlite'
        DB_DATABASE = ':memory:'
        
        // MongoDB settings (use test database)
        MONGODB_DSN = credentials('mongodb-test-dsn')
        
        // Composer and NPM cache
        COMPOSER_CACHE_DIR = '/var/cache/composer'
        NPM_CACHE_DIR = '/var/cache/npm'
    }
    
    stages {
        stage('Checkout') {
            steps {
                echo '📥 Checking out source code...'
                checkout scm
            }
        }
        
        stage('Install Dependencies') {
            parallel {
                stage('Composer Install') {
                    steps {
                        echo '📦 Installing PHP dependencies...'
                        script {
                            sh '''
                                composer install --no-dev --optimize-autoloader --no-interaction
                                composer require --dev phpunit/phpunit facebook/webdriver symfony/process
                            '''
                        }
                    }
                }
                
                stage('NPM Install') {
                    steps {
                        echo '📦 Installing Node.js dependencies...'
                        script {
                            sh '''
                                npm ci --cache ${NPM_CACHE_DIR}
                                npm run build
                            '''
                        }
                    }
                }
            }
        }
        
        stage('Environment Setup') {
            steps {
                echo '⚙️ Setting up test environment...'
                script {
                    sh '''
                        # Copy environment file
                        cp .env.example .env.testing
                        
                        # Generate application key
                        php artisan key:generate --env=testing
                        
                        # Create storage directories
                        mkdir -p storage/logs
                        mkdir -p tests/reports
                        mkdir -p tests/screenshots
                        
                        # Set permissions
                        chmod -R 775 storage bootstrap/cache tests/reports tests/screenshots
                    '''
                }
            }
        }
        
        stage('Start Services') {
            parallel {
                stage('Start Application') {
                    steps {
                        echo '🚀 Starting Laravel application...'
                        script {
                            sh '''
                                # Start Laravel development server in background
                                nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
                                echo $! > laravel.pid
                                
                                # Wait for server to start
                                sleep 10
                                
                                # Check if server is running
                                if curl -f http://localhost:8000 > /dev/null 2>&1; then
                                    echo "✅ Laravel server started successfully"
                                else
                                    echo "❌ Failed to start Laravel server"
                                    exit 1
                                fi
                            '''
                        }
                    }
                }
                
                stage('Start Selenium') {
                    steps {
                        echo '🔍 Starting Selenium server...'
                        script {
                            sh '''
                                # Start Selenium server using Docker
                                docker run -d --name selenium-chrome \\
                                    -p 4444:4444 \\
                                    -v /dev/shm:/dev/shm \\
                                    selenium/standalone-chrome:latest
                                
                                # Wait for Selenium to be ready
                                sleep 15
                                
                                # Check if Selenium is running
                                if curl -f http://localhost:4444/status > /dev/null 2>&1; then
                                    echo "✅ Selenium server started successfully"
                                else
                                    echo "❌ Failed to start Selenium server"
                                    exit 1
                                fi
                            '''
                        }
                    }
                }
            }
        }
        
        stage('Database Setup') {
            steps {
                echo '🗃️ Setting up test database...'
                script {
                    sh '''
                        # Run database migrations
                        php artisan migrate --env=testing --force
                        
                        # Seed database with test data
                        php artisan db:seed --env=testing --force
                        
                        # Import test products to MongoDB
                        php import-real-products-simple.php
                    '''
                }
            }
        }
        
        stage('Run Tests') {
            parallel {
                stage('Unit Tests') {
                    steps {
                        echo '🧪 Running unit tests...'
                        script {
                            sh '''
                                vendor/bin/phpunit --testsuite=Unit \\
                                    --log-junit tests/reports/unit-tests.xml \\
                                    --coverage-html tests/reports/unit-coverage \\
                                    --coverage-clover tests/reports/unit-coverage.xml
                            '''
                        }
                    }
                }
                
                stage('Feature Tests') {
                    steps {
                        echo '🧪 Running feature tests...'
                        script {
                            sh '''
                                vendor/bin/phpunit --testsuite=Feature \\
                                    --log-junit tests/reports/feature-tests.xml \\
                                    --coverage-html tests/reports/feature-coverage \\
                                    --coverage-clover tests/reports/feature-coverage.xml
                            '''
                        }
                    }
                }
                
                stage('Functional Tests') {
                    steps {
                        echo '🖥️ Running functional tests...'
                        script {
                            sh '''
                                vendor/bin/phpunit --testsuite=Functional \\
                                    --log-junit tests/reports/functional-tests.xml \\
                                    --testdox-html tests/reports/functional-report.html
                            '''
                        }
                    }
                }
            }
        }
        
        stage('Code Quality') {
            parallel {
                stage('PHP CodeSniffer') {
                    steps {
                        echo '🔍 Running PHP CodeSniffer...'
                        script {
                            sh '''
                                # Install PHPCS if not available
                                if ! command -v phpcs &> /dev/null; then
                                    composer global require squizlabs/php_codesniffer
                                fi
                                
                                # Run code style check
                                vendor/bin/phpcs --standard=PSR12 app/ --report=xml --report-file=tests/reports/phpcs.xml || true
                            '''
                        }
                    }
                }
                
                stage('Security Check') {
                    steps {
                        echo '🔒 Running security audit...'
                        script {
                            sh '''
                                # Check for security vulnerabilities
                                composer audit --format=json > tests/reports/security-audit.json || true
                                
                                # Check NPM packages
                                npm audit --json > tests/reports/npm-audit.json || true
                            '''
                        }
                    }
                }
            }
        }
        
        stage('Performance Tests') {
            steps {
                echo '⚡ Running performance tests...'
                script {
                    sh '''
                        # Simple load testing with curl
                        echo "Testing homepage load time..."
                        time curl -o /dev/null -s -w "%{time_total}\\n" http://localhost:8000
                        
                        echo "Testing products page load time..."
                        time curl -o /dev/null -s -w "%{time_total}\\n" http://localhost:8000/products
                        
                        # Optional: Use Apache Bench for more detailed testing
                        if command -v ab &> /dev/null; then
                            echo "Running Apache Bench tests..."
                            ab -n 100 -c 10 http://localhost:8000/ > tests/reports/ab-homepage.txt
                            ab -n 100 -c 10 http://localhost:8000/products > tests/reports/ab-products.txt
                        fi
                    '''
                }
            }
        }
    }
    
    post {
        always {
            echo '🧹 Cleaning up...'
            script {
                sh '''
                    # Stop Laravel server
                    if [ -f laravel.pid ]; then
                        kill $(cat laravel.pid) || true
                        rm laravel.pid
                    fi
                    
                    # Stop Selenium container
                    docker stop selenium-chrome || true
                    docker rm selenium-chrome || true
                    
                    # Clean up temporary files
                    rm -f .env.testing
                '''
            }
            
            // Archive test reports
            archiveArtifacts artifacts: 'tests/reports/**/*', allowEmptyArchive: true
            archiveArtifacts artifacts: 'tests/screenshots/**/*', allowEmptyArchive: true
            
            // Publish test results
            publishTestResults testResultsPattern: 'tests/reports/*-tests.xml'
            
            // Publish HTML reports
            publishHTML([
                allowMissing: false,
                alwaysLinkToLastBuild: true,
                keepAll: true,
                reportDir: 'tests/reports',
                reportFiles: '*.html',
                reportName: 'Test Reports'
            ])
        }
        
        success {
            echo '✅ All tests passed successfully!'
            // Send success notification
            slackSend(
                color: 'good',
                message: "✅ VOSIZ E-commerce Tests Passed - Build #${env.BUILD_NUMBER}"
            )
        }
        
        failure {
            echo '❌ Some tests failed!'
            // Send failure notification
            slackSend(
                color: 'danger',
                message: "❌ VOSIZ E-commerce Tests Failed - Build #${env.BUILD_NUMBER}\\nCheck: ${env.BUILD_URL}"
            )
        }
        
        unstable {
            echo '⚠️ Tests completed with warnings!'
            slackSend(
                color: 'warning',
                message: "⚠️ VOSIZ E-commerce Tests Unstable - Build #${env.BUILD_NUMBER}"
            )
        }
    }
}