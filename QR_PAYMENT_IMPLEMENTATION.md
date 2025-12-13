# QR Payment Implementation Guide
**QR Pay for POS Apartment Marketplace**

---

## 📱 QR PAYMENT OVERVIEW

### **What is QR Pay?**

QR Payment allows buyers to scan a QR code and pay using:
- 💳 DuitNow QR (all Malaysian banks)
- 💰 E-wallets (Touch n Go, Boost, GrabPay, ShopeePay)
- 📱 Mobile banking apps

**Perfect for Malaysia market! 🇲🇾**

---

## 🎯 TWO APPROACHES

### **Approach A: Static QR (Simple - RECOMMENDED)**

```
Seller has ONE QR code
Buyer scans → Pays any amount → Screenshot proof
Seller verifies → Confirms payment

Pros:
✅ Very simple
✅ No integration needed
✅ Seller keeps 100%
✅ Works immediately

Cons:
❌ Manual verification
❌ Need proof of payment
❌ Can have disputes
```

### **Approach B: Dynamic QR (Advanced)**

```
System generates unique QR per order
Buyer scans → Auto amount → Auto verification

Pros:
✅ Automated
✅ No disputes
✅ Track everything
✅ Professional

Cons:
❌ Need payment gateway integration
❌ Transaction fees apply
❌ More complex
```

---

## 💻 APPROACH A: STATIC QR (Let's Implement This!)

### **How It Works:**

```
Step 1: Seller Setup
Seller uploads their QR code image
(DuitNow QR from their bank app)

Step 2: Buyer Checkout
Buyer selects "QR Payment"
System shows seller's QR code

Step 3: Buyer Pays
Buyer scans with any app
Pays the amount
Takes screenshot

Step 4: Buyer Uploads Proof
Upload payment screenshot
Submit order

Step 5: Seller Verifies
Seller checks bank statement
Sees RM XX.XX received
Confirms payment
Order completed! ✅
```

---

## 📊 DATABASE CHANGES

### **Add QR Code to Users Table:**

```php
// Migration: add_qr_code_to_users
Schema::table('users', function (Blueprint $table) {
    $table->string('qr_code_image')->nullable()->after('phone');
    $table->string('qr_code_type')->nullable(); // duitnow, tng, boost, etc
});
```

### **Add Payment Proof to Orders:**

```php
// Migration: add_payment_proof_to_orders
Schema::table('orders', function (Blueprint $table) {
    $table->string('payment_proof')->nullable()->after('payment_ref');
    $table->text('payment_notes')->nullable();
});
```

---

## 💻 IMPLEMENTATION CODE

### **1. Update Migration**

```php
// database/migrations/2025_12_13_170000_add_qr_payment_fields.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add QR code to sellers
        Schema::table('users', function (Blueprint $table) {
            $table->string('qr_code_image')->nullable()->after('phone');
            $table->string('qr_code_type')->nullable()->after('qr_code_image');
            $table->text('qr_code_instructions')->nullable();
        });

        // Add payment proof to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('payment_ref');
            $table->text('payment_notes')->nullable();
        });

        // Update payment_method enum to include qr
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('online', 'cash', 'qr') DEFAULT 'online'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['qr_code_image', 'qr_code_type', 'qr_code_instructions']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'payment_notes']);
        });

        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('online', 'cash') DEFAULT 'online'");
    }
};
```

### **2. Update Order Model**

```php
// app/Models/Order.php

protected $fillable = [
    // ... existing fields
    'payment_proof',
    'payment_notes',
];

public function isQRPayment()
{
    return $this->payment_method === 'qr';
}

public function hasPaymentProof()
{
    return !empty($this->payment_proof);
}
```

### **3. Update User Model**

```php
// app/Models/User.php

protected $fillable = [
    // ... existing fields
    'qr_code_image',
    'qr_code_type',
    'qr_code_instructions',
];

public function hasQRCode()
{
    return !empty($this->qr_code_image);
}

public function getQRCodeUrl()
{
    return $this->qr_code_image 
        ? asset('storage/' . $this->qr_code_image)
        : null;
}
```

