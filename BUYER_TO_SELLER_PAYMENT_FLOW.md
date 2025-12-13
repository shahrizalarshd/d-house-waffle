# Buyer to Seller Payment Flow
**Bagaimana Duit Bergerak Dalam Sistem**

---

## 💰 THE BIG QUESTION

**"Buyer bayar kepada seller macam mana bila guna Billplz?"**

---

## 🎯 JAWAPAN RINGKAS

Ada **3 APPROACH** yang boleh digunakan:

### **Approach 1: Platform as Payment Receiver (RECOMMENDED)**
```
Buyer → Billplz → Platform Bank → Manual Payout → Seller Bank
```
**Best for MVP & current implementation**

### **Approach 2: Billplz Split Payment (ADVANCED)**
```
Buyer → Billplz → [Auto Split] → Platform (5%) + Seller (95%)
```
**Requires Billplz Enterprise**

### **Approach 3: Direct to Seller (NOT RECOMMENDED)**
```
Buyer → Billplz → Seller Bank (100%)
Seller owes platform 5% (hard to collect!)
```

---

## 📊 APPROACH 1: PLATFORM RECEIVES ALL (Current System)

### **Money Flow:**

```
Step 1: Buyer Checkout
┌─────────────────────────────┐
│ Buyer places order          │
│ Total: RM 100               │
│ - Items: RM 100             │
│ - Platform fee: RM 5 (5%)   │
│ - Seller gets: RM 95        │
└──────────────┬──────────────┘
               ↓
Step 2: Payment
┌─────────────────────────────┐
│ Buyer redirected to Billplz │
│ Pays RM 100 via FPX/Card    │
└──────────────┬──────────────┘
               ↓
Step 3: Money Goes to Platform
┌─────────────────────────────┐
│ Billplz Account:            │
│ Platform's Bank Account     │
│ Receives: RM 100            │
└──────────────┬──────────────┘
               ↓
Step 4: Platform Holds Money
┌─────────────────────────────┐
│ Order status: Paid          │
│ Seller prepares order       │
│ Buyer picks up              │
└──────────────┬──────────────┘
               ↓
Step 5: Payout to Seller (Weekly/Monthly)
┌─────────────────────────────┐
│ Platform does bank transfer │
│ Amount: RM 95               │
│ To: Seller's bank account   │
│                             │
│ Platform keeps: RM 5        │
└─────────────────────────────┘
```

### **Implementation Details:**

#### **A. Billplz Setup:**

```
Billplz Account: Platform's account
Bank Account: Platform's bank account (one account only)

When buyer pays RM 100:
  ↓
Money goes to: Platform bank account
Who receives: Platform
Amount: RM 100 (full amount)
```

#### **B. Order Record in Database:**

```sql
orders table:
- total_amount: 100.00 (buyer paid)
- platform_fee: 5.00 (platform's cut)
- seller_amount: 95.00 (what seller will get)
- payment_status: 'paid'
```

#### **C. Payout Process:**

**Weekly/Monthly Payout:**

```php
// Generate payout report
$orders = Order::where('seller_id', $sellerId)
    ->where('payment_status', 'paid')
    ->where('payout_status', 'pending')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->get();

$totalSellerAmount = $orders->sum('seller_amount');

// Platform admin does bank transfer
// RM 95.00 → Seller's bank account

// Mark as paid out
$orders->each(function($order) {
    $order->update(['payout_status' => 'paid']);
});
```

### **Pros & Cons:**

**✅ Pros:**
- Simple to implement
- No need Billplz Enterprise
- Platform has control
- Can handle disputes
- Track everything easily

**❌ Cons:**
- Manual bank transfers needed
- Seller tunggu seminggu/sebulan
- Platform temporarily holds money
- Admin workload (transfer every week)

---

## 📊 APPROACH 2: BILLPLZ SPLIT PAYMENT (Future Upgrade)

### **Money Flow:**

