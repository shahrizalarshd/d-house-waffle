# 🧇 D'house Waffle - Analisis Mendalam
## Guest Checkout & Sistem Loyalty

**Tarikh:** 3 Januari 2026  
**Status:** Perancangan & Analisis

---

## 📋 Ringkasan Eksekutif

Dokumen ini mengandungi analisis terperinci untuk dua ciri baharu:
1. **Guest Checkout** - Membolehkan pelanggan memesan tanpa perlu mendaftar akaun
2. **Loyalty System** - Sistem ganjaran untuk pelanggan berulang yang mendaftar

Matlamat utama: **Meningkatkan conversion rate** dengan mengurangkan friction untuk pelanggan baru, sambil **meningkatkan retention** melalui insentif loyalty.

---

## 📊 Bahagian 1: Analisis Sistem Semasa

### 1.1 Flow Semasa (Wajib Login)

```
Pelanggan Baru → Lihat Welcome Page → Klik Login/Register
                                            ↓
                        Isi Borang (Email, Password, Phone, Block, Unit)
                                            ↓
                              Login → Lihat Menu → Add to Cart
                                            ↓
                              Checkout → Pilih Payment → Order
```

**Masalah:**
- 5-7 langkah sebelum boleh order
- Pelanggan yang "just browsing" terus keluar
- Conversion rate rendah untuk first-time visitors

### 1.2 Struktur Database Semasa

**Jadual `orders`:**
```
├── buyer_id (FK, REQUIRED) ← Masalah utama
├── apartment_id
├── seller_id
├── order_no
├── total_amount
├── payment_method
├── payment_status
└── ... lain-lain
```

**Jadual `users`:**
```
├── name, email, password
├── phone, block, unit_no
├── apartment_id
├── role
└── status
```

**Isu:** `buyer_id` adalah **NOT NULLABLE** - tidak boleh simpan order tanpa user account.

---

## 🛒 Bahagian 2: Guest Checkout - Analisis Terperinci

### 2.1 Konsep

Pelanggan boleh memesan tanpa mendaftar akaun, hanya perlu masukkan maklumat minimum untuk penghantaran/pickup.

### 2.2 Flow Baharu (Guest)

```
Pelanggan Baru → Lihat Menu (PUBLIC) → Add to Cart
                        ↓
              Checkout → Pilih "Order as Guest"
                        ↓
              Isi: Nama, Phone, Block, Unit
                        ↓
              Pilih Payment → Place Order
                        ↓
              Success Page dengan Tracking Link
```

**Kelebihan:**
- 3-4 langkah sahaja
- Tiada password untuk diingat
- Pelanggan lapar boleh order dengan cepat

### 2.3 Perubahan Database untuk Guest Checkout

**Migrasi Baharu - `add_guest_fields_to_orders`:**

```php
Schema::table('orders', function (Blueprint $table) {
    // Jadikan buyer_id nullable
    $table->foreignId('buyer_id')->nullable()->change();
    
    // Tambah kolom untuk maklumat guest
    $table->string('guest_name')->nullable();
    $table->string('guest_phone')->nullable();
    $table->string('guest_block')->nullable();
    $table->string('guest_unit_no')->nullable();
    $table->string('guest_token')->nullable()->unique(); // Untuk tracking
    
    // Index untuk carian
    $table->index('guest_phone');
    $table->index('guest_token');
});
```

### 2.4 Logik Backend untuk Guest

**OrderController@placeOrder (Kemaskini):**

```php
public function placeOrder(Request $request)
{
    // Validasi berbeza untuk guest vs user
    if (auth()->check()) {
        // User logged in - gunakan data dari profile
        $buyerId = auth()->id();
        $guestData = null;
    } else {
        // Guest checkout
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_block' => 'required|string|max:10',
            'guest_unit_no' => 'required|string|max:20',
        ]);
        
        $buyerId = null;
        $guestData = [
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_block' => $validated['guest_block'],
            'guest_unit_no' => $validated['guest_unit_no'],
            'guest_token' => Str::random(32), // Untuk tracking
        ];
    }
    
    // ... create order dengan buyer_id atau guest data
}
```

