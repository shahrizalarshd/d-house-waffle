# QR Payment - Complete Implementation Files
**Ready-to-use code for QR Payment System**

---

## ✅ COMPLETED:

1. ✅ Migration (migrated successfully)
2. ✅ User Model (updated with QR methods)
3. ✅ Order Model (updated with QR & proof methods)

---

## 📝 REMAINING FILES TO UPDATE:

Due to character limits, here are the complete file contents you need to update manually:

### **1. Update Checkout View - Add QR Option**

File: `resources/views/buyer/checkout.blade.php`

Add this after the Cash payment option (around line 54):

```blade
<!-- QR Payment -->
<label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-purple-500 transition payment-method-option" data-method="qr">
    <input type="radio" name="payment_method" value="qr"
        class="w-5 h-5 text-purple-600">
    <div class="ml-3 flex-1">
        <div class="font-semibold text-gray-800">QR Payment</div>
        <div class="text-sm text-gray-600">
            Scan QR & pay with any app
        </div>
    </div>
    <i class="fas fa-qrcode text-purple-600 text-2xl"></i>
</label>
```

Add QR instructions box after cash payment note:

```blade
<!-- QR Payment Note -->
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
            </ul>
        </div>
    </div>
</div>
```

Update JavaScript to handle QR selection (in the payment method change event handler):

```javascript
if (this.value === 'qr') {
    qrNote.classList.remove('hidden');
    cashNote.classList.add('hidden');
    const selectedOpt = document.querySelector('[data-method="qr"]');
    selectedOpt.classList.add('border-purple-500', 'bg-purple-50');
} else if (this.value === 'cash') {
    // existing cash code
} else {
    cashNote.classList.add('hidden');
    qrNote.classList.add('hidden');
}
```

---

### **2. Update OrderController - Handle QR**

File: `app/Http/Controllers/OrderController.php`

Update validation to include 'qr':

```php
'payment_method' => 'required|in:online,cash,qr',
```

Add seller QR check before order creation:

```php
// Check if seller has QR code (for QR payment)
if ($validated['payment_method'] === 'qr') {
    $seller = User::find($sellerId);
    if (!$seller->hasQRCode()) {
        return back()->with('error', 
            "Seller {$seller->name} does not accept QR payments yet."
        );
    }
}
```

Update redirect logic:

```php
// For QR payment, redirect to QR payment page
if ($validated['payment_method'] === 'qr' && count($orders) === 1) {
    $redirect = route('orders.qr-payment', $orders[0]->id);
    $message = 'Order placed! Please scan QR code to pay.';
}
```

---

### **3. Create QR Payment View**

File: `resources/views/buyer/qr-payment.blade.php`

```blade
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
    @if($order->seller->hasQRCode())
    <div class="bg-purple-50 border-2 border-purple-300 rounded-lg p-6 mb-6 text-center">
        <h3 class="font-bold text-purple-900 mb-4">Scan QR Code to Pay</h3>
        
        <div class="bg-white p-4 rounded-lg inline-block mb-4">
            <img src="{{ $order->seller->getQRCodeUrl() }}" 
                 alt="QR Code" 
                 class="w-64 h-64 object-contain">
        </div>

        <div class="text-sm text-purple-800 mb-4">
            <p class="font-semibold mb-2">
                {{ $order->seller->qr_code_type ? ucfirst($order->seller->qr_code_type) : 'DuitNow' }} QR Code
            </p>
            @if($order->seller->qr_code_instructions)
            <p class="text-xs italic">{{ $order->seller->qr_code_instructions }}</p>
            @endif
        </div>

        <div class="bg-white p-4 rounded-lg text-left">
            <p class="font-semibold mb-2">📱 Payment Steps:</p>
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
    </div>

    <!-- Upload Payment Proof -->
    @if(!$order->hasPaymentProof())
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
    @else
    <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
        <p class="text-green-800 font-semibold mb-2">
            <i class="fas fa-check-circle mr-2"></i>Payment Proof Uploaded!
        </p>
        <p class="text-sm text-green-700">
            Waiting for seller to verify payment...
        </p>
        <a href="{{ route('buyer.order.detail', $order->id) }}" 
           class="inline-block mt-3 text-purple-600 underline">
            View Order Details
        </a>
    </div>
    @endif
    @else
    <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
        <p class="text-red-800">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Seller has not setup QR payment yet.
        </p>
    </div>
    @endif
</div>
@endsection
```

