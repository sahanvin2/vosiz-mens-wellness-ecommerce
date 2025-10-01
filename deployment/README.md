# 🚀 Vosiz AWS Deployment Guide

Complete deployment setup for **Vosiz Men's Wellness Ecommerce** on AWS with dual database architecture (MySQL + MongoDB).

## 📁 Deployment Structure

```
deployment/
├── elastic-beanstalk/          # Recommended deployment method
│   ├── .ebextensions/         # EB configuration files
│   │   ├── 01-packages.config # System packages and PHP extensions
│   │   ├── 02-php.config      # PHP settings and Laravel commands
│   │   └── 03-storage.config  # File permissions and logging
│   └── deploy.sh              # Automated deployment script (Unix)
├── docker/                    # Containerized deployment
│   ├── Dockerfile            # Multi-stage Docker build
│   ├── docker-compose.yml    # Complete stack with databases
│   ├── nginx/                # Nginx configuration
│   └── supervisord.conf      # Process management
├── scripts/                  # Utility scripts
│   ├── aws-setup.sh         # AWS infrastructure setup
│   └── post-deploy.sh       # Post-deployment configuration
├── .env.production.example   # Production environment template
└── deploy-wizard.bat        # Windows deployment wizard
```

## 🎯 Quick Start (Recommended: Elastic Beanstalk)

### Prerequisites
- AWS Account with appropriate permissions
- AWS CLI v2 installed and configured
- PHP 8.2+ and Composer
- Node.js 18+ (for frontend assets)

### Option 1: Windows Users (Easiest)
```cmd
# Run the deployment wizard
cd c:\xampp\htdocs\SSP\Lara
deployment\deploy-wizard.bat
```

### Option 2: Command Line Deployment
```bash
# 1. Install required tools
pip install awsebcli

# 2. Configure AWS credentials
aws configure

# 3. Prepare application
composer install --optimize-autoloader --no-dev
npm run build

# 4. Copy EB configuration
cp -r deployment/elastic-beanstalk/.ebextensions ./

# 5. Initialize and deploy
eb init vosiz-app --region us-east-1 --platform php-8.2
eb create vosiz-production --database.engine mysql
```

## 🗄️ Database Setup