### 2.5 Tracking untuk Guest

**Masalah:** Guest tiada akaun, macam mana nak check status?

**Penyelesaian:**

1. **Token-based Tracking:**
   - Setiap order guest dapat `guest_token` unik
   - URL: `/track/{guest_token}` → Public, tanpa login
   - Papar status order tanpa expose data sensitif

2. **WhatsApp/SMS Notification (Pilihan):**
   - Hantar link tracking ke phone number guest
   - Kos tambahan jika guna SMS gateway

3. **Success Page:**
   - Tunjuk order details dengan QR code
   - Pelanggan screenshot atau bookmark

**Route Baharu:**
```php
// Public route - tanpa auth
Route::get('/track/{token}', [OrderController::class, 'trackOrder'])
    ->name('order.track');
```

### 2.6 UI/UX untuk Guest Checkout

**Checkout Page - Dua Pilihan:**

```
┌─────────────────────────────────────────────────┐
│  🛒 Checkout                                    │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌─────────────────┐  ┌─────────────────┐      │
│  │  👤 Login       │  │  🚀 Quick Order │      │
│  │                 │  │                 │      │
│  │  Already have   │  │  Order without  │      │
│  │  an account?    │  │  registering    │      │
│  │                 │  │                 │      │
│  │  [Login Now]    │  │  [Continue]     │      │
│  └─────────────────┘  └─────────────────┘      │
│                                                 │
│  ✨ Login to earn loyalty points!              │
│                                                 │
└─────────────────────────────────────────────────┘
```

**Borang Guest (Jika pilih Quick Order):**