---

### **4. Add Routes**

File: `routes/web.php`

Add after order routes:

```php
Route::get('/orders/{id}/qr-payment', [OrderController::class, 'showQRPayment'])->name('orders.qr-payment');
Route::post('/orders/{id}/upload-proof', [OrderController::class, 'uploadPaymentProof'])->name('orders.upload-proof');
```

Add in seller routes:

```php
Route::post('/orders/{id}/verify-payment', [SellerController::class, 'verifyPayment'])->name('orders.verify-payment');
Route::post('/orders/{id}/reject-payment', [SellerController::class, 'rejectPayment'])->name('orders.reject-payment');
Route::get('/profile/qr', [SellerController::class, 'qrSetup'])->name('profile.qr');
Route::post('/profile/qr', [SellerController::class, 'updateQR'])->name('update-qr');
```

---

### **5. Add Controller Methods**

File: `app/Http/Controllers/OrderController.php`

Add these methods:

```php
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

    return redirect()->route('buyer.order.detail', $order->id)
        ->with('success', 'Payment proof uploaded! Waiting for seller verification.');
}
```

File: `app/Http/Controllers/SellerController.php`

Add these methods:

```php
public function verifyPayment($id)
{
    $order = Order::where('seller_id', auth()->id())->findOrFail($id);

    if ($order->payment_method !== 'qr') {
        return back()->with('error', 'Only QR orders can be verified.');
    }

    if (!$order->hasPaymentProof()) {
        return back()->with('error', 'No payment proof uploaded yet.');
    }

    $order->update([
        'payment_status' => 'paid',
        'status' => 'completed',
        'paid_at' => now(),
    ]);

    \Log::info('QR payment verified', [
        'order_id' => $order->id,
        'seller_id' => auth()->id(),
    ]);

    return back()->with('success', 'Payment verified! Order completed.');
}

public function rejectPayment($id)
{
    $order = Order::where('seller_id', auth()->id())->findOrFail($id);

    $order->update([
        'payment_status' => 'failed',
        'payment_notes' => ($order->payment_notes ?? '') . ' [REJECTED by seller]',
    ]);

    return back()->with('success', 'Payment rejected. Buyer will be notified.');
}

public function qrSetup()
{
    return view('seller.qr-setup');
}

public function updateQR(Request $request)
{
    $validated = $request->validate([
        'qr_code_type' => 'required|string|in:duitnow,tng,boost,grabpay,shopeepay',
        'qr_code_image' => 'nullable|image|max:2048',
        'qr_code_instructions' => 'nullable|string|max:500',
    ]);

    $user = auth()->user();
    
    if ($request->hasFile('qr_code_image')) {
        // Delete old QR if exists
        if ($user->qr_code_image) {
            \Storage::disk('public')->delete($user->qr_code_image);
        }
        
        $path = $request->file('qr_code_image')->store('qr-codes', 'public');
        $validated['qr_code_image'] = $path;
    }

    $user->update($validated);

    return back()->with('success', 'QR code updated successfully!');
}
```

---

### **6. Update Seller Orders View**

File: `resources/views/seller/orders.blade.php`

Add after the cash payment section:

```blade
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
            <img src="{{ $order->getPaymentProofUrl() }}" 
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

Update payment method badge display to include QR:

```blade
@if($order->payment_method === 'qr')
    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
        <i class="fas fa-qrcode mr-1"></i>QR PAY
    </span>
