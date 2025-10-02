# 🚀 VOSIZ E-COMMERCE DEPLOYMENT GUIDE

## 📋 **DEPLOYMENT OVERVIEW**

This guide will help you deploy your VOSIZ Men's Wellness E-commerce platform to AWS Lightsail with professional testing and monitoring.

---

## 🎯 **PRE-DEPLOYMENT CHECKLIST**

### ✅ **Code Quality Verification**
- [x] Unit Tests: 12/12 passing
- [x] Feature Tests: Core features working
- [x] Professional testing framework implemented
- [x] Code style enforcement configured
- [x] Static analysis setup complete

### ✅ **Files Ready for Deployment**
- [x] Professional testing suite
- [x] MongoDB integration
- [x] Product management system
- [x] User authentication
- [x] Admin dashboard
- [x] Responsive design

---

## 🔧 **LIGHTSAIL DEPLOYMENT STEPS**

### **Step 1: Create Lightsail Instance**

1. **Go to AWS Lightsail Console**
   ```
   https://lightsail.aws.amazon.com/
   ```

2. **Create Instance**
   - Choose: `Linux/Unix`
   - Select: `OS Only` → `Ubuntu 22.04 LTS`
   - Choose plan: `$10/month` (recommended for production)
   - Instance name: `vosiz-ecommerce-prod`

3. **Configure Networking**
   - Open ports: `22 (SSH)`, `80 (HTTP)`, `443 (HTTPS)`
   - Create static IP address

### **Step 2: Initial Server Setup**

```bash
# Connect via SSH
ssh ubuntu@YOUR_STATIC_IP

# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2 php8.2-fpm php8.2-mysql \
    php8.2-xml php8.2-mbstring php8.2-zip php8.2-curl php8.2-gd \
    git curl unzip nodejs npm composer

# Install MongoDB tools
wget -qO - https://www.mongodb.org/static/pgp/server-7.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/7.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-7.0.list
sudo apt update
sudo apt install -y mongodb-database-tools
```

### **Step 3: Configure Database**

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE vosiz_ecommerce;
CREATE USER 'vosiz_user'@'localhost' IDENTIFIED BY 'SECURE_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON vosiz_ecommerce.* TO 'vosiz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### **Step 4: Deploy Application**

```bash
# Navigate to web directory
cd /var/www

# Clone your repository
sudo git clone https://github.com/YOUR_USERNAME/vosiz-mens-wellness-ecommerce.git
sudo mv vosiz-mens-wellness-ecommerce vosiz

# Set permissions
sudo chown -R www-data:www-data /var/www/vosiz
sudo chmod -R 755 /var/www/vosiz
sudo chmod -R 775 /var/www/vosiz/storage /var/www/vosiz/bootstrap/cache

# Install dependencies
cd /var/www/vosiz
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm install
sudo -u www-data npm run build
```

### **Step 5: Configure Environment**

```bash
# Copy environment file
sudo -u www-data cp .env.example .env

# Edit environment configuration
sudo nano .env
```

**Configure these key values in .env:**
```bash
APP_NAME="VOSIZ Men's Wellness"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vosiz_ecommerce
DB_USERNAME=vosiz_user
DB_PASSWORD=SECURE_PASSWORD_HERE

MONGODB_URI="mongodb+srv://username:password@cluster.mongodb.net/database"
MONGODB_DB=vosiz_products

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

```bash
# Generate application key
sudo -u www-data php artisan key:generate

# Run migrations
sudo -u www-data php artisan migrate --force

# Clear and cache configuration
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### **Step 6: Configure Nginx**

```bash
# Create Nginx configuration
sudo nano /etc/nginx/sites-available/vosiz
```

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/vosiz/public;

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
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

### **Step 7: SSL Setup with Let's Encrypt**

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

---

## 🔄 **CONTINUOUS DEPLOYMENT SETUP**

### **GitHub Actions Workflow**

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Lightsail

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ubuntu
        key: ${{ secrets.PRIVATE_KEY }}
        script: |
          cd /var/www/vosiz
          sudo -u www-data git pull origin main
          sudo -u www-data composer install --optimize-autoloader --no-dev
          sudo -u www-data npm install
          sudo -u www-data npm run build
          sudo -u www-data php artisan migrate --force
          sudo -u www-data php artisan config:cache
          sudo -u www-data php artisan route:cache
          sudo -u www-data php artisan view:cache
          sudo systemctl restart nginx