```
┌─────────────────────────────────────────────────┐
│  📝 Your Details                                │
├─────────────────────────────────────────────────┤
│                                                 │
│  Name *                                         │
│  ┌─────────────────────────────────────┐       │
│  │ Ahmad                                │       │
│  └─────────────────────────────────────┘       │
│                                                 │
│  Phone Number *                                 │
│  ┌─────────────────────────────────────┐       │
│  │ 012-3456789                          │       │
│  └─────────────────────────────────────┘       │
│                                                 │
│  Block *              Unit No *                 │
│  ┌───────────┐       ┌───────────────┐         │
│  │ A         │       │ 12-05         │         │
│  └───────────┘       └───────────────┘         │
│                                                 │
│  💡 Tip: Register to earn FREE waffles!        │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 2.7 Keselamatan Guest Checkout

| Risiko | Mitigasi |
|--------|----------|
| Fake orders | Rate limiting per phone number (max 3 pending orders) |
| Spam | Require valid Malaysian phone format |
| Data privacy | Guest data auto-delete selepas 30 hari jika tidak convert |
| Order tracking abuse | Token expires selepas 7 hari |

### 2.8 Kelebihan & Kekurangan Guest Checkout

**Kelebihan:**
- ✅ Conversion rate meningkat (kurang friction)
- ✅ Pelanggan baru mudah cuba
- ✅ Cepat untuk one-time buyers
- ✅ Tiada password untuk diingat

**Kekurangan:**
- ❌ Tiada order history (untuk guest)
- ❌ Susah untuk remarketing
- ❌ Potensi duplicate orders
- ❌ Tidak layak untuk loyalty program

---

## 🏆 Bahagian 3: Loyalty System - Analisis Terperinci

### 3.1 Konsep

Sistem ganjaran untuk menggalakkan pelanggan:
1. Mendaftar akaun (convert dari guest)
2. Order berulang kali
3. Terus setia dengan jenama

### 3.2 Perbandingan Model Loyalty

| Model | Cara Kerja | Kelebihan | Kekurangan |
|-------|------------|-----------|------------|
| **Stamp Card** | Order X kali → dapat diskaun | Mudah faham | Tiada fleksibiliti |
| **Points** | RM1 = 1 mata, tukar dengan reward | Sangat fleksibel | Kompleks untuk setup |
| **Tier** | Bronze → Silver → Gold | Gamification | Sukar untuk bisnes kecil |
| **Cashback** | X% dikreditkan untuk next order | Mudah track | Margin terjejas |

### 3.3 Cadangan: Model Hybrid (Stamp + Tier Lite)

**Mekanisma:**

```
┌─────────────────────────────────────────────────────────┐
│                   LOYALTY CARD 🧇                       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│   [ ✓ ] [ ✓ ] [ ✓ ] [   ] [   ]                        │
│                                                         │
│   3 / 5 orders completed                               │
│   ══════════════════════▓▓▓▓▓▓░░░░░░░░░░ 60%           │
│                                                         │
│   🎁 2 more orders to unlock 10% discount!             │
│                                                         │
├─────────────────────────────────────────────────────────┤
│   Total Lifetime Orders: 23                            │
│   Member Since: Dec 2025                               │
│   Status: 🥈 Silver Member                             │
└─────────────────────────────────────────────────────────┘
```

**Peraturan:**
1. **Stamp Card:** Order 5 kali → dapat 10% untuk order ke-6
2. **Tier (Opsional):**
   - 🥉 Bronze: 0-9 lifetime orders (tiada bonus)
   - 🥈 Silver: 10-24 orders (extra 2% off always)
   - 🥇 Gold: 25+ orders (extra 5% off + priority)

### 3.4 Perubahan Database untuk Loyalty

**Migrasi - `add_loyalty_fields_to_users`:**

```php
Schema::table('users', function (Blueprint $table) {
    // Loyalty tracking
    $table->unsignedInteger('loyalty_stamps')->default(0);
    $table->unsignedInteger('lifetime_orders')->default(0);
    $table->decimal('lifetime_spent', 10, 2)->default(0);
    $table->string('loyalty_tier')->default('bronze'); // bronze, silver, gold
    $table->boolean('loyalty_discount_available')->default(false);
    $table->timestamp('loyalty_discount_expires_at')->nullable();
});
```

**Migrasi - `create_loyalty_settings_table`:**

```php
Schema::create('loyalty_settings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
    
    // Stamp card settings
    $table->unsignedInteger('stamps_required')->default(5);
    $table->decimal('stamp_discount_percent', 5, 2)->default(10.00);
    $table->unsignedInteger('discount_validity_days')->default(30);
    
    // Tier settings
    $table->boolean('tiers_enabled')->default(false);
    $table->unsignedInteger('silver_threshold')->default(10);
    $table->unsignedInteger('gold_threshold')->default(25);
    $table->decimal('silver_bonus_percent', 5, 2)->default(2.00);
    $table->decimal('gold_bonus_percent', 5, 2)->default(5.00);
    
    $table->timestamps();
});
```

**Migrasi - `create_loyalty_transactions_table` (Audit Trail):**

```php
Schema::create('loyalty_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
    $table->string('type'); // stamp_earned, discount_used, tier_upgraded
    $table->string('description');
    $table->integer('stamps_change')->default(0); // +1 or -5
    $table->timestamps();
});
```

### 3.5 Logik Backend untuk Loyalty

**LoyaltyService.php (Service Class Baharu):**

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\LoyaltyTransaction;
use App\Models\LoyaltySetting;

class LoyaltyService
{
    /**
     * Award stamp after order completed
     */
    public function awardStamp(User $user, Order $order): void
    {
        $settings = $this->getSettings($order->apartment_id);
        
        // Add stamp
        $user->increment('loyalty_stamps');
        $user->increment('lifetime_orders');
        $user->increment('lifetime_spent', $order->total_amount);
        
        // Log transaction
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'stamp_earned',
            'description' => "Earned 1 stamp from Order #{$order->order_no}",
            'stamps_change' => 1,
        ]);
        
        // Check if discount unlocked
        if ($user->loyalty_stamps >= $settings->stamps_required) {
            $this->unlockDiscount($user, $settings);
        }
        
        // Check tier upgrade
        $this->checkTierUpgrade($user, $settings);
    }
    
    /**
     * Unlock discount when stamps complete
     */
    protected function unlockDiscount(User $user, LoyaltySetting $settings): void
    {
        $user->update([
            'loyalty_stamps' => 0, // Reset stamps
            'loyalty_discount_available' => true,
            'loyalty_discount_expires_at' => now()->addDays($settings->discount_validity_days),
        ]);
        
        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'type' => 'discount_unlocked',
            'description' => "Unlocked {$settings->stamp_discount_percent}% discount!",
            'stamps_change' => -$settings->stamps_required,
        ]);
    }
    
    /**
     * Apply discount to order
     */
    public function applyDiscount(User $user, float $subtotal): array
    {
        $discount = 0;
        $discountDetails = [];
        $settings = $this->getSettings($user->apartment_id);
        
        // Stamp card discount
        if ($user->loyalty_discount_available && 
            $user->loyalty_discount_expires_at > now()) {
            $stampDiscount = $subtotal * ($settings->stamp_discount_percent / 100);
            $discount += $stampDiscount;
            $discountDetails[] = [
                'type' => 'stamp_card',
                'percent' => $settings->stamp_discount_percent,
                'amount' => $stampDiscount,
            ];
        }
        
        // Tier bonus
        if ($settings->tiers_enabled) {
            $tierPercent = $this->getTierBonusPercent($user, $settings);
            if ($tierPercent > 0) {
                $tierDiscount = $subtotal * ($tierPercent / 100);
                $discount += $tierDiscount;
                $discountDetails[] = [
                    'type' => 'tier_bonus',
                    'tier' => $user->loyalty_tier,
                    'percent' => $tierPercent,
                    'amount' => $tierDiscount,
                ];
            }
        }
        
        return [
            'total_discount' => $discount,
            'details' => $discountDetails,
        ];
    }
    
    /**
     * Mark discount as used
     */
    public function useDiscount(User $user, Order $order): void
    {
        if ($user->loyalty_discount_available) {
            $user->update([
                'loyalty_discount_available' => false,
                'loyalty_discount_expires_at' => null,
            ]);
            
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'discount_used',
                'description' => "Used loyalty discount on Order #{$order->order_no}",
            ]);
        }
    }
    
    /**
     * Check and upgrade tier
     */
    protected function checkTierUpgrade(User $user, LoyaltySetting $settings): void
    {
        if (!$settings->tiers_enabled) return;
        
        $newTier = 'bronze';
        if ($user->lifetime_orders >= $settings->gold_threshold) {
            $newTier = 'gold';
        } elseif ($user->lifetime_orders >= $settings->silver_threshold) {
            $newTier = 'silver';
        }
        
        if ($newTier !== $user->loyalty_tier) {
            $oldTier = $user->loyalty_tier;
            $user->update(['loyalty_tier' => $newTier]);
            
            LoyaltyTransaction::create([
                'user_id' => $user->id,
                'type' => 'tier_upgraded',
                'description' => "Upgraded from {$oldTier} to {$newTier}!",
            ]);
        }
    }
    
    protected function getTierBonusPercent(User $user, LoyaltySetting $settings): float
    {
        return match($user->loyalty_tier) {
            'gold' => $settings->gold_bonus_percent,
            'silver' => $settings->silver_bonus_percent,
            default => 0,
        };
    }
    
    protected function getSettings(int $apartmentId): LoyaltySetting
    {
        return LoyaltySetting::firstOrCreate(
            ['apartment_id' => $apartmentId],
            [
                'stamps_required' => 5,
                'stamp_discount_percent' => 10,
                'discount_validity_days' => 30,
            ]
        );
    }
}
```

