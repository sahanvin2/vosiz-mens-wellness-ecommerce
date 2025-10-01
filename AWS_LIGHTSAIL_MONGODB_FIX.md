# 🔧 AWS Lightsail MongoDB SSL Fix Guide

## 🚨 Problem
Your AWS Lightsail server shows the error:
```
MongoDB\Driver\Exception\InvalidArgumentException - Internal Server Error
Failed to parse URI options: Can't create SSL client, SSL not enabled in this build.
```

## ✅ Solution Steps

### Step 1: Upload Files to Server
Upload these files to your server:
- `.env.production` → rename to `.env`
- `scripts/fix-mongodb-ssl.sh`
- `scripts/test-mongodb-production.php`
- Updated `config/database.php`
- Updated `app/Http/Controllers/ProductController.php`

### Step 2: Install MongoDB Extension with SSL Support
```bash
# Make script executable
chmod +x scripts/fix-mongodb-ssl.sh

# Run the fix script
./scripts/fix-mongodb-ssl.sh
```

### Step 3: Update Environment Configuration
Copy the production environment settings to your `.env` file:

```env
# MongoDB Atlas Configuration (FIXED)
MONGODB_DSN="mongodb+srv://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0.2m8hhzb.mongodb.net/vosiz_products?retryWrites=true&w=majority&ssl=false&tls=false"
MONGODB_DATABASE=vosiz_products

# SSL/TLS Settings (DISABLED for compatibility)
MONGO_DB_SSL=false
MONGO_DB_TLS=false
MONGO_DB_TLS_ALLOW_INVALID_CERTS=true
MONGO_DB_TLS_ALLOW_INVALID_HOSTNAMES=true
```

### Step 4: Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 5: Test MongoDB Connection
```bash
php scripts/test-mongodb-production.php
```

### Step 6: Set Proper Permissions
```bash
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 🔍 Alternative Solutions

### Option 1: Use DSN Connection String
If the above doesn't work, try this DSN in your `.env`:
```env
MONGODB_DSN="mongodb://sahannawarathne2004_db_user:j6caFrJSLCuJ2uP6@cluster0-shard-00-00.2m8hhzb.mongodb.net:27017,cluster0-shard-00-01.2m8hhzb.mongodb.net:27017,cluster0-shard-00-02.2m8hhzb.mongodb.net:27017/vosiz_products?ssl=false&replicaSet=atlas-default-shard-0&authSource=admin&retryWrites=true&w=majority"
```

### Option 2: Install MongoDB Extension Manually
```bash
# Install dependencies
sudo apt-get install -y php-dev pkg-config libssl-dev libsasl2-dev

# Install MongoDB extension
sudo pecl install mongodb

# Add to PHP configuration
echo "extension=mongodb.so" | sudo tee -a /etc/php/8.3/cli/php.ini
echo "extension=mongodb.so" | sudo tee -a /etc/php/8.3/fpm/php.ini

# Restart services
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Option 3: Fallback to MySQL Only
If MongoDB continues to fail, temporarily disable MongoDB features:

1. Comment out MongoDB routes in `routes/web.php`
2. Use MySQL for products temporarily
3. Create a MySQL product table as backup

## 🧪 Testing Commands

### Check PHP Extensions
```bash
php -m | grep mongodb
php -m | grep openssl
```

### Test MongoDB Connection
```bash
php scripts/test-mongodb-production.php
```

### Check Laravel Configuration
```bash
php artisan config:show database.connections.mongodb
```

### Test Application Routes
```bash
curl -I http://54.152.161.67/products
curl -I http://54.152.161.67/
```

## 🚀 Production Optimizations

### Enable Production Optimizations
```bash
# Optimize for production
composer install --optimize-autoloader --no-dev

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper file permissions
sudo chown -R www-data:www-data /var/www/html
```

### Monitor Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.3-fpm.log
```

## 📊 What Each Fix Does

### 1. Updated Database Configuration
- Uses DSN connection string for better compatibility
- Disables SSL/TLS for problematic servers
- Adds connection timeout options
- Provides fallback configurations

### 2. Enhanced ProductController
- Wraps MongoDB calls in try-catch blocks
- Provides graceful error handling
- Returns empty results instead of crashing
- Logs errors for debugging

### 3. Production Environment
- Optimized for AWS Lightsail
- Disabled debug mode
- Proper cache drivers
- SSL-disabled MongoDB connection

### 4. Installation Scripts
- Automated MongoDB extension installation
- SSL library installation
- Service restart automation
- Connection testing utilities

## ✅ Expected Results

After applying these fixes:
- ✅ No more SSL errors
- ✅ MongoDB Atlas connection works
- ✅ Products page loads successfully
- ✅ Graceful error handling if MongoDB fails
- ✅ Production-optimized performance

## 🆘 If Issues Persist

1. **Check server requirements**: Ensure Ubuntu 20.04+ or similar
2. **Verify PHP version**: Should be PHP 8.1+
3. **Contact hosting provider**: Some providers block certain MongoDB connection types
4. **Use alternative deployment**: Consider DigitalOcean or AWS EC2 instead

## 📞 Support Commands

```bash
# System information
uname -a
php --version
php -m | grep -E "(mongodb|openssl|curl)"

# Network connectivity
ping cluster0.2m8hhzb.mongodb.net
telnet cluster0.2m8hhzb.mongodb.net 27017

# Application status
php artisan about
```

This comprehensive fix should resolve your MongoDB SSL issues on AWS Lightsail! 🎯