### **4. Checkout View with QR Option**

```blade
<!-- resources/views/buyer/checkout.blade.php -->

<div class="space-y-3">
    <!-- Online Payment -->
    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-blue-500 transition payment-method-option" data-method="online">
        <input type="radio" name="payment_method" value="online" checked class="w-5 h-5 text-blue-600">
        <div class="ml-3 flex-1">
            <div class="font-semibold text-gray-800">Online Payment</div>
            <div class="text-sm text-gray-600">Pay via FPX, Card, or E-wallet</div>
        </div>
        <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
    </label>

    <!-- QR Payment -->
    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition payment-method-option" data-method="qr">
        <input type="radio" name="payment_method" value="qr" class="w-5 h-5 text-purple-600">
        <div class="ml-3 flex-1">
            <div class="font-semibold text-gray-800">QR Payment</div>
            <div class="text-sm text-gray-600">Scan QR & pay with any app</div>
        </div>
        <i class="fas fa-qrcode text-purple-600 text-2xl"></i>
    </label>

    <!-- Cash Payment -->
    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-green-500 transition payment-method-option" data-method="cash">
        <input type="radio" name="payment_method" value="cash" class="w-5 h-5 text-green-600">
        <div class="ml-3 flex-1">
            <div class="font-semibold text-gray-800">Cash on Pickup</div>
            <div class="text-sm text-gray-600">Pay cash to seller</div>
        </div>
        <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
    </label>
</div>

<!-- QR Payment Instructions -->
<div id="qr-payment-note" class="bg-purple-50 border border-purple-200 p-4 rounded-lg mt-4 hidden">
    <div class="flex items-start">
        <i class="fas fa-qrcode text-purple-600 mt-1 mr-3 text-2xl"></i>
        <div class="text-sm text-purple-800">
            <strong>QR Payment Instructions:</strong>
            <ul class="list-disc ml-5 mt-2 space-y-1">
                <li>You will see seller's QR code after placing order</li>
                <li>Scan with any e-wallet or banking app</li>
                <li>Pay exact amount: <strong id="qr-total">RM 0.00</strong></li>
                <li>Upload payment screenshot as proof</li>
                <li>Seller will verify and confirm</li>
            </ul>
        </div>
    </div>
</div>
```

### **5. Order Placement with QR**

```php
// app/Http/Controllers/OrderController.php

public function placeOrder(Request $request)
{
    $validated = $request->validate([
        'cart' => 'required|array',
        'cart.*.product_id' => 'required|exists:products,id',
        'cart.*.quantity' => 'required|integer|min:1',
        'payment_method' => 'required|in:online,cash,qr',
    ]);

    // ... order creation code ...

    foreach ($groupedBySeller as $sellerId => $items) {
        // ... calculate amounts ...

        // Check if seller has QR code (for QR payment)
        if ($validated['payment_method'] === 'qr') {
            $seller = User::find($sellerId);
            if (!$seller->hasQRCode()) {
                return back()->with('error', 
                    "Seller {$seller->name} does not accept QR payments yet."
                );
            }
        }

        $order = Order::create([
            // ... existing fields ...
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
        ]);

        // ... create order items ...

        // No Payment record for cash or QR
        if ($validated['payment_method'] === 'online') {
            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'billplz',
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);
        }

        $orders[] = $order;
    }

    // Redirect based on payment method
    if ($validated['payment_method'] === 'qr' && count($orders) === 1) {
        return redirect()->route('orders.qr-payment', $orders[0]->id);
    }

    // ... other redirects ...
}

// Show QR payment page
public function showQRPayment($id)
{
    $order = Order::with(['seller', 'items'])
        ->where('buyer_id', auth()->id())
        ->findOrFail($id);

    if ($order->payment_method !== 'qr') {
        return redirect()->route('buyer.order.detail', $id);
    }

    return view('buyer.qr-payment', compact('order'));
}

// Upload payment proof
public function uploadPaymentProof(Request $request, $id)
{
    $order = Order::where('buyer_id', auth()->id())
        ->findOrFail($id);

    $validated = $request->validate([
        'payment_proof' => 'required|image|max:5120', // 5MB max
        'payment_notes' => 'nullable|string|max:500',
    ]);

    // Store payment proof image
    $path = $request->file('payment_proof')->store('payment-proofs', 'public');

    $order->update([
        'payment_proof' => $path,
        'payment_notes' => $validated['payment_notes'] ?? null,
    ]);

    // Notify seller
    // Mail::to($order->seller)->send(new PaymentProofUploaded($order));

    return redirect()->route('buyer.order.detail', $order->id)
        ->with('success', 'Payment proof uploaded! Waiting for seller verification.');
}
```

