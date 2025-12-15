# D'house Waffle 🧇

A Laravel-based ordering system for **D'house Waffle** - delivering delicious handmade waffles to apartment residents.

## About

D'house Waffle is a waffle business operating within apartment communities. Residents can easily order fresh waffles through this platform and pick them up at the apartment lobby.

## Features

- **🧇 Waffle Menu**: Browse classic and premium waffle selections
- **🛒 Easy Ordering**: Simple cart and checkout system
- **💳 Multiple Payment Options**: Cash, QR Payment (DuitNow/TNG), or Online Payment
- **📱 Mobile-First Design**: Optimized for smartphone ordering
- **👨‍🍳 Staff Operations**: Order processing with limited access
- **🧇 Owner Dashboard**: Full business management and revenue tracking
- **📊 Analytics**: Sales statistics and performance monitoring
- **🕐 Order Tracking**: Real-time order status updates

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL
- **Payment**: Billplz / ToyyibPay (webhook ready)

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL
- Node.js & NPM (for assets)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd pos-apartment
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Update .env file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_apartment
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Start development server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Default Credentials

### 🔧 Super Admin (System Owner)
- Email: `super@admin.com`
- Password: `password`
- Access: Platform-wide settings

### 🧇 Owner (D'house Waffle Business Owner)
- Email: `owner@dhouse.com`
- Password: `password`
- Access: Full business management

### 👨‍🍳 Staff (D'house Waffle Staff)
- Email: `staff@dhouse.com`
- Password: `password`
- Access: Order processing only

### 👤 Customer (Resident)
- Email: `customer@test.com`
- Password: `password`
- Access: Order waffles

> **📖 Detailed Role Documentation**: See [NEW_ROLE_STRUCTURE.md](NEW_ROLE_STRUCTURE.md) for complete role structure and permissions.

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── BuyerController.php
│   │   ├── SellerController.php
│   │   ├── AdminController.php
│   │   ├── OrderController.php
│   │   ├── ProductController.php
│   │   ├── SellerApplicationController.php
│   │   └── PaymentWebhookController.php
│   └── Middleware/
│       └── RoleMiddleware.php
└── Models/
    ├── Apartment.php
    ├── User.php
    ├── SellerApplication.php
    ├── Product.php
    ├── Order.php
    ├── OrderItem.php
    └── Payment.php

database/
├── migrations/
│   ├── *_create_apartments_table.php
│   ├── *_create_users_table.php
│   ├── *_create_seller_applications_table.php
│   ├── *_create_products_table.php
│   ├── *_create_orders_table.php
│   ├── *_create_order_items_table.php
│   └── *_create_payments_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── buyer/
│   ├── home.blade.php
│   ├── cart.blade.php
│   ├── checkout.blade.php
│   ├── orders.blade.php
│   ├── order-detail.blade.php
│   └── payment.blade.php
├── seller/
│   ├── dashboard.blade.php
│   ├── orders.blade.php
│   ├── products.blade.php
│   ├── product-create.blade.php
│   └── product-edit.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── sellers.blade.php
│   ├── orders.blade.php
│   └── settings.blade.php
└── seller-application/
    ├── form.blade.php
    └── status.blade.php
```

## User Flows

### 👤 Customer Flow
1. Register/Login → Browse waffle menu at `/home`
2. Add waffles to cart (stored in localStorage)
3. Proceed to checkout
4. Choose payment method:
   - Cash (pay on pickup)
   - QR Payment (upload proof)
   - Online Payment (via Billplz/ToyyibPay)
5. Receive order confirmation
6. Track order status
7. Pick up at lobby when ready

### 👨‍🍳 Staff Flow
1. Login → Access `/staff/dashboard`
2. View incoming orders
3. Update order status: pending → preparing → ready → completed
4. Mark orders as paid (for cash/QR payments)
5. Process daily operations

### 🧇 Owner Flow
1. Login → Access `/owner/dashboard`
2. View full revenue and statistics
3. Manage waffle menu (add/edit items)
4. Process orders (same as staff)
5. Configure business settings
6. Setup QR payment code
7. Access complete business analytics

### 🔧 Super Admin Flow
1. Login → Access `/super/dashboard`
2. Manage platform settings
3. Configure payment gateways
4. View all apartments and users
5. System-level administration

## Business Rules

1. **Single Seller**: D'house Waffle is the only seller on the platform
2. **No Service Fee**: Direct purchase from seller (0% platform fee)
3. **Payment Options**: Cash on pickup, QR payment, or online payment
4. **Pickup Location**: Lobby Utama (Ground Floor)
5. **Operating Hours**: 9:00 AM - 9:00 PM daily
6. **Order Management**: Real-time order tracking and status updates

## Payment Integration

The system supports webhooks for:
- **Billplz**: `/webhook/billplz`
- **ToyyibPay**: `/webhook/toyyibpay`

To integrate:
1. Configure payment gateway credentials in `.env`
2. Set webhook URL in payment gateway dashboard
3. Payments will automatically update order status

## API Endpoints

### Webhooks
- `POST /webhook/billplz` - Billplz payment callback
- `POST /webhook/toyyibpay` - ToyyibPay payment callback

## Future Enhancements

- 📸 Product images for each waffle
- ⭐ Customer reviews and ratings
- 🎁 Loyalty points and rewards
- 📅 Pre-order scheduling
- 🔔 Push notifications for order updates
- 📊 Advanced sales analytics
- 🏢 Multi-apartment expansion

## Database Schema

### apartments
- id, name, address, service_fee_percent, pickup_location, pickup_start_time, pickup_end_time, status, timestamps

### users
- id, apartment_id, name, email, password, phone, role, unit_no, block, status, timestamps

### seller_applications
- id, user_id, apartment_id, status, approved_by, approved_at, remarks, timestamps

### products
- id, apartment_id, seller_id, name, description, price, is_active, timestamps

### orders
- id, apartment_id, buyer_id, seller_id, order_no, total_amount, platform_fee, seller_amount, status, pickup_location, pickup_time, payment_status, payment_ref, timestamps

### order_items
- id, order_id, product_id, product_name, price, quantity, subtotal, timestamps

### payments
- id, order_id, gateway, amount, status, reference_no, paid_at, timestamps

## License

Proprietary - All rights reserved

## Support

For issues and questions, please contact the development team.
# Auto-deploy test