```
Step 1: Buyer Checkout
┌─────────────────────────────┐
│ Order: RM 100               │
│ Platform fee: RM 5          │
│ Seller amount: RM 95        │
└──────────────┬──────────────┘
               ↓
Step 2: Create Bill with Split
┌─────────────────────────────┐
│ Billplz API Call:           │
│ {                           │
│   amount: 10000, (RM 100)   │
│   split_payment: {          │
│     platform: 500,  (RM 5)  │
│     seller: 9500   (RM 95)  │
│   }                         │
│ }                           │
└──────────────┬──────────────┘
               ↓
Step 3: Buyer Pays
┌─────────────────────────────┐
│ Buyer pays RM 100           │
│ via Billplz                 │
└──────────────┬──────────────┘
               ↓
Step 4: Automatic Split
┌──────────────────┬──────────┐
│  Platform Bank   │ Seller   │
│  Gets RM 5       │ Gets RM  │
│  automatically   │ 95 auto  │
└──────────────────┴──────────┘
                ↓
Step 5: Settlement (T+2 days)
┌─────────────────────────────┐
│ Both receive money in 2-3   │
│ working days                │
│ NO manual transfer needed!  │
└─────────────────────────────┘
```

### **Implementation:**

```php
use Illuminate\Support\Facades\Http;

public function createBillWithSplit(Order $order)
{
    $settings = PlatformSetting::getBillplzSettings();
    $seller = $order->seller;
    
    // Seller must have registered Billplz bank account
    if (!$seller->billplz_bank_account_id) {
        throw new \Exception('Seller bank account not registered');
    }
    
    $response = Http::withBasicAuth($settings['api_key'], '')
        ->post('https://www.billplz.com/api/v3/bills', [
            'collection_id' => $settings['collection_id'],
            'email' => $order->buyer->email,
            'name' => $order->buyer->name,
            'amount' => $order->total_amount * 100, // Convert to cents
            'description' => "Order {$order->order_no}",
            
            // SPLIT PAYMENT CONFIGURATION
            'split_payment' => [
                'email' => config('app.platform_email'),
                'split_payments' => [
                    [
                        // Platform gets fee
                        'bank_account_id' => $settings['platform_bank_account'],
                        'fixed_cut' => $order->platform_fee * 100,
                        'split_header' => 'Platform Fee',
                    ],
                    [
                        // Seller gets amount
                        'bank_account_id' => $seller->billplz_bank_account_id,
                        'fixed_cut' => $order->seller_amount * 100,
                        'split_header' => 'Seller Payment',
                    ],
                ],
            ],
            
            'callback_url' => route('webhook.billplz'),
            'redirect_url' => route('buyer.order.detail', $order->id),
        ]);
        
    return $response->json();
}
```

### **Requirements:**

**1. Billplz Enterprise Account**
```
Cost: ~RM 500/month
Features: Split payment, advanced features
```

**2. Seller Bank Account Registration**
```php
// Add to users table
Schema::table('users', function (Blueprint $table) {
    $table->string('bank_name')->nullable();
    $table->string('bank_account_no')->nullable();
    $table->string('bank_account_name')->nullable();
    $table->string('billplz_bank_account_id')->nullable();
});
```

**3. Seller Onboarding Flow**
```
1. Seller applies
2. Admin approves
3. Seller adds bank details
4. Billplz verifies bank account
5. Account activated
6. Can receive split payments
```

### **Pros & Cons:**

**✅ Pros:**
- Fully automatic!
- No manual transfers
- Seller gets money fast (T+2)
- Transparent
- Scales well

**❌ Cons:**
- Need Billplz Enterprise (~RM 500/month)
- Each seller must register bank with Billplz
- More complex setup
- KYC verification for each seller

---

## 📊 APPROACH 3: DIRECT TO SELLER (NOT RECOMMENDED)

### **Money Flow:**

```
Buyer → Billplz → Seller Bank (RM 100)
                      ↓
        Seller owes platform RM 5 (how to collect?)
```

### **Why NOT Recommended:**

**❌ Problems:**
1. Platform can't collect fee automatically
2. Seller might not pay fee
3. Hard to enforce payment
4. Need monthly invoicing
5. Chase sellers for payment
6. Legal issues if seller doesn't pay

**Only works if:**
- Fee is 0% (campaign mode)
- Or use subscription model instead

---

## 🎯 RECOMMENDED FLOW FOR YOUR SYSTEM

### **Current Stage (MVP):**

**USE APPROACH 1: Platform Receives All**

```
Implementation Steps:

1. Setup Billplz with platform's bank account
2. Buyer pays → Money to platform
3. Platform tracks seller amounts in database
4. Weekly/monthly payout to sellers
5. Simple & works!
```