### 3.6 Integrasi dengan Order Flow

**SellerController@updateOrderStatus (Kemaskini):**

```php
public function updateOrderStatus(Request $request, $id)
{
    $order = Order::findOrFail($id);
    $oldStatus = $order->status;
    
    $order->update(['status' => $request->status]);
    
    // Award loyalty stamp when order completed
    if ($request->status === 'completed' && 
        $oldStatus !== 'completed' && 
        $order->buyer_id) { // Only for registered users
        
        $loyaltyService = app(LoyaltyService::class);
        $loyaltyService->awardStamp($order->buyer, $order);
    }
    
    return back()->with('success', 'Order status updated!');
}
```

**OrderController@placeOrder (Kemaskini untuk Diskaun):**

```php
public function placeOrder(Request $request)
{
    // ... existing validation ...
    
    $subtotal = /* calculate */;
    $discount = 0;
    $discountDetails = [];
    
    // Apply loyalty discount for logged in users
    if (auth()->check()) {
        $loyaltyService = app(LoyaltyService::class);
        $discountResult = $loyaltyService->applyDiscount(auth()->user(), $subtotal);
        $discount = $discountResult['total_discount'];
        $discountDetails = $discountResult['details'];
    }
    
    $totalAmount = $subtotal - $discount;
    
    $order = Order::create([
        // ... existing fields ...
        'subtotal' => $subtotal,
        'discount_amount' => $discount,
        'total_amount' => $totalAmount,
    ]);
    
    // Mark discount as used
    if ($discount > 0 && auth()->check()) {
        $loyaltyService->useDiscount(auth()->user(), $order);
    }
    
    // ... rest of order creation ...
}
```

