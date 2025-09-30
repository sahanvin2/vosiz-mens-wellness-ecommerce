# AWS ECS Docker Deployment

## Step 1: Optimize Dockerfile for Production

```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    pkg-config

# Install MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
```

## Step 2: Create docker-compose for ECS

```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: vosiz-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - vosiz-network

  nginx:
    image: nginx:alpine
    container_name: vosiz-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - vosiz-network

networks:
  vosiz-network:
    driver: bridge
```

## Step 3: Deploy to AWS ECS

1. **Build and push to ECR:**
```bash
# Create ECR repository
aws ecr create-repository --repository-name vosiz-ecommerce

# Get login token
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin your-account.dkr.ecr.us-east-1.amazonaws.com

# Build and tag image
docker build -t vosiz-ecommerce .
docker tag vosiz-ecommerce:latest your-account.dkr.ecr.us-east-1.amazonaws.com/vosiz-ecommerce:latest

# Push to ECR
docker push your-account.dkr.ecr.us-east-1.amazonaws.com/vosiz-ecommerce:latest
```

2. **Create ECS Cluster:**
- Go to AWS ECS Console
- Create new cluster (Fargate)
- Define task definition
- Create service with Application Load Balancer

3. **Setup RDS for MySQL:**
- Create RDS MySQL instance
- Configure security groups
- Update environment variables