**Code Already Ready:**
```php
// OrderController.php already calculates:
$platformFee = $totalAmount * ($apartment->service_fee_percent / 100);
$sellerAmount = $totalAmount - $platformFee;

// Saves in database
Order::create([
    'total_amount' => $totalAmount,
    'platform_fee' => $platformFee,
    'seller_amount' => $sellerAmount,
]);
```

### **Future Stage (When Scale Up):**

**UPGRADE TO APPROACH 2: Split Payment**

```
When to upgrade:
- More than 50 sellers
- More than 500 orders/month
- Manual payout too time-consuming
- Can afford RM 500/month for Enterprise
```

---

## 💻 COMPLETE IMPLEMENTATION GUIDE

### **Approach 1 (Current): Step-by-Step**

#### **Step 1: Setup Billplz**

```
1. Sign up at Billplz.com
2. Add PLATFORM's bank account
3. Create collection
4. Get API credentials
5. Configure in super admin settings
```

#### **Step 2: Order & Payment Creation**

```php
// OrderController.php - placeOrder()

// Create order with fee breakdown
$order = Order::create([
    'total_amount' => 100.00,
    'platform_fee' => 5.00,    // Platform's cut
    'seller_amount' => 95.00,   // Seller will get this
    'payment_status' => 'pending',
]);

// Create Billplz bill
$bill = $this->billplzService->createBill($order);

// Redirect buyer to Billplz payment page
return redirect($bill['url']);
```

#### **Step 3: Buyer Pays**

```
1. Buyer redirected to Billplz
2. Chooses payment method (FPX/Card/Wallet)
3. Completes payment
4. Money goes to platform's bank account
5. Billplz sends webhook to system
```

#### **Step 4: Webhook Updates Order**

```php
// PaymentWebhookController.php

public function billplz(Request $request)
{
    $billId = $request->input('id');
    $paid = $request->input('paid');
    
    $payment = Payment::where('reference_no', $billId)->first();
    
    if ($paid === 'true') {
        $payment->update(['status' => 'paid']);
        $payment->order->update(['payment_status' => 'paid']);
        
        // Order now visible to seller
        // Seller can start preparing
    }
}
```

#### **Step 5: Seller Sees Order**

```
Seller Dashboard:
┌────────────────────────────┐
│ New Order!                 │
│ Order #ORD-123             │
│ Amount: RM 100             │
│ Your Earning: RM 95        │
│ Platform Fee: RM 5         │
│ Status: Paid ✅            │
│                            │
│ [Prepare Order]            │
└────────────────────────────┘
```

#### **Step 6: Track Payouts**

```php
// Create payout tracking table
Schema::create('seller_payouts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('seller_id');
    $table->decimal('amount', 10, 2);
    $table->date('period_start');
    $table->date('period_end');
    $table->integer('order_count');
    $table->enum('status', ['pending', 'processing', 'paid']);
    $table->string('payment_method')->nullable(); // bank_transfer, etc
    $table->string('reference_no')->nullable();
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();
});

// Weekly payout generation (cron job)
$payouts = DB::table('orders')
    ->select('seller_id', DB::raw('SUM(seller_amount) as total'))
    ->where('payment_status', 'paid')
    ->where('payout_status', 'pending')
    ->groupBy('seller_id')
    ->get();

foreach ($payouts as $payout) {
    SellerPayout::create([
        'seller_id' => $payout->seller_id,
        'amount' => $payout->total,
        'period_start' => now()->startOfWeek(),
        'period_end' => now()->endOfWeek(),
        'status' => 'pending',
    ]);
}
```

#### **Step 7: Admin Does Bank Transfer**

```
Admin Panel:
┌────────────────────────────────────┐
│ Pending Payouts                    │
├────────────────────────────────────┤
│ Seller: Ahmad                      │
│ Amount: RM 285.00                  │
│ Orders: 15                         │
│ Bank: Maybank - 1234567890        │
│ [Mark as Paid]                     │
├────────────────────────────────────┤
│ Seller: Fatimah                    │
│ Amount: RM 450.00                  │
│ Orders: 23                         │
│ Bank: CIMB - 9876543210           │
│ [Mark as Paid]                     │
└────────────────────────────────────┘

Admin does:
1. Login to platform bank account
2. Transfer RM 285 to Ahmad
3. Transfer RM 450 to Fatimah
4. Click "Mark as Paid" in system
5. Done!
```