### 3.7 UI untuk Loyalty System

**Customer Dashboard - Loyalty Card:**

```html
<!-- Loyalty Card Widget -->
<div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-lg font-bold">🧇 D'house Waffle</h3>
            <p class="text-amber-100 text-sm">Loyalty Card</p>
        </div>
        <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
            {{ $user->loyalty_tier === 'gold' ? '🥇' : ($user->loyalty_tier === 'silver' ? '🥈' : '🥉') }}
            {{ ucfirst($user->loyalty_tier) }}
        </span>
    </div>
    
    <!-- Stamp Progress -->
    <div class="mb-4">
        <div class="flex justify-between text-sm mb-2">
            <span>{{ $user->loyalty_stamps }} / {{ $settings->stamps_required }} stamps</span>
            <span>{{ $user->loyalty_stamps >= $settings->stamps_required ? '🎉 Complete!' : '' }}</span>
        </div>
        <div class="flex gap-2">
            @for($i = 0; $i < $settings->stamps_required; $i++)
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                    {{ $i < $user->loyalty_stamps ? 'bg-white text-amber-600' : 'bg-white/20' }}">
                    {{ $i < $user->loyalty_stamps ? '✓' : ($i + 1) }}
                </div>
            @endfor
        </div>
    </div>
    
    <!-- Discount Status -->
    @if($user->loyalty_discount_available)
    <div class="bg-white/20 rounded-lg p-3 text-center">
        <p class="font-bold">🎁 You have {{ $settings->stamp_discount_percent }}% OFF!</p>
        <p class="text-sm text-amber-100">
            Valid until {{ $user->loyalty_discount_expires_at->format('d M Y') }}
        </p>
    </div>
    @else
    <p class="text-center text-amber-100 text-sm">
        {{ $settings->stamps_required - $user->loyalty_stamps }} more orders to unlock 
        {{ $settings->stamp_discount_percent }}% discount!
    </p>
    @endif
</div>
```

### 3.8 Checkout dengan Diskaun

```html
<!-- Order Summary with Discount -->
<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h3 class="font-bold mb-3">Order Summary</h3>
    <div class="space-y-2 text-sm">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span id="subtotal">RM 25.00</span>
        </div>
        
        @if($hasLoyaltyDiscount)
        <div class="flex justify-between text-green-600">
            <span>🎁 Loyalty Discount ({{ $discountPercent }}%):</span>
            <span id="discount">- RM 2.50</span>
        </div>
        @endif
        
        @if($tierBonus > 0)
        <div class="flex justify-between text-purple-600">
            <span>🥈 {{ ucfirst($tier) }} Member Bonus:</span>
            <span>- RM 0.50</span>
        </div>
        @endif
        
        <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t-2">
            <span>🧇 Total:</span>
            <span class="text-amber-600" id="total">RM 22.00</span>
        </div>
    </div>
</div>
```

