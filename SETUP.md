# Setup Guide - D'house Waffle MVP

## Quick Start with Docker (Recommended)

### 1. Prerequisites
- Docker Desktop installed
- Git

### 2. Clone and Setup

```bash
# Clone repository
git clone <repository-url>
cd pos-apartment

# Copy environment file
cp .env.example .env

# Update .env for Docker
# Change these lines:
DB_HOST=mysql
DB_DATABASE=pos_apartment
DB_USERNAME=sail
DB_PASSWORD=password
```

### 3. Start Docker Containers

```bash
# Start containers
./vendor/bin/sail up -d

# Or use docker-compose directly
docker-compose up -d
```

### 4. Setup Application

```bash
# Install dependencies (if not already done)
./vendor/bin/sail composer install

# Generate app key
./vendor/bin/sail artisan key:generate

# Run migrations and seeders
./vendor/bin/sail artisan migrate:fresh --seed

# Access the application
# Open: http://localhost
```

## Manual Setup (Without Docker)

### 1. Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM

### 2. Setup Steps

```bash
# Clone repository
git clone <repository-url>
cd pos-apartment

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_apartment
DB_USERNAME=root
DB_PASSWORD=your_password

# Create database
mysql -u root -p
CREATE DATABASE pos_apartment;
exit;

# Run migrations and seeders
php artisan migrate:fresh --seed

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start development server
php artisan serve
```

## Default Login Credentials

After seeding, you can login with these accounts:

### Super Admin
- **Email**: super@admin.com
- **Password**: password
- **Access**: All features + multi-apartment management (future)

### Apartment Admin (JMB)
- **Email**: admin@apartment.com
- **Password**: password
- **Access**: Approve sellers, view orders, manage settings

### Seller
- **Email**: seller@test.com
- **Password**: password
- **Access**: Manage products, view own orders

### Buyer
- **Email**: buyer@test.com
- **Password**: password
- **Access**: Browse products, place orders

## Testing the Application

### 1. As a Buyer
1. Login as `buyer@test.com`
2. Browse products on home page
3. Add products to cart
4. Go to cart and checkout
5. View orders

### 2. As a Seller
1. Login as `seller@test.com`
2. View dashboard with statistics
3. Manage products (create, edit, activate/deactivate)
4. View and update order status

### 3. As Admin
1. Login as `admin@apartment.com`
2. View dashboard
3. Approve/reject seller applications
4. View all orders
5. Update apartment settings (service fee, pickup location/time)

### 4. Seller Application Flow
1. Login as buyer
2. Click "Become a Seller" on home page
3. Fill application form
4. Logout and login as admin
5. Go to "Manage Sellers"
6. Approve the application
7. Login back as the buyer (now seller)
8. Access seller dashboard

## Common Issues & Solutions

### Database Connection Error
```bash
# Make sure MySQL is running
# For Docker:
./vendor/bin/sail mysql

# For local:
mysql.server start  # macOS
sudo service mysql start  # Linux
```

### Permission Errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Clear All Caches
```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## Payment Gateway Integration

### For Billplz
1. Sign up at https://www.billplz.com
2. Get API credentials
3. Update `.env`:
```env
BILLPLZ_API_KEY=your_api_key
BILLPLZ_COLLECTION_ID=your_collection_id
BILLPLZ_X_SIGNATURE=your_signature_key
```
4. Set webhook URL: `https://yourdomain.com/webhook/billplz`

### For ToyyibPay
1. Sign up at https://toyyibpay.com
2. Get API credentials
3. Update `.env`:
```env
TOYYIBPAY_SECRET_KEY=your_secret_key
TOYYIBPAY_CATEGORY_CODE=your_category_code
```
4. Set webhook URL: `https://yourdomain.com/webhook/toyyibpay`

## Production Deployment

### 1. Environment Setup
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Use production database
# Set secure APP_KEY
# Configure mail settings
# Configure queue driver (redis/database)
```

### 2. Optimization
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### 3. Security Checklist
- [ ] Set strong `APP_KEY`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure HTTPS
- [ ] Set proper file permissions
- [ ] Enable CSRF protection (already enabled)
- [ ] Configure secure session settings
- [ ] Set up regular database backups
- [ ] Configure rate limiting

## Support

For issues and questions:
1. Check this setup guide
2. Review README.md
3. Check Laravel documentation: https://laravel.com/docs
4. Contact development team

## Next Steps

1. Customize apartment information in admin settings
2. Invite residents to register
3. Review and approve seller applications
4. Configure payment gateway
5. Test complete order flow
6. Monitor transactions and platform fees

---

**Note**: This is an MVP (Minimum Viable Product). Additional features can be added based on requirements.