### **6. QR Payment View**

```blade
<!-- resources/views/buyer/qr-payment.blade.php -->
@extends('layouts.app')

@section('title', 'QR Payment')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-qrcode mr-2"></i>QR Payment
    </h1>

    <!-- Order Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold mb-3">Order: {{ $order->order_no }}</h3>
        
        @foreach($order->items as $item)
        <div class="flex justify-between text-sm mb-2">
            <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
            <span>RM {{ number_format($item->subtotal, 2) }}</span>
        </div>
        @endforeach

        <div class="border-t pt-3 mt-3">
            <div class="flex justify-between text-xl font-bold text-purple-600">
                <span>Total to Pay:</span>
                <span>RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- QR Code Display -->
    <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-6 mb-6 text-center">
        <h3 class="font-bold text-purple-900 mb-4">Scan QR Code to Pay</h3>
        
        @if($order->seller->hasQRCode())
        <div class="bg-white p-4 rounded-lg inline-block mb-4">
            <img src="{{ $order->seller->getQRCodeUrl() }}" 
                 alt="QR Code" 
                 class="w-64 h-64 object-contain">
        </div>

        <div class="text-sm text-purple-800 mb-4">
            <p class="font-semibold mb-2">{{ $order->seller->qr_code_type ? ucfirst($order->seller->qr_code_type) : 'DuitNow' }} QR Code</p>
            @if($order->seller->qr_code_instructions)
            <p class="text-xs">{{ $order->seller->qr_code_instructions }}</p>
            @endif
        </div>

        <div class="bg-white p-4 rounded-lg text-left">
            <p class="font-semibold mb-2">Payment Steps:</p>
            <ol class="list-decimal ml-5 space-y-1 text-sm">
                <li>Open your banking app or e-wallet</li>
                <li>Select "Scan QR" or "Pay"</li>
                <li>Scan the QR code above</li>
                <li>Enter amount: <strong>RM {{ number_format($order->total_amount, 2) }}</strong></li>
                <li>Confirm payment</li>
                <li>Take screenshot of receipt</li>
                <li>Upload screenshot below</li>
            </ol>
        </div>
        @else
        <div class="bg-red-50 border border-red-200 p-4 rounded">
            <p class="text-red-800">Seller has not setup QR payment yet.</p>
        </div>
        @endif
    </div>

    <!-- Upload Payment Proof -->
    @if($order->seller->hasQRCode() && !$order->hasPaymentProof())
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold mb-4">Upload Payment Proof</h3>

        <form method="POST" action="{{ route('orders.upload-proof', $order->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    Payment Screenshot *
                </label>
                <input type="file" 
                       name="payment_proof" 
                       accept="image/*" 
                       capture="camera"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-600 mt-1">
                    Upload screenshot showing payment confirmation
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    Notes (Optional)
                </label>
                <textarea name="payment_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                          placeholder="Transaction reference, time, etc..."></textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                <i class="fas fa-upload mr-2"></i>Upload Payment Proof
            </button>
        </form>
    </div>
    @elseif($order->hasPaymentProof())
    <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
        <p class="text-green-800 font-semibold mb-2">
            <i class="fas fa-check-circle mr-2"></i>Payment Proof Uploaded!
        </p>
        <p class="text-sm text-green-700">
            Waiting for seller to verify payment...
        </p>
    </div>
    @endif
</div>
@endsection
```

