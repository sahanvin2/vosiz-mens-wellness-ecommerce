#!/bin/bash

# MongoDB SSL Fix Script for AWS Lightsail
# Run this script on your server to fix MongoDB SSL issues

echo "🔧 Fixing MongoDB SSL issues on AWS Lightsail..."

# Update system packages
echo "📦 Updating system packages..."
sudo apt-get update

# Install PHP MongoDB extension with SSL support
echo "🔧 Installing PHP MongoDB extension with SSL support..."
sudo apt-get install -y php-mongodb php-dev pkg-config libssl-dev libsasl2-dev

# Alternative: Install via PECL if the above doesn't work
echo "📦 Installing MongoDB extension via PECL..."
sudo pecl install mongodb

# Add MongoDB extension to PHP configuration
echo "⚙️ Adding MongoDB extension to PHP configuration..."
echo "extension=mongodb.so" | sudo tee -a /etc/php/8.3/cli/php.ini
echo "extension=mongodb.so" | sudo tee -a /etc/php/8.3/fpm/php.ini

# Install OpenSSL development libraries
echo "🔐 Installing OpenSSL development libraries..."
sudo apt-get install -y libssl-dev openssl

# Restart PHP-FPM and Nginx
echo "🔄 Restarting services..."
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx

# Verify MongoDB extension is loaded
echo "✅ Verifying MongoDB extension..."
php -m | grep mongodb

echo ""
echo "🎯 MongoDB SSL fix completed!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with the production configuration"
echo "2. Run: php artisan config:clear"
echo "3. Run: php artisan cache:clear"
echo "4. Test your application"
echo ""