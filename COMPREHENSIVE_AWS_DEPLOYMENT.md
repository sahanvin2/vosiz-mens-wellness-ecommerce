# Complete AWS Deployment Setup - Vosiz Men's Wellness Ecommerce

## 🚀 Multi-Option AWS Deployment Guide

This comprehensive guide provides **4 production-ready deployment strategies** for your Laravel application with dual database architecture (MySQL + MongoDB).

---

## 📋 Pre-Deployment Preparation

### 1. Create Deployment Directory Structure
```bash
mkdir -p deployment/{elastic-beanstalk,docker,lambda,scripts}
mkdir -p deployment/elastic-beanstalk/.ebextensions
mkdir -p deployment/docker/nginx
```

### 2. Environment Requirements
- PHP 8.2+
- Composer 2.x
- Node.js 18+ (for frontend assets)
- AWS CLI v2
- EB CLI (for Elastic Beanstalk)

---

## 🎯 Option 1: Elastic Beanstalk (Recommended)

### Benefits:
- ✅ Managed platform with auto-scaling
- ✅ Built-in load balancer
- ✅ Easy SSL certificate integration
- ✅ Blue-green deployments
- ✅ Integrated monitoring

### Architecture:
```
Internet → Route 53 → CloudFront → ALB → EB Environment → RDS MySQL
                                              ↓
                                      MongoDB Atlas
```

### Cost: ~$40-80/month for production

---

## 🐳 Option 2: ECS with Fargate

### Benefits:
- ✅ Containerized deployment
- ✅ Serverless containers
- ✅ Fine-grained resource control
- ✅ Better for microservices

### Cost: ~$50-100/month for production

---

## ⚡ Option 3: Lambda Serverless

### Benefits:
- ✅ Pay-per-request pricing
- ✅ Auto-scaling to zero
- ✅ No server management
- ✅ Perfect for variable traffic

### Cost: ~$5-30/month (depends on traffic)

---

## 🖥️ Option 4: Traditional EC2

### Benefits:
- ✅ Full control over environment
- ✅ Custom configurations
- ✅ Direct server access
- ✅ Cost-effective for high traffic

### Cost: ~$30-150/month

---

## 🔧 Quick Start - Elastic Beanstalk Setup

### Step 1: Install Required Tools
```bash
# Install AWS CLI v2
curl "https://awscli.amazonaws.com/awscli-exe-windows-x86_64.msi" -o "AWSCLIV2.msi"
msiexec /i AWSCLIV2.msi

# Install EB CLI
pip install awsebcli

# Verify installations
aws --version
eb --version
```

### Step 2: Configure AWS Credentials
```bash
aws configure
# Enter your:
# - AWS Access Key ID
# - AWS Secret Access Key  
# - Default region: us-east-1
# - Default output format: json
```

### Step 3: Prepare Application
```bash
cd c:\xampp\htdocs\SSP\Lara

# Install production dependencies
composer install --optimize-autoloader --no-dev

# Build frontend assets
npm run build

# Clear and cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📁 Deployment Configuration Files

I'll create all the necessary configuration files for you next. Each deployment option will have its own set of configuration files.

### Files to be created:
1. **Elastic Beanstalk**: .ebextensions configurations
2. **Docker**: Dockerfile, docker-compose, nginx config
3. **Lambda**: Serverless configuration
4. **Scripts**: Database setup, environment setup, deployment automation

---

## 🗄️ Database Strategy

### Production Database Setup:
- **MySQL**: Amazon RDS (managed service)
- **MongoDB**: MongoDB Atlas (cloud-managed)
- **Redis**: Amazon ElastiCache (for sessions/cache)
- **File Storage**: Amazon S3 (for uploads)

### Connection Architecture:
```
Laravel App → RDS MySQL (users, orders, categories)
            → MongoDB Atlas (products, reviews)
            → ElastiCache Redis (sessions, cache)
            → S3 (file uploads)
```

---

## 🔐 Security & Environment Setup

### Production Environment Variables:
```env
# Application
APP_NAME="Vosiz Men's Wellness"
APP_ENV=production
APP_KEY=base64:your-generated-app-key
APP_DEBUG=false
APP_URL=https://vosiz.yourdomain.com

# MySQL Database (RDS)
DB_CONNECTION=mysql
DB_HOST=vosiz-db.cluster-xyz.us-east-1.rds.amazonaws.com
DB_PORT=3306
DB_DATABASE=vosiz_production
DB_USERNAME=vosiz_admin
DB_PASSWORD=your-secure-password

# MongoDB (Atlas)
MONGODB_DSN=mongodb+srv://username:password@cluster.mongodb.net/vosiz_products

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=vosiz-cache.abc123.cache.amazonaws.com
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=sqs
SQS_KEY=your-sqs-access-key
SQS_SECRET=your-sqs-secret-key
SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/123456789012
SQS_QUEUE=vosiz-jobs

# File Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-s3-access-key
AWS_SECRET_ACCESS_KEY=your-s3-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=vosiz-uploads
AWS_USE_PATH_STYLE_ENDPOINT=false

# Mail
MAIL_MAILER=ses
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your-ses-username
MAIL_PASSWORD=your-ses-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vosiz.com
MAIL_FROM_NAME="Vosiz Men's Wellness"

# Payment
STRIPE_KEY=pk_live_your_stripe_public_key
STRIPE_SECRET=sk_live_your_stripe_secret_key
```

---

## 📊 Monitoring & Performance

### CloudWatch Integration:
- Application performance metrics
- Database connection monitoring
- Error rate tracking
- Custom business metrics (orders, revenue)

### Health Checks:
- Application health endpoint: `/health`
- Database connectivity verification
- MongoDB Atlas connection test
- Redis cache connectivity

---

## 💰 Detailed Cost Breakdown

### Starter Production (Low Traffic):
```
Service                 | Cost/Month
------------------------|------------
EC2 t3.small           | $15.00
RDS db.t3.micro        | $13.00  
S3 Storage (10GB)      | $0.30
CloudFront CDN         | $1.00
Route 53 DNS           | $0.50
MongoDB Atlas M0       | Free
SSL Certificate        | Free (ACM)
------------------------|------------
Total                  | ~$30/month
```

### Production Scale (Medium Traffic):
```
Service                 | Cost/Month
------------------------|------------
EC2 t3.medium          | $30.00
RDS db.t3.small        | $25.00
ElastiCache t3.micro   | $15.00
S3 Storage (100GB)     | $3.00
CloudFront CDN         | $5.00
SES Email Service      | $1.00
MongoDB Atlas M10      | $57.00
------------------------|------------
Total                  | ~$136/month
```

---

## 🚀 Next Steps

1. **Choose your deployment strategy** (I recommend Elastic Beanstalk for most cases)
2. **Set up MongoDB Atlas** (can be done in parallel)
3. **Configure AWS services** (RDS, S3, etc.)
4. **Deploy application** using provided scripts
5. **Configure domain and SSL**
6. **Set up monitoring and backups**

Would you like me to proceed with creating the specific configuration files for your preferred deployment option?

**Recommended: Elastic Beanstalk** - provides the best balance of ease-of-use, features, and cost for a Laravel application.