### **7. Seller Verification**

```blade
<!-- resources/views/seller/orders.blade.php - Add QR section -->

@elseif($order->payment_method === 'qr')
<!-- QR payment status -->
@if($order->payment_status === 'pending')
    @if($order->hasPaymentProof())
    <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg mb-4">
        <p class="text-sm text-purple-800 mb-3">
            <i class="fas fa-qrcode mr-2"></i>
            <strong>QR Payment Proof Received</strong><br>
            Please verify payment in your bank statement
        </p>

        <!-- Show payment proof -->
        <div class="bg-white p-3 rounded mb-3">
            <p class="text-xs text-gray-600 mb-2">Payment Proof:</p>
            <img src="{{ asset('storage/' . $order->payment_proof) }}" 
                 alt="Payment Proof" 
                 class="max-w-full h-auto rounded border cursor-pointer"
                 onclick="window.open(this.src, '_blank')">
            @if($order->payment_notes)
            <p class="text-xs text-gray-600 mt-2">
                <strong>Notes:</strong> {{ $order->payment_notes }}
            </p>
            @endif
        </div>

        <div class="flex gap-2">
            <form method="POST" action="{{ route('seller.orders.verify-payment', $order->id) }}" class="flex-1">
                @csrf
                <button type="submit" 
                        class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 text-sm"
                        onclick="return confirm('Confirm payment received in your account?')">
                    <i class="fas fa-check-circle mr-1"></i>Verify & Complete
                </button>
            </form>
            <form method="POST" action="{{ route('seller.orders.reject-payment', $order->id) }}" class="flex-1">
                @csrf
                <button type="submit" 
                        class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 text-sm"
                        onclick="return confirm('Reject payment? Buyer will be notified.')">
                    <i class="fas fa-times-circle mr-1"></i>Reject
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mb-4">
        <p class="text-sm text-yellow-800">
            <i class="fas fa-clock mr-2"></i>Waiting for buyer to upload payment proof
        </p>
    </div>
    @endif
@else
<div class="bg-green-50 border border-green-200 p-3 rounded-lg mb-4">
    <p class="text-sm text-green-800">
        <i class="fas fa-check-circle mr-2"></i>QR Payment verified
    </p>
</div>
@endif
@endif
```

### **8. Seller QR Code Setup**

```blade
<!-- resources/views/seller/profile.blade.php -->

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="font-bold text-lg mb-4">QR Payment Setup</h3>

    <form method="POST" action="{{ route('seller.update-qr') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">
                QR Code Type
            </label>
            <select name="qr_code_type" class="w-full px-3 py-2 border rounded-lg">
                <option value="duitnow" {{ auth()->user()->qr_code_type === 'duitnow' ? 'selected' : '' }}>
                    DuitNow QR (All Banks)
                </option>
                <option value="tng" {{ auth()->user()->qr_code_type === 'tng' ? 'selected' : '' }}>
                    Touch n Go eWallet
                </option>
                <option value="boost" {{ auth()->user()->qr_code_type === 'boost' ? 'selected' : '' }}>
                    Boost
                </option>
                <option value="grabpay" {{ auth()->user()->qr_code_type === 'grabpay' ? 'selected' : '' }}>
                    GrabPay
                </option>
                <option value="shopeepay" {{ auth()->user()->qr_code_type === 'shopeepay' ? 'selected' : '' }}>
                    ShopeePay
                </option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">
                Upload QR Code Image
            </label>

            @if(auth()->user()->hasQRCode())
            <div class="mb-3">
                <img src="{{ auth()->user()->getQRCodeUrl() }}" 
                     alt="Current QR" 
                     class="w-48 h-48 object-contain border rounded p-2">
            </div>
            @endif

            <input type="file" 
                   name="qr_code_image" 
                   accept="image/*"
                   class="w-full px-3 py-2 border rounded-lg">
            <p class="text-xs text-gray-600 mt-1">
                Get your QR code from your banking app (Settings → QR Code)
            </p>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">
                Instructions for Buyers (Optional)
            </label>
            <textarea name="qr_code_instructions" 
                      rows="3"
                      class="w-full px-3 py-2 border rounded-lg"
                      placeholder="e.g., Please include order number in payment notes">{{ auth()->user()->qr_code_instructions }}</textarea>
        </div>

        <button type="submit" 
                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
            Save QR Code
        </button>
    </form>
</div>
```

