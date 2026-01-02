# 🧇 Guest Checkout & Loyalty System - Implementation Complete

**Tarikh:** 3 Januari 2026  
**Status:** ✅ Selesai

---

## 📋 Ringkasan Perubahan

### Ciri Baharu yang Ditambah:

1. **Guest Checkout** - Pelanggan boleh order tanpa perlu mendaftar akaun
2. **Loyalty Stamp Card** - Sistem ganjaran untuk pelanggan berdaftar
3. **Tier System (Opsional)** - Bronze → Silver → Gold membership

---

## 📁 Fail yang Ditambah/Dikemaskini

### Migrasi Database Baharu:
```
database/migrations/
├── 2026_01_02_163038_add_guest_fields_to_orders_table.php
├── 2026_01_02_163044_add_loyalty_fields_to_users_table.php
├── 2026_01_02_163044_create_loyalty_settings_table.php
├── 2026_01_02_163044_create_loyalty_transactions_table.php
└── 2026_01_02_163045_add_discount_fields_to_orders_table.php
```

### Model Baharu:
```
app/Models/
├── LoyaltySetting.php
└── LoyaltyTransaction.php
```

### Service Class Baharu:
```
app/Services/
└── LoyaltyService.php
```

### Model yang Dikemaskini:
- `app/Models/Order.php` - Tambah guest methods & fields
- `app/Models/User.php` - Tambah loyalty fields & methods

### Controller yang Dikemaskini:
- `app/Http/Controllers/BuyerController.php` - Public menu, loyalty page
- `app/Http/Controllers/OrderController.php` - Guest checkout, loyalty discount
- `app/Http/Controllers/SellerController.php` - Award stamps on completion
- `app/Http/Controllers/AdminController.php` - Loyalty settings

### Routes yang Dikemaskini:
- `routes/web.php` - Public routes untuk guest, loyalty routes

### Views Baharu:
```
resources/views/buyer/
├── menu-public.blade.php      # Menu untuk guest (tanpa login)
├── order-track.blade.php      # Track order untuk guest
└── loyalty.blade.php          # Halaman loyalty card

resources/views/owner/
└── loyalty-settings.blade.php # Settings loyalty untuk owner
```

### Views yang Dikemaskini:
- `resources/views/buyer/home.blade.php` - Loyalty info banner
- `resources/views/buyer/checkout.blade.php` - Guest option & discount display
- `resources/views/seller/orders.blade.php` - Guest order info
- `resources/views/seller/dashboard.blade.php` - Loyalty settings link
- `resources/views/layouts/app.blade.php` - Loyalty nav link

---

## 🚀 Cara Setup

### 1. Jalankan Migrasi

```bash
php artisan migrate
```

### 2. (Opsional) Seed Loyalty Settings

Loyalty settings akan dicipta secara automatik dengan nilai default apabila pertama kali diakses.

---

## 📱 Penggunaan

### Untuk Guest (Pelanggan Baru):

1. Layari `/menu` untuk lihat menu (tanpa login)
2. Tambah item ke cart
3. Pergi ke `/checkout`
4. Pilih "Quick Order" (Guest)
5. Isi maklumat: Nama, Phone, Block, Unit
6. Pilih payment method & place order
7. Dapat tracking link unik: `/track/{token}`

### Untuk Registered Customer:

1. Login dan layari menu
2. Lihat loyalty progress di banner homepage
3. Collect stamps setiap order completed
4. Selepas 5 orders, dapat 10% diskaun automatik
5. Lihat full details di `/loyalty`

### Untuk Owner:

1. Pergi ke Owner Dashboard
2. Klik "Loyalty Settings"
3. Configure:
   - Guest checkout on/off
   - Stamps required (default: 5)
   - Discount percentage (default: 10%)
   - Discount validity (default: 30 days)
   - Tier system on/off

---

## 🔧 Configuration

### Default Settings:

```php
// Guest Checkout
'guest_checkout_enabled' => true,
'guest_pending_limit' => 3, // Max pending orders per phone

// Stamp Card
'loyalty_enabled' => true,
'stamps_required' => 5,
'stamp_discount_percent' => 10.00,
'discount_validity_days' => 30,

// Tiers (disabled by default)
'tiers_enabled' => false,
'silver_threshold' => 10,
'gold_threshold' => 25,
'silver_bonus_percent' => 2.00,
'gold_bonus_percent' => 5.00,
```

---

## 📊 Database Schema Baharu

### orders table (tambahan):
```
guest_name          VARCHAR(255) NULL
guest_phone         VARCHAR(255) NULL
guest_block         VARCHAR(255) NULL
guest_unit_no       VARCHAR(255) NULL
guest_token         VARCHAR(64) UNIQUE NULL
subtotal            DECIMAL(10,2)
discount_amount     DECIMAL(10,2)
discount_type       VARCHAR(255) NULL
discount_details    TEXT NULL (JSON)
```

### users table (tambahan):
```
loyalty_stamps              INT DEFAULT 0
lifetime_orders             INT DEFAULT 0
lifetime_spent              DECIMAL(10,2) DEFAULT 0
loyalty_tier                VARCHAR(255) DEFAULT 'bronze'
loyalty_discount_available  BOOLEAN DEFAULT FALSE
loyalty_discount_expires_at TIMESTAMP NULL
```

### loyalty_settings table (baharu):
```
id                      BIGINT PRIMARY KEY
apartment_id            BIGINT FK
guest_checkout_enabled  BOOLEAN
guest_pending_limit     INT
loyalty_enabled         BOOLEAN
stamps_required         INT
stamp_discount_percent  DECIMAL(5,2)
discount_validity_days  INT
tiers_enabled           BOOLEAN
silver_threshold        INT
gold_threshold          INT
silver_bonus_percent    DECIMAL(5,2)
gold_bonus_percent      DECIMAL(5,2)
```

### loyalty_transactions table (baharu):
```
id              BIGINT PRIMARY KEY
user_id         BIGINT FK
order_id        BIGINT FK NULL
type            VARCHAR(255)
description     VARCHAR(255)
stamps_change   INT
created_at      TIMESTAMP
```

---

## 🎯 Business Rules

### Guest Checkout:
- Max 3 pending orders per phone number (configurable)
- Guest token expires after order completed
- No loyalty stamps for guest orders
- CTA to register shown on tracking page

### Loyalty Stamps:
- 1 stamp per completed order
- Only for registered users
- Stamps reset to 0 when discount unlocked
- Discount valid for X days (configurable)
- Discount auto-applied at checkout if available

### Tier System (if enabled):
- Bronze: 0-9 lifetime orders (no bonus)
- Silver: 10-24 orders (extra 2% off)
- Gold: 25+ orders (extra 5% off)
- Tier bonuses stack with stamp card discount

---

## ✅ Testing Checklist

- [ ] Guest dapat lihat menu tanpa login
- [ ] Guest dapat add to cart
- [ ] Guest dapat checkout dengan isi details
- [ ] Guest dapat tracking link selepas order
- [ ] Guest order visible kepada seller dengan label "Guest"
- [ ] Registered user dapat stamps selepas order completed
- [ ] Discount auto-unlock selepas X stamps
- [ ] Discount applied di checkout
- [ ] Discount used marked after order placed
- [ ] Loyalty page shows correct progress
- [ ] Owner dapat configure loyalty settings
- [ ] Tier upgrades work (if enabled)

---

## 🔮 Future Enhancements

- [ ] SMS/WhatsApp notification dengan tracking link untuk guest
- [ ] Convert guest to registered user (link past orders)
- [ ] Referral program
- [ ] Birthday bonus stamps
- [ ] Double stamp days
- [ ] Points instead of stamps (more flexible)

---

**Implementation Complete!** 🎉