### 3.9 Kelebihan & Kekurangan Loyalty System

**Kelebihan:**
- ✅ Meningkatkan repeat orders
- ✅ Menggalakkan guest untuk register
- ✅ Data pelanggan untuk remarketing
- ✅ Gamification meningkatkan engagement
- ✅ Diferensiasi dari pesaing

**Kekurangan:**
- ❌ Kompleksiti sistem bertambah
- ❌ Kos diskaun mengurangkan margin
- ❌ Perlu maintenance dan monitoring
- ❌ Potensi abuse (multiple accounts)

---

## ⚖️ Bahagian 4: Perbandingan Senario

### 4.1 Conversion Funnel Comparison

**Tanpa Guest Checkout (Sekarang):**
```
100 visitors → 40 register → 30 add to cart → 20 checkout → 15 complete
Conversion: 15%
```

**Dengan Guest Checkout:**
```
100 visitors → 60 add to cart → 50 checkout → 40 complete (25 guest + 15 registered)
Conversion: 40% (+167% improvement)
```

**Dengan Loyalty System:**
```
40 completed orders → 15 registered users
15 registered → 10 repeat orders → 5 unlock discount → 8 use discount
Repeat Rate: 53%
```

### 4.2 Revenue Impact Analysis

**Scenario: 100 orders/month**

| Metric | Tanpa Perubahan | Dengan Guest + Loyalty |
|--------|-----------------|------------------------|
| Total Orders | 100 | 150 (+50%) |
| Guest Orders | 0 | 60 |
| Registered Orders | 100 | 90 |
| Repeat Orders | 30 | 50 |
| Discount Given | RM 0 | RM 150 (10 users × RM 15 avg) |
| Additional Revenue | - | RM 750 (50 new orders × RM 15) |
| **Net Gain** | - | **+RM 600/month** |

### 4.3 Implementation Complexity

| Feature | Effort | Risk | Priority |
|---------|--------|------|----------|
| Guest Checkout | Medium (2-3 days) | Low | **HIGH** |
| Stamp Card | Low (1-2 days) | Low | **HIGH** |
| Tier System | Medium (2 days) | Medium | **MEDIUM** |
| Auto SMS/WhatsApp | High (depends on API) | Medium | **LOW** |

---

## 🔒 Bahagian 5: Keselamatan & Edge Cases

### 5.1 Guest Checkout Security

| Risiko | Mitigasi |
|--------|----------|
| Spam orders | Rate limit: Max 3 pending orders per phone |
| Fake phone numbers | Format validation + optional OTP |
| Data harvesting | Guest data auto-purge after 90 days |
| Order tracking abuse | Token expires 7 days after completion |

### 5.2 Loyalty System Security

| Risiko | Mitigasi |
|--------|----------|
| Multiple accounts | Unique phone number per account |
| Discount abuse | One active discount per user |
| Point manipulation | Audit trail via loyalty_transactions |
| Expired discount use | Check expiry date server-side |

### 5.3 Edge Cases to Handle

1. **Guest converts to registered user:**
   - Link past guest orders via phone number match
   - Optionally award stamps for past orders

2. **Discount expires before use:**
   - Show warning in dashboard
   - Optional: Send reminder notification

3. **Order cancelled after stamp awarded:**
   - Deduct stamp if order cancelled within 24 hours
   - Log reversal in transaction history

4. **Tier downgrade (if implemented):**
   - Usually tiers don't downgrade
   - Alternative: "Maintain tier with X orders/year"

---

## 📝 Bahagian 6: Pelan Pelaksanaan

### Phase 1: Guest Checkout (Minggu 1)

