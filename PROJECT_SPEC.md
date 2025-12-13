# PROJECT_SPEC.md
## D'house Waffle (MVP)
Single Tenant • SaaS-Ready • Laravel

---

## 1. PROJECT OVERVIEW
This project is a Laravel-based ordering system for D'house Waffle - a waffle business operating within apartment communities.

Purpose:
- Allow apartment residents to order fresh handmade waffles
- D'house Waffle manages menu and orders via dashboard
- Simple ordering and pickup system
- Multiple payment options (Cash, QR, Online)
- No platform fees - direct sales model

Scope:
- Single seller (D'house Waffle)
- Single apartment location
- Focus on easy ordering and quick pickup
- Mobile-first design

---

## 2. TECH STACK
- Backend: Laravel
- Frontend: Blade (mobile-first)
- Database: MySQL
- Payment Gateway: Billplz or ToyyibPay

---

## 3. ROLES
buyer (customers/residents)
seller (D'house Waffle staff)
apartment_admin (apartment management)
super_admin (system administrator)  

---

## 4. CORE BUSINESS RULES
1. Single seller model (D'house Waffle only)
2. No seller application system needed
3. Direct payment to seller (no platform fee)
4. Three payment methods: Cash, QR Payment, Online
5. Pickup at Lobby Utama (Ground Floor)
6. Operating hours: 9:00 AM - 9:00 PM
7. Apartment residents only

---

## 5. DATABASE DESIGN

### apartments
id, name, address, service_fee_percent, pickup_location,
pickup_start_time, pickup_end_time, status, timestamps

### users
id, apartment_id, name, email, password, phone,
role, unit_no, block, status, timestamps

### categories (for waffle types)
id, name, slug, icon, is_active, timestamps

### seller_applications (legacy - not used)
id, user_id, apartment_id, status, approved_by,
approved_at, remarks, timestamps

### products
id, apartment_id, seller_id, name, description,
price, is_active, timestamps

### orders
id, apartment_id, buyer_id, seller_id, order_no,
total_amount, platform_fee, seller_amount,
status, pickup_location, pickup_time,
payment_status, payment_ref, timestamps

### order_items
id, order_id, product_id, product_name,
price, quantity, subtotal, timestamps

### payments
id, order_id, gateway, amount, status,
reference_no, paid_at, timestamps

---

## 6. AUTH REDIRECT
buyer → /home  
seller → /seller/dashboard  
apartment_admin → /admin/dashboard  
super_admin → /super/dashboard  

---

## 7. SELLER ACCOUNT
D'house Waffle has pre-created seller account (no application needed)

---

## 8. CUSTOMER ORDER FLOW
Browse waffle menu → Add to cart → Checkout → Select payment method → Place order → Track status → Pickup at lobby

---

## 9. D'HOUSE WAFFLE DASHBOARD
- View all incoming orders
- Update order status: pending → preparing → ready → completed
- Manage waffle menu (add/edit/disable items)
- Mark payments as received (for cash/QR)
- View daily/monthly sales statistics

---

## 10. ADMIN DASHBOARD
- View all orders and transactions
- Monitor business performance
- Configure pickup location and operating hours
- Manage apartment settings

---

## 11. CONTROLLERS
AuthController  
BuyerController  
SellerController  
AdminController  
OrderController  
ProductController  
CategoryController (for waffle categories)  
PaymentWebhookController  

---

## 12. ROUTES

Public:
/, /login, /register

Customer (Buyer):
/home (browse waffles), /cart, /checkout, /orders

D'house Waffle (Seller):
/seller/dashboard, /seller/orders, /seller/products (menu management)

Admin:
/admin/dashboard, /admin/sellers, /admin/orders, /admin/settings

---

## 13. STATUS ENUMS
Order: pending, preparing, ready, completed, cancelled  
Payment: pending, paid, failed  

---

## 14. WHAT NOT TO BUILD (MVP)
- Multi-seller marketplace
- Seller application system
- Wallet/escrow
- Chat system
- Customer reviews
- Native mobile app
- Advanced analytics

---

## 15. IMPLEMENTATION NOTES
✅ Completed:
- User authentication system
- Waffle product management
- Order system with cart
- Multiple payment methods (Cash, QR, Online)
- Seller dashboard for D'house Waffle
- Admin dashboard
- Mobile-responsive design

🎯 Focus Areas:
- Simple waffle ordering experience
- Fast order processing
- Easy pickup coordination
- Real-time order tracking

---

## 16. BRAND IDENTITY
- Name: D'house Waffle 🧇
- Colors: Amber/Orange gradient (waffle theme)
- Target: Apartment residents
- Value Proposition: Fresh handmade waffles delivered to your doorstep
- Operating Hours: 9:00 AM - 9:00 PM
- Pickup: Lobby Utama (Ground Floor)