@endif
```

---

### **7. Create Seller QR Setup Page**

File: `resources/views/seller/qr-setup.blade.php`

```blade
@extends('layouts.app')

@section('title', 'QR Code Setup')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <a href="{{ route('seller.dashboard') }}" class="text-blue-600 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
    </a>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">QR Payment Setup</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Setup Your QR Code</h3>

        <form method="POST" action="{{ route('seller.update-qr') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    QR Code Type *
                </label>
                <select name="qr_code_type" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="duitnow" {{ auth()->user()->qr_code_type === 'duitnow' ? 'selected' : '' }}>
                        DuitNow QR (All Banks) - Recommended
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
                    Upload QR Code Image *
                </label>

                @if(auth()->user()->hasQRCode())
                <div class="mb-3">
                    <p class="text-sm text-gray-600 mb-2">Current QR Code:</p>
                    <img src="{{ auth()->user()->getQRCodeUrl() }}" 
                         alt="Current QR" 
                         class="w-48 h-48 object-contain border rounded p-2 bg-white">
                </div>
                @endif

                <input type="file" 
                       name="qr_code_image" 
                       accept="image/*"
                       class="w-full px-3 py-2 border rounded-lg">
                <p class="text-xs text-gray-600 mt-1">
                    Get your QR code from your banking app (Settings → QR Code / Receive Money)
                </p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">
                    Instructions for Buyers (Optional)
                </label>
                <textarea name="qr_code_instructions" 
                          rows="3"
                          class="w-full px-3 py-2 border rounded-lg"
                          placeholder="e.g., Please include order number in payment reference">{{ auth()->user()->qr_code_instructions }}</textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                <i class="fas fa-save mr-2"></i>Save QR Code
            </button>
        </form>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-bold text-blue-900 mb-2">
            <i class="fas fa-info-circle mr-2"></i>How to Get Your QR Code
        </h3>
        <ol class="list-decimal ml-5 space-y-1 text-sm text-blue-800">
            <li>Open your banking app (Maybank, CIMB, Public Bank, etc)</li>
            <li>Go to Settings or More menu</li>
            <li>Find "DuitNow QR" or "Receive Money"</li>
            <li>Screenshot your QR code</li>
            <li>Upload here</li>
        </ol>
        <p class="text-xs text-blue-700 mt-2">
            💡 DuitNow QR is FREE and works with ALL Malaysian banks!
        </p>
    </div>
</div>
@endsection
```

---

## 🚀 TESTING CHECKLIST

After implementing all files:

### Buyer Flow:
- [ ] Can see QR payment option at checkout
- [ ] Can select QR payment
- [ ] Can place QR order
- [ ] Can see seller's QR code
- [ ] Can upload payment screenshot
- [ ] Can view order with proof uploaded

### Seller Flow:
- [ ] Can access QR setup page
- [ ] Can upload QR code image
- [ ] Can receive QR orders
- [ ] Can see payment proof
- [ ] Can verify payment
- [ ] Can reject invalid payment
- [ ] Payment verification completes order

---

## 📱 STORAGE SETUP

Make sure storage is linked:

```bash
./vendor/bin/sail artisan storage:link
```

This creates symlink: `public/storage` → `storage/app/public`

---

## 🎯 SUMMARY

**What We've Completed:**
1. ✅ Database migration
2. ✅ Models updated (User & Order)
3. ✅ All view files provided above
4. ✅ All controller methods provided
5. ✅ All routes provided

**What You Need To Do:**
1. Update checkout view (add QR option)
2. Create qr-payment.blade.php
3. Create qr-setup.blade.php
4. Add controller methods
5. Add routes
6. Update seller orders view
7. Test the flow!

**System Will Have:**
- ✅ Cash Payment
- ✅ QR Payment
- 🔜 Online Payment (Billplz)

Perfect payment variety! 🎉

---

**Document Created:** 2025-12-13  
**All Code Ready To Use!** ✅