**Hari 1-2:**
- [ ] Create migration for guest fields in orders
- [ ] Update Order model with guest methods
- [ ] Update routes to allow public menu access

**Hari 3-4:**
- [ ] Update BuyerController for guest handling
- [ ] Update OrderController for guest orders
- [ ] Create guest tracking page

**Hari 5:**
- [ ] Update checkout UI with guest option
- [ ] Update order detail views for guest info
- [ ] Testing & bug fixes

### Phase 2: Basic Loyalty - Stamp Card (Minggu 2)

**Hari 1-2:**
- [ ] Create migrations for loyalty fields
- [ ] Create LoyaltyService class
- [ ] Create LoyaltySetting model

**Hari 3-4:**
- [ ] Integrate with order completion flow
- [ ] Update checkout for discount application
- [ ] Create loyalty card UI component

**Hari 5:**
- [ ] Owner settings page for loyalty config
- [ ] Testing all scenarios
- [ ] Documentation update

### Phase 3: Advanced - Tier System (Minggu 3 - Optional)

- [ ] Implement tier upgrade logic
- [ ] Create tier badges and visuals
- [ ] Add tier bonus calculations
- [ ] Email/notification for tier upgrades

---

## 💰 Bahagian 7: Kos & ROI

### Development Cost (Anggaran)

| Item | Hours | Cost (RM 50/hr) |
|------|-------|-----------------|
| Guest Checkout | 16 hrs | RM 800 |
| Stamp Card Loyalty | 12 hrs | RM 600 |
| Tier System | 10 hrs | RM 500 |
| Testing & Polish | 8 hrs | RM 400 |
| **Total** | 46 hrs | **RM 2,300** |

### Expected ROI

| Metric | Monthly Value |
|--------|---------------|
| New orders from reduced friction | +30 orders × RM 15 = RM 450 |
| Repeat orders from loyalty | +15 orders × RM 15 = RM 225 |
| Less discount cost | -10 discounts × RM 3 = -RM 30 |
| **Net Monthly Gain** | **RM 645** |
| **Payback Period** | ~3.5 months |

---

## ✅ Bahagian 8: Cadangan Keputusan

### Recommendation: Proceed with Guest Checkout + Basic Loyalty

**Justification:**
1. Guest Checkout sangat kritikal untuk conversion
2. Stamp Card mudah faham dan implement
3. Tier System boleh ditambah kemudian
4. ROI positif dalam masa 4 bulan

### Proposed Settings (Default)

```php
// Guest Checkout
'guest_enabled' => true,
'guest_pending_limit' => 3, // Max pending orders per phone

// Loyalty - Stamp Card
'stamps_required' => 5,
'stamp_discount_percent' => 10, // 10% off
'discount_validity_days' => 30,

// Loyalty - Tiers (Disabled initially)
'tiers_enabled' => false,
'silver_threshold' => 10,
'gold_threshold' => 25,
```

---

## 🎯 Bahagian 9: Kesimpulan

### Apa yang akan berubah untuk Pelanggan:

**Pelanggan Baru (Guest):**
- Boleh lihat menu tanpa login ✅
- Boleh order dengan hanya isi nama, phone, alamat ✅
- Dapat tracking link untuk check status ✅
- Digalakkan untuk register dengan insentif loyalty ✅

**Pelanggan Berdaftar:**
- Semua kelebihan guest, PLUS:
- Kumpul "stamp" setiap order ✅
- Dapat 10% diskaun selepas 5 orders ✅
- Lihat loyalty card dalam dashboard ✅
- Order history tersimpan ✅

**Pemilik (Owner):**
- Lebih ramai pelanggan (reduced friction) ✅
- Data pelanggan untuk remarketing ✅
- Loyalty settings boleh diubah ✅
- Dapat lihat loyalty analytics ✅

---

**Dokumen ini adalah analisis lengkap. Sila sahkan untuk mula pembangunan.**

---

*Disediakan pada: 3 Januari 2026*  
*Untuk: D'house Waffle*  
*Status: Menunggu Kelulusan*

