# AWS Serverless Deployment with Laravel Vapor

## Installation

```bash
# Install Laravel Vapor
composer require laravel/vapor-cli laravel/vapor-core

# Install Vapor CLI globally
composer global require laravel/vapor-cli
```

## Step 1: Configure vapor.yml

```yaml
id: your-project-id
name: vosiz-ecommerce
environments:
    production:
        memory: 1024
        cli-memory: 512
        runtime: php-8.2
        build:
            - 'composer install --no-dev'
            - 'npm ci && npm run build'
        deploy:
            - 'php artisan migrate --force'
    staging:
        memory: 1024
        cli-memory: 512
        runtime: php-8.2
        build:
            - 'composer install'
            - 'npm ci && npm run build'
```

## Step 2: Deploy

```bash
# Login to Vapor
vapor login

# Deploy to staging
vapor deploy staging

# Deploy to production
vapor deploy production
```

## Limitations for Your App:
- MongoDB support is limited
- File uploads need S3 configuration
- Session management requires database
- Higher costs for complex applications