---

## 📊 MONEY TRAIL EXAMPLE

### **Scenario: 3 Orders in a Week**

```
Order 1:
Buyer A → RM 50 → Platform Bank
  Platform fee: RM 2.50 (5%)
  Seller (Ahmad) will get: RM 47.50

Order 2:
Buyer B → RM 100 → Platform Bank
  Platform fee: RM 5.00 (5%)
  Seller (Ahmad) will get: RM 95.00

Order 3:
Buyer C → RM 80 → Platform Bank
  Platform fee: RM 4.00 (5%)
  Seller (Fatimah) will get: RM 76.00
```

**Platform Bank Balance:**
```
Total received: RM 230.00

Breakdown:
- Platform fee earned: RM 11.50
- Owed to Ahmad: RM 142.50 (47.50 + 95.00)
- Owed to Fatimah: RM 76.00

Total owed to sellers: RM 218.50
Platform keeps: RM 11.50
```

**Weekly Payout:**
```
Platform transfers:
- RM 142.50 → Ahmad's bank
- RM 76.00 → Fatimah's bank

Platform balance after payout:
- RM 11.50 (platform revenue)
```

---

## ⚠️ IMPORTANT CONSIDERATIONS

### **1. Cash Flow Management**

```
Platform must have enough cash:
- Receive RM 230
- Pay out RM 218.50
- Keep RM 11.50

Never pay out before receiving!
```

### **2. Dispute Handling**

```
If buyer disputes:
- Money still with platform
- Can refund easily
- Seller doesn't get paid for disputed order
- Good!
```

### **3. Payout Schedule**

```
Recommended:
- Weekly: Good balance
- Bi-weekly: Less admin work
- Monthly: Too long for sellers

Never:
- Daily: Too much work
- Per order: Expensive bank fees
```

### **4. Bank Transfer Fees**

```
Most banks:
- Own bank: Free
- Other bank: RM 1-2 per transfer

Solution:
- Batch transfers
- Use same bank for platform & sellers
- Or pay for business account (unlimited transfers)
```

---

## 🎯 FINAL RECOMMENDATION

### **For Your Current System:**

**IMPLEMENT APPROACH 1 NOW:**

1. ✅ Billplz with platform bank account (simplest)
2. ✅ Track seller amounts in database (already done!)
3. ✅ Weekly/monthly manual payout (manageable)
4. ✅ Can launch immediately
5. ✅ No additional costs

**UPGRADE TO APPROACH 2 LATER:**

When:
- More than 50 sellers
- More than 500 orders/month
- Revenue can cover RM 500/month Enterprise fee
- Manual payout becomes burden

Benefits:
- Automatic split
- Faster payout to sellers
- Less admin work
- More professional

---

## 📞 IMPLEMENTATION CHECKLIST

### **Today (MVP):**

- [x] Order calculates platform_fee & seller_amount
- [x] Payment records created
- [x] Billplz integration points ready
- [ ] Create BillplzService to create bills
- [ ] Setup actual Billplz account
- [ ] Test payment flow
- [ ] Create payout tracking table
- [ ] Build admin payout UI
- [ ] Document bank transfer process

### **Later (Scale):**

- [ ] Evaluate Billplz Enterprise
- [ ] Add seller bank account fields
- [ ] Build seller bank registration flow
- [ ] Implement split payment API
- [ ] Test split payment
- [ ] Migrate from manual to auto payout

---

## ✅ SUMMARY

### **Question: Buyer bayar kepada seller macam mana?**

### **Answer:**

**Current System (Recommended for MVP):**
```
Buyer pays RM 100
    ↓
Goes to Platform bank (via Billplz)
    ↓
Platform holds money
    ↓
Weekly/monthly transfer RM 95 to seller
    ↓
Platform keeps RM 5
```

**Future Upgrade (When scale):**
```
Buyer pays RM 100
    ↓
Billplz automatically splits:
  → RM 5 to Platform bank
  → RM 95 to Seller bank
    ↓
Both receive in 2-3 days
```

**Why Current Approach is Best:**
1. ✅ Simple to implement
2. ✅ No extra costs
3. ✅ Launch faster
4. ✅ Learn from usage
5. ✅ Upgrade later when needed

**Your system is READY for this flow!** 🎉

---

**Document Created:** 2025-12-13  
**Version:** 1.0

