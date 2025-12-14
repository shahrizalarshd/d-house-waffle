#!/bin/bash

# D'house Waffle Production Deployment Script
# Usage: ./deploy.sh

echo "🚀 Starting deployment to production..."

# Configuration
SERVER="root@152.42.208.154"
PROJECT_DIR="/var/www/d-house-waffle"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}📡 Connecting to production server...${NC}"

ssh $SERVER << 'ENDSSH'
    cd /var/www/d-house-waffle
    
    echo "📥 Pulling latest code from GitHub..."
    git pull origin main
    
    echo "🔧 Clearing caches..."
    docker compose exec laravel.test php artisan config:clear
    docker compose exec laravel.test php artisan cache:clear
    docker compose exec laravel.test php artisan view:clear
    docker compose exec laravel.test php artisan route:clear
    
    echo "🗄️  Running migrations..."
    docker compose exec laravel.test php artisan migrate --force
    
    echo "⚡ Caching configuration..."
    docker compose exec laravel.test php artisan config:cache
    docker compose exec laravel.test php artisan route:cache
    docker compose exec laravel.test php artisan view:cache
    
    echo "🔄 Restarting services..."
    docker compose restart laravel.test
    
    echo "✅ Deployment complete!"
ENDSSH

echo -e "${GREEN}🎉 Production deployment successful!${NC}"
echo -e "${YELLOW}🌐 Website: http://152.42.208.154${NC}"


