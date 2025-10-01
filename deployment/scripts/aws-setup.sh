#!/bin/bash

# AWS Infrastructure Setup Script for Vosiz
# This script creates all necessary AWS resources for production deployment

set -e

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration variables
PROJECT_NAME="vosiz"
REGION="us-east-1"
ENVIRONMENT="production"

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

# Check AWS CLI installation and credentials
check_aws_setup() {
    print_status "Checking AWS setup..."
    
    if ! command -v aws &> /dev/null; then
        print_error "AWS CLI not found. Please install AWS CLI v2"
        exit 1
    fi
    
    aws sts get-caller-identity &> /dev/null
    if [ $? -eq 0 ]; then
        print_success "AWS credentials configured"
    else
        print_error "AWS credentials not configured. Run 'aws configure'"
        exit 1
    fi
}

# Create S3 bucket for file uploads
create_s3_bucket() {
    print_status "Creating S3 bucket for file storage..."
    
    BUCKET_NAME="${PROJECT_NAME}-${ENVIRONMENT}-uploads"
    
    aws s3 mb s3://${BUCKET_NAME} --region ${REGION} 2>/dev/null || {
        print_warning "Bucket ${BUCKET_NAME} might already exist or name taken"
    }
    
    # Configure bucket policy for public read access to specific paths
    cat > /tmp/bucket-policy.json << EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::${BUCKET_NAME}/public/*"
        }
    ]
}
EOF
    
    aws s3api put-bucket-policy --bucket ${BUCKET_NAME} --policy file:///tmp/bucket-policy.json
    
    # Enable versioning
    aws s3api put-bucket-versioning --bucket ${BUCKET_NAME} --versioning-configuration Status=Enabled
    
    print_success "S3 bucket ${BUCKET_NAME} created and configured"
    
    # Create CloudFront distribution for CDN
    print_status "Creating CloudFront distribution..."
    
    cat > /tmp/cloudfront-config.json << EOF
{
    "CallerReference": "${PROJECT_NAME}-${ENVIRONMENT}-$(date +%s)",
    "Comment": "Vosiz CDN for static assets",
    "DefaultRootObject": "",
    "Origins": {
        "Quantity": 1,
        "Items": [
            {
                "Id": "S3-${BUCKET_NAME}",
                "DomainName": "${BUCKET_NAME}.s3.amazonaws.com",
                "S3OriginConfig": {
                    "OriginAccessIdentity": ""
                }
            }
        ]
    },
    "DefaultCacheBehavior": {
        "TargetOriginId": "S3-${BUCKET_NAME}",
        "ViewerProtocolPolicy": "redirect-to-https",
        "TrustedSigners": {
            "Enabled": false,
            "Quantity": 0
        },
        "ForwardedValues": {
            "QueryString": false,
            "Cookies": {
                "Forward": "none"
            }
        },
        "MinTTL": 0,
        "Compress": true
    },
    "Enabled": true,
    "PriceClass": "PriceClass_100"
}
EOF
    
    print_success "CloudFront distribution configuration prepared"
}

# Create RDS MySQL instance
create_rds_instance() {
    print_status "Creating RDS MySQL instance..."
    
    DB_INSTANCE_ID="${PROJECT_NAME}-${ENVIRONMENT}-db"
    DB_NAME="vosiz_production"
    DB_USERNAME="vosiz_admin"
    DB_PASSWORD=$(openssl rand -base64 32 | tr -d /+= | head -c 16)
    
    aws rds create-db-instance \
        --db-instance-identifier ${DB_INSTANCE_ID} \
        --db-instance-class db.t3.micro \
        --engine mysql \
        --engine-version 8.0.35 \
        --master-username ${DB_USERNAME} \
        --master-user-password ${DB_PASSWORD} \
        --allocated-storage 20 \
        --db-name ${DB_NAME} \
        --vpc-security-group-ids default \
        --backup-retention-period 7 \
        --storage-encrypted \
        --deletion-protection \
        --region ${REGION} 2>/dev/null || {
        print_warning "RDS instance might already exist"
    }
    
    print_success "RDS instance creation initiated"
    print_warning "Save these credentials securely:"
    echo "  DB_HOST: Will be available after instance creation"
    echo "  DB_DATABASE: ${DB_NAME}"
    echo "  DB_USERNAME: ${DB_USERNAME}"
    echo "  DB_PASSWORD: ${DB_PASSWORD}"
}

# Create ElastiCache Redis instance
create_redis_cache() {
    print_status "Creating ElastiCache Redis instance..."
    
    CACHE_CLUSTER_ID="${PROJECT_NAME}-${ENVIRONMENT}-cache"
    
    aws elasticache create-cache-cluster \
        --cache-cluster-id ${CACHE_CLUSTER_ID} \
        --cache-node-type cache.t3.micro \
        --engine redis \
        --num-cache-nodes 1 \
        --region ${REGION} 2>/dev/null || {
        print_warning "Redis cache might already exist"
    }
    
    print_success "ElastiCache Redis creation initiated"
}

# Create SQS queue for job processing
create_sqs_queue() {
    print_status "Creating SQS queue for job processing..."
    
    QUEUE_NAME="${PROJECT_NAME}-${ENVIRONMENT}-queue"
    
    aws sqs create-queue \
        --queue-name ${QUEUE_NAME} \
        --attributes VisibilityTimeoutSeconds=300,MessageRetentionPeriod=1209600 \
        --region ${REGION} 2>/dev/null || {
        print_warning "SQS queue might already exist"
    }
    
    print_success "SQS queue created"
}

# Create IAM role for Elastic Beanstalk
create_iam_roles() {
    print_status "Creating IAM roles..."
    
    # Create role for EC2 instances
    cat > /tmp/ec2-trust-policy.json << EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Principal": {
                "Service": "ec2.amazonaws.com"
            },
            "Action": "sts:AssumeRole"
        }
    ]
}
EOF
    
    aws iam create-role \
        --role-name ${PROJECT_NAME}-ec2-role \
        --assume-role-policy-document file:///tmp/ec2-trust-policy.json 2>/dev/null || {
        print_warning "IAM role might already exist"
    }
    
    # Attach necessary policies
    aws iam attach-role-policy \
        --role-name ${PROJECT_NAME}-ec2-role \
        --policy-arn arn:aws:iam::aws:policy/AWSElasticBeanstalkWebTier
        
    aws iam attach-role-policy \
        --role-name ${PROJECT_NAME}-ec2-role \
        --policy-arn arn:aws:iam::aws:policy/AmazonS3FullAccess
        
    aws iam attach-role-policy \
        --role-name ${PROJECT_NAME}-ec2-role \
        --policy-arn arn:aws:iam::aws:policy/AmazonSQSFullAccess
    
    print_success "IAM roles created and policies attached"
}

# Setup MongoDB Atlas (instructions)
setup_mongodb_atlas() {
    print_status "MongoDB Atlas setup instructions..."
    
    print_warning "Please complete MongoDB Atlas setup manually:"
    echo "  1. Go to https://cloud.mongodb.com"
    echo "  2. Create a new project: 'Vosiz Production'"
    echo "  3. Create a cluster (M0 free tier or M10 for production)"
    echo "  4. Create database user with read/write permissions"
    echo "  5. Add your AWS IP ranges to network access"
    echo "  6. Get connection string for Laravel application"
    echo "  7. Update MONGODB_DSN in your environment variables"
}

# Display final setup instructions
display_final_instructions() {
    print_success "🎉 AWS infrastructure setup completed!"
    
    print_status "Next steps:"
    echo "  1. Wait for RDS instance to be available (5-10 minutes)"
    echo "  2. Complete MongoDB Atlas setup"
    echo "  3. Update environment variables in your deployment"
    echo "  4. Deploy your application using Elastic Beanstalk"
    
    print_status "Check resource status:"
    echo "  RDS: aws rds describe-db-instances --db-instance-identifier ${PROJECT_NAME}-${ENVIRONMENT}-db"
    echo "  ElastiCache: aws elasticache describe-cache-clusters --cache-cluster-id ${PROJECT_NAME}-${ENVIRONMENT}-cache"
    echo "  S3: aws s3 ls | grep ${PROJECT_NAME}"
    
    print_warning "Remember to:"
    echo "  - Save all passwords and connection strings securely"
    echo "  - Configure security groups for proper access"
    echo "  - Set up SSL certificates for production domains"
    echo "  - Configure backup and monitoring policies"
}

# Main execution
main() {
    echo "🚀 Starting AWS Infrastructure Setup for Vosiz..."
    
    check_aws_setup
    create_s3_bucket
    create_rds_instance
    create_redis_cache
    create_sqs_queue
    create_iam_roles
    setup_mongodb_atlas
    display_final_instructions
    
    print_success "✅ Infrastructure setup script completed!"
}

# Run main function
main "$@"