---

## 🎯 FLOW SUMMARY

### **Complete QR Payment Flow:**

```
1. SELLER SETUP
   ↓
Seller uploads QR code image from bank app

2. BUYER CHECKOUT
   ↓
Select "QR Payment"
Place Order

3. SHOW QR CODE
   ↓
System displays seller's QR code
Shows exact amount to pay

4. BUYER PAYS
   ↓
Scans QR with banking app
Pays RM XX.XX
Takes screenshot

5. UPLOAD PROOF
   ↓
Buyer uploads payment screenshot
Adds optional notes

6. SELLER VERIFIES
   ↓
Seller checks bank statement
Sees money received
Verifies payment proof
Clicks "Verify & Complete"

7. ORDER COMPLETED! ✅
```

---

## ✅ BENEFITS

### **For Buyers:**
✅ Instant payment (2-3 seconds)
✅ Use any app (bank/e-wallet)
✅ No cash needed
✅ Convenient & fast
✅ Secure (official QR)

### **For Sellers:**
✅ Get money immediately
✅ No cash handling
✅ Money goes to bank directly
✅ Track via bank statement
✅ No gateway fees

### **For Platform:**
✅ Modern payment option
✅ Appeal to tech-savvy users
✅ No transaction fees
✅ Simple to implement

---

## 🎯 RECOMMENDATION

### **Best Setup:**

```
Payment Options:
1. ✅ Cash on Pickup (Simple, community)
2. ✅ QR Payment (Fast, modern)
3. ✅ Online Payment (Automated, future)

Let buyers choose! 🎉
```

### **Seller Requirements:**

```
To accept QR payments:
- Have DuitNow QR code (free from any bank)
- Upload QR code image to profile
- Check bank statement for payments
- Verify payment proofs
```

---

## ⚠️ IMPORTANT NOTES

### **1. Verification Process:**

```
Critical: Seller MUST verify payment in bank statement
Don't just rely on screenshot (can be fake!)
Check:
- Amount matches (RM XX.XX)
- Time is recent
- Money actually received
```

### **2. Dispute Handling:**

```
If dispute:
1. Check seller's bank statement
2. Check buyer's screenshot
3. Contact bank if needed
4. Mediate fairly
```

### **3. Payment Proof:**

```
Screenshot should show:
- Amount paid
- Date & time
- Transaction reference
- Recipient name/account
```

---

## 📊 COMPARISON

| Payment Method | Speed | Cost | Verification | Best For |
|----------------|-------|------|--------------|----------|
| **Cash** | Instant | Free | Face-to-face | Food, neighbors |
| **QR Pay** | Instant | Free | Screenshot | All products |
| **Online** | Instant | RM 1.50+ | Auto | Expensive items |

---

## 🚀 QUICK START

Want me to implement this QR payment system now?

I will:
1. ✅ Create migration
2. ✅ Update models
3. ✅ Add QR option to checkout
4. ✅ Create QR payment view
5. ✅ Add upload proof functionality
6. ✅ Seller verification system
7. ✅ Profile QR setup page

Ready? 🎯

---

**Document Created:** 2025-12-13  
**Version:** 1.0

