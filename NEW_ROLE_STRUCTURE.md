# D'house Waffle - New Role Structure

## 🎯 Overview

Sistem D'house Waffle telah dikemaskini dengan struktur role yang lebih sesuai untuk single-seller waffle business.

---

## 👥 4 Role Types

### 1. 🛍️ **Customer** (Pelanggan)
**Previously:** `buyer`  
**Route Prefix:** `/home`, `/cart`, `/orders`

**Access & Permissions:**
- ✅ Browse waffle menu
- ✅ Add items to cart
- ✅ Place orders
- ✅ Choose payment method (Cash/QR/Online)
- ✅ Track order status
- ✅ View order history
- ✅ Manage profile
- ❌ No business management access

**Navigation (Mobile):**
- 🧇 Menu
- 🛒 Cart
- 🧾 Orders
- 👤 Profile

**Test Accounts:**
- Email: `customer@test.com` | Password: `password`
- Email: `customer2@test.com` | Password: `password`

---

### 2. 👨‍🍳 **Staff** (Pekerja)
**Previously:** `N/A` (new role)  
**Route Prefix:** `/staff`

**Access & Permissions:**
- ✅ View incoming orders
- ✅ Update order status (preparing → ready → completed)
- ✅ Process cash payments
- ✅ Verify QR payments
- ❌ Cannot manage menu/products
- ❌ Cannot view revenue details
- ❌ Cannot access business settings
- ❌ Cannot view profit margins

**Navigation (Mobile):**
- 🏠 Dashboard
- 📋 Orders

**Purpose:**
- Untuk staff yang hanya handle orders
- No access to sensitive business info
- Perfect for part-time/temporary workers

**Test Account:**
- Email: `staff@dhouse.com` | Password: `password`

---

### 3. 🧇 **Owner** (Pemilik)
**Previously:** `seller` + `apartment_admin` (merged)  
**Route Prefix:** `/owner`

**Access & Permissions:**
- ✅ Everything staff can do
- ✅ View full dashboard with revenue
- ✅ Manage waffle menu (add/edit/hide products)
- ✅ View sales statistics & analytics
- ✅ Configure business settings:
  - Service fee
  - Pickup location
  - Operating hours
- ✅ Setup QR payment code
- ✅ View all orders & revenue
- ✅ Full business control

**Navigation (Mobile):**
- 📊 Dashboard (with revenue)
- 📋 Orders
- 🍽️ Menu Management
- ⚙️ Settings

**Purpose:**
- Full business owner access
- Manage products, pricing, settings
- View financial reports
- Control entire operation

**Test Account:**
- Email: `owner@dhouse.com` | Password: `password`

---

### 4. 🔧 **Super Admin** (Pemilik Sistem)
**Previously:** `super_admin` (unchanged)  
**Route Prefix:** `/super`

**Access & Permissions:**
- ✅ System-level administration
- ✅ Manage multiple apartments (future)
- ✅ Platform-wide settings
- ✅ Billplz/Payment gateway configuration
- ✅ View all users & statistics
- ✅ Access all apartments data
- ✅ System configuration

**Navigation (Mobile):**
- 👑 Super Admin Dashboard

**Purpose:**
- System/platform owner
- Not involved in daily waffle business
- Technical & platform management
- Payment gateway setup

**Test Account:**
- Email: `super@admin.com` | Password: `password`

---

## 🔄 Migration Changes

### Database Update
```sql
-- Old roles
ENUM('buyer', 'seller', 'apartment_admin', 'super_admin')

-- New roles
ENUM('customer', 'staff', 'owner', 'super_admin')
```

### Automatic Data Migration
- `buyer` → `customer`
- `seller` → `owner`
- `apartment_admin` → deleted (merged into owner)
- `super_admin` → unchanged

---

## 📊 Role Comparison

| Feature | Customer | Staff | Owner | Super Admin |
|---------|----------|-------|-------|-------------|
| Order waffles | ✅ | ❌ | ✅* | ✅* |
| Process orders | ❌ | ✅ | ✅ | ❌ |
| Manage menu | ❌ | ❌ | ✅ | ❌ |
| View revenue | ❌ | ❌ | ✅ | ✅ |
| Business settings | ❌ | ❌ | ✅ | ❌ |
| Platform settings | ❌ | ❌ | ❌ | ✅ |
| Payment gateway | ❌ | ❌ | ❌ | ✅ |

\* Can order as customer but have business access too