```

---

## 📊 **MONITORING & MAINTENANCE**

### **Health Check Script**

Create `health-check.sh`:

```bash
#!/bin/bash
# VOSIZ Health Check Script

echo "🏥 VOSIZ System Health Check"
echo "=========================="

# Check if Nginx is running
if systemctl is-active --quiet nginx; then
    echo "✅ Nginx: Running"
else
    echo "❌ Nginx: Not running"
fi

# Check if PHP-FPM is running
if systemctl is-active --quiet php8.2-fpm; then
    echo "✅ PHP-FPM: Running"
else
    echo "❌ PHP-FPM: Not running"
fi

# Check if MySQL is running
if systemctl is-active --quiet mysql; then
    echo "✅ MySQL: Running"
else
    echo "❌ MySQL: Not running"
fi

# Check disk space
DISK_USAGE=$(df / | grep -vE '^Filesystem|tmpfs|cdrom' | awk '{ print $5 }' | sed 's/%//g')
if [ $DISK_USAGE -lt 80 ]; then
    echo "✅ Disk Space: ${DISK_USAGE}% used"
else
    echo "⚠️ Disk Space: ${DISK_USAGE}% used (Warning: >80%)"
fi

# Check memory usage
MEMORY_USAGE=$(free | grep Mem | awk '{printf("%.1f"), $3/$2 * 100.0}')
echo "📊 Memory Usage: ${MEMORY_USAGE}%"

# Check if site is responding
if curl -s --head http://localhost | head -n 1 | grep -q 200; then
    echo "✅ Website: Responding"
else
    echo "❌ Website: Not responding"
fi

echo ""
echo "Health check completed at $(date)"
```

### **Automated Backup Script**

Create `backup.sh`:

```bash
#!/bin/bash
# VOSIZ Backup Script

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/ubuntu/backups"
mkdir -p $BACKUP_DIR

echo "🔄 Starting VOSIZ Backup - $DATE"

# Backup database
mysqldump -u vosiz_user -p vosiz_ecommerce > $BACKUP_DIR/vosiz_db_$DATE.sql

# Backup application files
tar -czf $BACKUP_DIR/vosiz_app_$DATE.tar.gz -C /var/www vosiz

# Keep only last 7 days of backups
find $BACKUP_DIR -name "vosiz_*" -mtime +7 -delete

echo "✅ Backup completed - $DATE"
```

---

## 🎯 **POST-DEPLOYMENT TESTING**

### **Automated Testing on Server**

```bash
# Run professional tests on production
cd /var/www/vosiz

# Run unit tests
sudo -u www-data php vendor/bin/phpunit --testsuite=Unit

# Check code quality
sudo -u www-data php vendor/bin/phpstan analyse app --level=5

# Verify site functionality
curl -s -o /dev/null -w "%{http_code}" http://localhost
```

---

## 🔧 **TROUBLESHOOTING**

### **Common Issues & Solutions**

1. **Permission Issues**
   ```bash
   sudo chown -R www-data:www-data /var/www/vosiz
   sudo chmod -R 755 /var/www/vosiz
   sudo chmod -R 775 /var/www/vosiz/storage /var/www/vosiz/bootstrap/cache
   ```

2. **Composer Memory Issues**
   ```bash
   php -d memory_limit=-1 /usr/local/bin/composer install
   ```

3. **Nginx 502 Error**
   ```bash
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart nginx
   ```

4. **Database Connection Issues**
   ```bash
   # Check MySQL status
   sudo systemctl status mysql
   
   # Test connection
   mysql -u vosiz_user -p vosiz_ecommerce
   ```

---

## 📋 **DEPLOYMENT CHECKLIST**

- [ ] AWS Lightsail instance created
- [ ] Domain pointed to static IP
- [ ] Server packages installed
- [ ] Database configured
- [ ] Application deployed
- [ ] Environment configured
- [ ] Nginx configured
- [ ] SSL certificate installed
- [ ] Automated deployment setup
- [ ] Monitoring configured
- [ ] Backup system setup
- [ ] Professional tests passing

---

## 🎉 **CONGRATULATIONS!**

Your VOSIZ Men's Wellness E-commerce platform is now professionally deployed with:

✅ **Professional Testing Framework**
✅ **Automated Quality Assurance**
✅ **Secure Production Environment**
✅ **Continuous Deployment Pipeline**
✅ **Monitoring & Backup Systems**
✅ **SSL Security**

**Your platform is ready for customers! 🚀**