### MongoDB Atlas (Products Database)
1. Create account at [MongoDB Atlas](https://cloud.mongodb.com)
2. Create cluster (M0 free tier available)
3. Create database user with read/write permissions
4. Add AWS IP ranges to network access list
5. Get connection string for `MONGODB_DSN`

### MySQL RDS (User/Order Data)
- Automatically created with Elastic Beanstalk
- Or manually create RDS instance:
```bash
aws rds create-db-instance \
    --db-instance-identifier vosiz-production-db \
    --db-instance-class db.t3.micro \
    --engine mysql \
    --master-username vosizadmin \
    --master-user-password your-secure-password \
    --allocated-storage 20
```

## 🔧 Environment Configuration

### Required Environment Variables
Copy `deployment/.env.production.example` and customize:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-32-character-key
APP_URL=https://vosiz.yourdomain.com

# MySQL Database (RDS)
DB_HOST=your-rds-endpoint.amazonaws.com
DB_DATABASE=vosiz_production
DB_USERNAME=vosizadmin
DB_PASSWORD=your-secure-password

# MongoDB (Atlas)
MONGODB_DSN=mongodb+srv://user:pass@cluster.mongodb.net/vosiz_products

# File Storage (S3)
FILESYSTEM_DISK=s3
AWS_BUCKET=vosiz-production-uploads
AWS_ACCESS_KEY_ID=your-s3-key
AWS_SECRET_ACCESS_KEY=your-s3-secret

# Cache (Redis/ElastiCache)
CACHE_DRIVER=redis
REDIS_HOST=your-elasticache-endpoint.amazonaws.com

# Queue (SQS)
QUEUE_CONNECTION=sqs
SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/account-id
SQS_QUEUE=vosiz-production-queue
```

### Setting Environment Variables in EB
```bash
# Set environment variables
eb setenv APP_KEY=base64:your-key-here
eb setenv MONGODB_DSN=mongodb+srv://...
eb setenv AWS_BUCKET=your-bucket-name

# Or set multiple at once
eb setenv APP_ENV=production APP_DEBUG=false DB_PASSWORD=secure123
```

## 📊 Infrastructure Options & Costs

### 🥇 Starter Setup (~$30/month)
- **Elastic Beanstalk**: Free platform
- **EC2 t3.micro**: $8.50/month
- **RDS db.t3.micro**: $13/month  
- **S3 Storage**: $3/month
- **MongoDB Atlas M0**: Free
- **Total**: ~$30/month

### 🚀 Production Setup (~$100/month)
- **EC2 t3.small**: $15/month
- **RDS db.t3.small**: $25/month
- **ElastiCache**: $15/month
- **S3 + CloudFront**: $10/month
- **MongoDB Atlas M10**: $57/month
- **Total**: ~$120/month

### 💪 High-Traffic Setup (~$300/month)
- **EC2 Auto Scaling**: $50-150/month
- **RDS Multi-AZ**: $50/month
- **ElastiCache Cluster**: $30/month
- **S3 + CloudFront**: $20/month
- **MongoDB Atlas M20**: $75/month
- **Load Balancer**: $18/month
- **Total**: ~$300/month

## 🐳 Alternative: Docker Deployment

### Local Development
```bash
cd deployment/docker
docker-compose up -d
```

### AWS ECS Deployment
```bash
# Build and push to ECR
aws ecr create-repository --repository-name vosiz-app
docker build -t vosiz-app .
docker tag vosiz-app:latest account-id.dkr.ecr.region.amazonaws.com/vosiz-app:latest
docker push account-id.dkr.ecr.region.amazonaws.com/vosiz-app:latest

# Deploy to ECS (requires additional ECS setup)
```

## 🔧 Post-Deployment Setup

### 1. Run Post-Deployment Script
```bash
# SSH into EB environment
eb ssh

# Run post-deployment setup
bash /var/app/current/deployment/scripts/post-deploy.sh
```

### 2. Database Migration & Seeding
```bash
# Run migrations
php artisan migrate --force

# Seed with initial data
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=MongoDBProductSeeder
```

### 3. SSL Certificate Setup
```bash
# Request certificate via ACM
aws acm request-certificate \
    --domain-name vosiz.yourdomain.com \
    --validation-method DNS

# Configure in EB console Load Balancer settings
```

## 📈 Monitoring & Maintenance

### Health Checks
- Application: `/health` or `/health.php`
- Database connectivity verification
- MongoDB Atlas connection test

### Monitoring Tools
- **CloudWatch**: Application and infrastructure metrics
- **EB Health Dashboard**: Application health overview
- **RDS Performance Insights**: Database performance
- **Custom Metrics**: Orders, revenue, user activity

### Log Management
```bash
# View application logs
eb logs

# Real-time log streaming
eb logs --stream

# Download logs
eb logs --all
```

## 🚨 Troubleshooting

### Common Issues

#### 1. Environment Variable Not Found
```bash
# Check current environment variables
eb printenv

# Set missing variables
eb setenv VARIABLE_NAME=value
```

#### 2. Database Connection Failed
- Verify RDS security groups allow EB access
- Check environment variables for database credentials
- Ensure RDS instance is in same VPC as EB

#### 3. MongoDB Connection Issues
- Check MongoDB Atlas network access list
- Verify connection string format
- Test connection from EB environment

#### 4. File Upload Issues
- Verify S3 bucket permissions
- Check IAM role has S3 access
- Test S3 connectivity

### Getting Help
```bash
# Check EB events
eb events

# Check application health
eb health

# SSH into environment for debugging
eb ssh

# View detailed logs
eb logs --all
```

## 🔄 Deployment Updates

### Code Updates
```bash
# Deploy new version
git add .
git commit -m "Update description"
eb deploy
```

### Configuration Updates
```bash
# Update environment variables
eb setenv NEW_VARIABLE=value

# Update EB configuration
# Edit .ebextensions files and redeploy
eb deploy
```

### Rolling Back
```bash
# List application versions
eb appversion

# Deploy previous version
eb deploy --version-label=previous-version-name
```

## 📞 Support & Resources

### Documentation
- [AWS Elastic Beanstalk Documentation](https://docs.aws.amazon.com/elasticbeanstalk/)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [MongoDB Atlas Documentation](https://docs.atlas.mongodb.com/)

### Useful Commands
```bash
# EB Commands
eb init          # Initialize EB application
eb create        # Create new environment
eb deploy        # Deploy application
eb open          # Open application in browser
eb terminate     # Terminate environment

# Laravel Commands
php artisan migrate --force    # Run migrations
php artisan config:cache      # Cache configuration
php artisan queue:work        # Process jobs

# AWS Commands
aws s3 ls                     # List S3 buckets
aws rds describe-db-instances # List RDS instances
aws elasticache describe-cache-clusters # List cache clusters
```

---

## ✅ Deployment Checklist

### Pre-Deployment
- [ ] AWS CLI installed and configured
- [ ] EB CLI installed
- [ ] MongoDB Atlas cluster created
- [ ] Environment variables documented
- [ ] Application tested locally
- [ ] Frontend assets built

### Deployment
- [ ] EB application initialized
- [ ] Production environment created
- [ ] Environment variables set
- [ ] Database migrations run
- [ ] Initial data seeded
- [ ] Health checks passing

### Post-Deployment
- [ ] SSL certificate configured
- [ ] Domain name pointed to EB
- [ ] Monitoring set up
- [ ] Backup strategy implemented
- [ ] Error tracking configured
- [ ] Performance testing completed

### Production Ready
- [ ] Security review completed
- [ ] Load testing passed
- [ ] Disaster recovery plan
- [ ] Team access configured
- [ ] Documentation updated

---

**🎉 Congratulations!** Your Vosiz Men's Wellness Ecommerce application is now running on AWS with enterprise-grade infrastructure!

For additional support, refer to the comprehensive guides in this deployment folder or check the project documentation.