---

## 🚀 Route Structure

### Customer Routes
```php
/home              - Browse menu
/cart              - Shopping cart
/checkout          - Place order
/orders            - Order history
/orders/{id}       - Order details
/profile           - Customer profile
```

### Staff Routes
```php
/staff/dashboard   - View today's orders
/staff/orders      - All orders (limited view)
```

### Owner Routes
```php
/owner/dashboard   - Full dashboard with revenue
/owner/orders      - All orders (full details)
/owner/products    - Manage menu
/owner/settings    - Business settings
/owner/profile     - QR payment setup
```

### Super Admin Routes
```php
/super/dashboard   - System overview
/super/settings    - Platform settings
/super/apartments  - Manage apartments
/super/users       - Manage all users
```

---

## 🔐 Authorization

### Middleware Usage
```php
// Staff only
Route::middleware('role:staff')

// Owner only
Route::middleware('role:owner')

// Staff OR Owner
Route::middleware('role:staff,owner')

// Super Admin only
Route::middleware('role:super_admin')
```

### Model Helper Methods
```php
// New methods
$user->isCustomer()
$user->isStaff()
$user->isOwner()
$user->isSuperAdmin()

// Business access checks
$user->canManageBusiness()        // staff or owner
$user->canAccessBusinessSettings() // owner only

// Backward compatibility (deprecated)
$user->isBuyer()   // same as isCustomer()
$user->isSeller()  // same as canManageBusiness()
$user->isAdmin()   // same as isOwner()
```

---

## 📱 UI/UX Changes

### Bottom Navigation (Mobile)

**Customer:**
- 🧇 Menu
- 🛒 Cart
- 🧾 Orders
- 👤 Profile

**Staff:**
- 🏠 Dashboard
- 📋 Orders

**Owner:**
- 📊 Dashboard
- 📋 Orders
- 🍽️ Menu
- ⚙️ Settings

**Super Admin:**
- 👑 Super Dashboard

### Color Coding
- Customer: Gray/Amber
- Staff: Blue
- Owner: Purple
- Super Admin: Red

---

## 🎯 Why This Structure?

### ✅ Advantages

1. **Better Security**
   - Staff can't see revenue/profit
   - Clear separation of duties
   - Owner has full control

2. **Scalability**
   - Easy to add multiple staff
   - Clear role hierarchy
   - Future-proof structure

3. **Simplicity**
   - No seller applications
   - No approval process
   - Straightforward access levels

4. **Real Business Model**
   - Reflects actual operations
   - Staff vs owner distinction
   - Proper access control

### 🎨 Business Flow

```
Customer → Places Order
    ↓
Staff → Processes Order
    ↓
Owner → Monitors Business & Revenue
    ↓
Super Admin → Manages Platform
```

---

## 📝 Test Accounts Summary

```
👤 Customer 1:
   Email: customer@test.com
   Pass: password
   Unit: 03-10, Block C

👤 Customer 2:
   Email: customer2@test.com
   Pass: password
   Unit: 05-08, Block B

👨‍🍳 Staff:
   Email: staff@dhouse.com
   Pass: password
   Name: Sarah (Staff)

🧇 Owner:
   Email: owner@dhouse.com
   Pass: password
   Name: Ahmad (D'house Waffle Owner)

🔧 Super Admin:
   Email: super@admin.com
   Pass: password
   Name: System Admin
```

---

## 🚀 Running the Migration

```bash
# Backup first (recommended)
./vendor/bin/sail artisan db:seed --class=BackupSeeder

# Run migration
./vendor/bin/sail artisan migrate

# Or fresh start
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## 📋 Post-Migration Checklist

- [ ] Migration ran successfully
- [ ] All test accounts work
- [ ] Customer can order
- [ ] Staff can process orders
- [ ] Owner can manage business
- [ ] Super admin has full access
- [ ] Navigation updated correctly
- [ ] Role badges display properly

---

## 🔮 Future Enhancements

1. **Multi-Staff Management**
   - Owner can create staff accounts
   - Assign specific permissions
   - Track staff performance

2. **Advanced Analytics**
   - Staff efficiency metrics
   - Peak hours analysis
   - Customer preferences

3. **Role-Based Notifications**
   - Staff: New orders only
   - Owner: Revenue alerts + orders
   - Customer: Order status updates

---

**Last Updated:** December 14, 2025  
**Version:** 2.0  
**Status:** ✅ Implemented & Ready

