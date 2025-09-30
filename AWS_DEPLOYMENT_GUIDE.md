# AWS EC2 Deployment Guide for Vosiz E-commerce

## Prerequisites
- AWS Account
- MongoDB Atlas account (free tier available)
- Domain name (optional)

## Step 1: Create MongoDB Atlas Database
1. Go to https://cloud.mongodb.com
2. Create free cluster
3. Create database user
4. Whitelist IP addresses (0.0.0.0/0 for development)
5. Get connection string

## Step 2: Launch EC2 Instance
1. AWS Console → EC2 → Launch Instance
2. Choose Ubuntu Server 22.04 LTS
3. Instance type: t3.small (or t3.micro for testing)
4. Configure security group:
   - SSH (22) - Your IP
   - HTTP (80) - 0.0.0.0/0
   - HTTPS (443) - 0.0.0.0/0
   - Custom (8000) - 0.0.0.0/0 (for Laravel serve)

## Step 3: Connect to EC2 and Install Dependencies

```bash
# Connect to EC2
ssh -i your-key.pem ubuntu@your-ec2-public-ip

# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-json -y

# Install MongoDB PHP extension
sudo apt install php8.2-dev php8.2-mongodb -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Git
sudo apt install git -y

# Install Nginx
sudo apt install nginx -y
```

## Step 4: Clone and Setup Application

```bash
# Clone your repository
cd /var/www
sudo git clone https://github.com/sahanvin2/vosiz-mens-wellness-ecommerce.git
sudo chown -R ubuntu:ubuntu vosiz-mens-wellness-ecommerce
cd vosiz-mens-wellness-ecommerce

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Create environment file
cp .env.example .env
```

## Step 5: Configure Environment Variables

Edit .env file with your settings:

```env
APP_NAME="Vosiz Men's Wellness"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vosiz_mysql
DB_USERNAME=vosiz_user
DB_PASSWORD=secure_password

MONGODB_DSN=mongodb+srv://username:password@cluster.mongodb.net/vosiz_products?retryWrites=true&w=majority

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

## Step 6: Setup MySQL for User Data

```bash
# Install MySQL
sudo apt install mysql-server -y

# Secure MySQL
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE vosiz_mysql;
CREATE USER 'vosiz_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON vosiz_mysql.* TO 'vosiz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 7: Run Laravel Setup

```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

## Step 8: Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/vosiz
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/vosiz-mens-wellness-ecommerce/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/vosiz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## Step 9: Setup SSL with Let's Encrypt

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## Step 10: Setup Process Manager (PM2 for Queue Workers)

```bash
# Install PM2
sudo npm install -g pm2

# Start queue worker
pm2 start "php artisan queue:work" --name vosiz-queue

# Save PM2 configuration
pm2 save
pm2 startup
```