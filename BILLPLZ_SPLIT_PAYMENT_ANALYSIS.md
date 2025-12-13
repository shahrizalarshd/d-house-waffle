# Billplz Split Payment Analysis
**Analisis untuk POS Apartment System**

---

## 📋 TABLE OF CONTENTS
1. [Billplz Overview](#billplz-overview)
2. [Split Payment Feature](#split-payment-feature)
3. [How It Works](#how-it-works)
4. [API Implementation](#api-implementation)
5. [Code Examples](#code-examples)
6. [Setup Requirements](#setup-requirements)
7. [Pros & Cons](#pros--cons)
8. [Alternative Solutions](#alternative-solutions)

---

## 🏦 BILLPLZ OVERVIEW

**Billplz** adalah Malaysian payment gateway yang popular untuk:
- Online Banking (FPX)
- Credit/Debit Cards
- E-wallets (Boost, TNG, ShopeePay, GrabPay)

### Key Features:
✅ Malaysian-based (support local banks)
✅ Competitive rates
✅ Good documentation
✅ Webhook support
✅ Split Payment feature
✅ No setup fee
✅ No monthly fee

### Pricing:
- **FPX**: RM 1.50 per transaction
- **Cards**: 2.8% + RM 0.50 per transaction
- **E-wallets**: 2.5% per transaction

**Website**: https://www.billplz.com

---

## 💰 SPLIT PAYMENT FEATURE

### What is Split Payment?

Billplz ada feature untuk **automatically split payment** kepada multiple bank accounts dalam satu transaksi.

**Example:**
```
Buyer bayar RM 100
      ↓
Billplz Split Payment
      ↓  ↙
      ↓ Bank A (Platform) → RM 5.00
      ↓
Bank B (Seller) → RM 95.00
```

### Important Notes:

⚠️ **CRITICAL FINDING**: Billplz's "Split Payment" feature has **LIMITATIONS**:

1. **Split Payment TO** (Payout Split):
   - This feature is for MERCHANT to split money RECEIVED
   - Requires **Billplz for Business** account
   - Requires **Enterprise** plan
   - NOT available for basic accounts
   
2. **Collections** (Alternative):
   - Basic Billplz uses "Collections"
   - Money goes to ONE bank account only
   - Manual payout required

---

## 🔍 HOW IT ACTUALLY WORKS

### Method 1: Split Payment (Enterprise Only)

**Requirements:**
- ✅ Billplz Enterprise account
- ✅ Multiple bank accounts registered
- ✅ Each seller must verify their bank account with Billplz
- ✅ KYC (Know Your Customer) for each seller

**Flow:**
```
1. Platform creates bill with split_payment_id
2. Buyer pays via Billplz
3. Billplz automatically splits:
   - RM 5 → Platform bank account
   - RM 95 → Seller bank account
4. Payout to both accounts (T+2 working days)
```

**API Endpoint:**
```
POST https://www.billplz-sandbox.com/api/v3/bills
```

**Split Payment Parameters:**
```json
{
  "collection_id": "abc123",
  "email": "buyer@example.com",
  "name": "John Doe",
  "amount": "10000",
  "description": "Order ORD-123",
  "split_payment": {
    "split_payment_id": "sp_xxx",
    "split": [
      {
        "bank_account_id": "ba_platform",
        "fixed_cut": "500",
        "variable_cut": ""
      },
      {
        "bank_account_id": "ba_seller",
        "fixed_cut": "9500",
        "variable_cut": ""
      }
    ]
  }
}
```

### Method 2: Collections (Standard - Recommended for MVP)

**Requirements:**
- ✅ Basic Billplz account (Free)
- ✅ ONE bank account (Platform)
- ✅ Manual payout to sellers

**Flow:**
```
1. Buyer pays RM 100
2. Money goes to PLATFORM bank account
3. Platform manually transfer RM 95 to seller
4. Platform keeps RM 5 as fee
```

**Simpler but requires manual work**

---

## 💻 API IMPLEMENTATION

### Setup Billplz Package

```bash
composer require billplz/billplz-laravel
```

### Configuration

**config/billplz.php:**
```php
<?php

return [
    'api_key' => env('BILLPLZ_API_KEY'),
    'collection_id' => env('BILLPLZ_COLLECTION_ID'),
    'x_signature' => env('BILLPLZ_X_SIGNATURE'),
    'sandbox' => env('BILLPLZ_SANDBOX', true),
    
    // Split payment settings (Enterprise only)
    'split_payment_enabled' => env('BILLPLZ_SPLIT_ENABLED', false),
    'platform_bank_account' => env('BILLPLZ_PLATFORM_BANK_ACCOUNT'),
];
```

**.env:**
```env
BILLPLZ_API_KEY=your_api_key_here
BILLPLZ_COLLECTION_ID=your_collection_id
BILLPLZ_X_SIGNATURE=your_signature_key
BILLPLZ_SANDBOX=true
BILLPLZ_SPLIT_ENABLED=false
BILLPLZ_PLATFORM_BANK_ACCOUNT=ba_xxxx
```

### Service Class

**app/Services/BillplzService.php:**
```php
<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    protected $apiKey;
    protected $collectionId;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('billplz.api_key');
        $this->collectionId = config('billplz.collection_id');
        $this->baseUrl = config('billplz.sandbox') 
            ? 'https://www.billplz-sandbox.com/api/v3'
            : 'https://www.billplz.com/api/v3';
    }
    
    /**
     * Create a bill (standard - no split)
     */
    public function createBill(Order $order)
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->post("{$this->baseUrl}/bills", [
                'collection_id' => $this->collectionId,
                'email' => $order->buyer->email,
                'name' => $order->buyer->name,
                'amount' => $order->total_amount * 100, // Convert to cents
                'description' => "Order {$order->order_no} - {$order->apartment->name}",
                'callback_url' => route('webhook.billplz'),
                'redirect_url' => route('buyer.order.detail', $order->id),
                'reference_1_label' => 'Order ID',
                'reference_1' => $order->id,
            ]);
            
        if ($response->successful()) {
            $bill = $response->json();
            Log::info('Billplz bill created', ['bill_id' => $bill['id']]);
            return $bill;
        }
        
        Log::error('Billplz bill creation failed', ['response' => $response->json()]);
        throw new \Exception('Failed to create bill: ' . $response->json()['error']);
    }
    
    /**
     * Create a bill with split payment (Enterprise only)
     */
    public function createBillWithSplit(Order $order)
    {
        // Check if seller has bank account registered
        if (!$order->seller->billplz_bank_account_id) {
            throw new \Exception('Seller bank account not registered');
        }
        
        $response = Http::withBasicAuth($this->apiKey, '')
            ->post("{$this->baseUrl}/bills", [
                'collection_id' => $this->collectionId,
                'email' => $order->buyer->email,
                'name' => $order->buyer->name,
                'amount' => $order->total_amount * 100,
                'description' => "Order {$order->order_no}",
                'callback_url' => route('webhook.billplz'),
                'redirect_url' => route('buyer.order.detail', $order->id),
                'split_payment' => [
                    'email' => config('billplz.platform_email'),
                    'split_payments' => [
                        [
                            'bank_account_id' => config('billplz.platform_bank_account'),
                            'fixed_cut' => $order->platform_fee * 100, // Platform fee
                        ],
                        [
                            'bank_account_id' => $order->seller->billplz_bank_account_id,
                            'fixed_cut' => $order->seller_amount * 100, // Seller amount
                        ],
                    ],
                ],
            ]);
            
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Failed to create split bill: ' . $response->json()['error']);
    }
    
    /**
     * Get bill details
     */
    public function getBill($billId)
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get("{$this->baseUrl}/bills/{$billId}");
            
        if ($response->successful()) {
            return $response->json();
        }
        
        return null;
    }
    
    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($data, $signature)
    {
        $xSignature = config('billplz.x_signature');
        
        // Remove signature from data
        unset($data['x_signature']);
        
        // Sort data by key
        ksort($data);
        
        // Create signature string
        $signatureString = '';
        foreach ($data as $key => $value) {
            $signatureString .= $key . $value;
        }
        
        // Generate HMAC
        $calculatedSignature = hash_hmac('sha256', $signatureString, $xSignature);
        
        return hash_equals($calculatedSignature, $signature);
    }
}
```

---

## 📝 CODE EXAMPLES

### 1. Update OrderController

**app/Http/Controllers/OrderController.php:**
```php
use App\Services\BillplzService;

class OrderController extends Controller
{
    protected $billplz;
    
    public function __construct(BillplzService $billplz)
    {
        $this->billplz = $billplz;
    }
    
    public function placeOrder(Request $request)
    {
        // ... existing order creation code ...
        
        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'billplz',
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);
        
        // Create Billplz bill
        try {
            $bill = $this->billplz->createBill($order);
            
            // Update payment with reference
            $payment->update([
                'reference_no' => $bill['id']
            ]);
            
            // Redirect to Billplz payment page
            return redirect($bill['url']);
            
        } catch (\Exception $e) {
            Log::error('Billplz error: ' . $e->getMessage());
            return back()->with('error', 'Payment gateway error. Please try again.');
        }
    }
}
```

### 2. Update PaymentWebhookController

**app/Http/Controllers/PaymentWebhookController.php:**
```php
use App\Services\BillplzService;

class PaymentWebhookController extends Controller
{
    protected $billplz;
    
    public function __construct(BillplzService $billplz)
    {
        $this->billplz = $billplz;
    }
    
    public function billplz(Request $request)
    {
        // Verify signature
        $signature = $request->input('x_signature');
        if (!$this->billplz->verifyWebhookSignature($request->all(), $signature)) {
            Log::warning('Invalid Billplz webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        Log::info('Billplz webhook received', $request->all());
        
        $billId = $request->input('id');
        $paid = $request->input('paid');
        $paidAt = $request->input('paid_at');
        
        // Find payment by reference
        $payment = Payment::where('reference_no', $billId)->first();
        
        if (!$payment) {
            Log::error('Payment not found for bill ID: ' . $billId);
            return response()->json(['error' => 'Payment not found'], 404);
        }
        
        if ($paid === 'true' || $paid === true) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);
            
            $payment->order->update([
                'payment_status' => 'paid',
                'payment_ref' => $billId,
            ]);
            
            Log::info('Payment successful', [
                'order_no' => $payment->order->order_no,
                'amount' => $payment->amount
            ]);
        } else {
            $payment->update(['status' => 'failed']);
            $payment->order->update(['payment_status' => 'failed']);
            
            Log::info('Payment failed', ['order_no' => $payment->order->order_no]);
        }
        
        return response()->json(['success' => true]);
    }
}
```

### 3. Update Payment View

**resources/views/buyer/payment.blade.php:**
```blade
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Payment</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold mb-4">Order: {{ $order->order_no }}</h3>
        
        <div class="border-t border-b py-4 mb-4">
            @foreach($order->items as $item)
            <div class="flex justify-between mb-2">
                <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                <span>RM {{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
        </div>

        <div class="space-y-2 mb-6">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>RM {{ number_format($order->seller_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Platform Fee ({{ $order->apartment->service_fee_percent }}%):</span>
                <span>RM {{ number_format($order->platform_fee, 2) }}</span>
            </div>
            <div class="flex justify-between text-xl font-bold border-t pt-2">
                <span>Total:</span>
                <span class="text-blue-600">RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        @if($order->payment_status === 'pending')
            <form action="{{ route('orders.place') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                
                <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-credit-card mr-2"></i>
                    Pay with Billplz
                </button>
            </form>
        @else
            <div class="text-center py-4">
                <span class="px-4 py-2 rounded-full 
                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        @endif
    </div>
</div>
@endsection
```

---

## ⚙️ SETUP REQUIREMENTS

### 1. Billplz Account Setup

1. **Sign up**: https://www.billplz.com/join
2. **Verify identity** (MyKad/Business Registration)
3. **Add bank account** untuk terima payment
4. **Create Collection** untuk organize bills
5. **Get API credentials**:
   - API Secret Key
   - Collection ID
   - X Signature Key

### 2. Webhook Configuration

Set webhook URL di Billplz Dashboard:
```
https://yourdomain.com/webhook/billplz
```

### 3. Testing Mode

Billplz provide **Sandbox** untuk testing:
- Sandbox URL: https://www.billplz-sandbox.com
- Test credentials provided
- No real money involved

### 4. Production Checklist

Before going live:
- ✅ Complete Billplz KYC verification
- ✅ Test all payment flows in sandbox
- ✅ Verify webhook signature working
- ✅ Test failed payment scenario
- ✅ Check bank account receiving funds (T+2 days)
- ✅ Update .env to production mode
- ✅ SSL certificate installed (HTTPS required)

---

## ✅ PROS & CONS

### Pros:
✅ Malaysian payment gateway (support all local banks)
✅ Multiple payment methods (FPX, Cards, E-wallets)
✅ Good documentation & support
✅ Reasonable pricing
✅ Webhook support for automation
✅ Sandbox for testing
✅ No setup or monthly fees
✅ Fast settlement (T+2 working days)

### Cons:
❌ Split payment requires Enterprise plan (expensive)
❌ Each seller needs bank verification (admin overhead)
❌ Transaction fees apply
❌ Webhook can delay (need to handle)
❌ Manual payout if not using split payment
❌ Requires SSL certificate
❌ Settlement not instant (T+2 days)

---

## 🔄 ALTERNATIVE SOLUTIONS

Since Billplz Split Payment requires Enterprise plan, here are alternatives:

### Option 1: Standard Billplz + Manual Payout (Recommended for MVP)

**Flow:**
```
Buyer (RM 100) → Billplz → Platform Bank
                              ↓
                   Monthly/Weekly Payout
                              ↓
                          Seller Bank
```

**Implementation:**
- Simple setup (basic Billplz account)
- Platform receives all payments
- Generate monthly payout reports
- Manual bank transfer to sellers
- Track payouts in database

**Best for:**
- MVP / Early stage
- Limited sellers (< 50)
- Testing business model

### Option 2: Stripe Connect

**Features:**
- Automatic split payment
- Per-transaction split
- Seller onboarding flow
- International support

**Cons:**
- Higher fees (2.9% + RM 1.00)
- Requires sellers to have Stripe account
- More complex setup

### Option 3: ToyyibPay

**Features:**
- Malaysian gateway
- Islamic-compliant
- Support split payment
- Lower fees for non-profit

**Check:** https://toyyibpay.com

### Option 4: Build Payout System

**Features:**
- Platform holds money
- Automated payout schedule
- Track seller earnings
- Generate reports

**Requires:**
- Payout tracking table
- Cron job for automation
- Bank API integration (optional)

---

## 🎯 RECOMMENDATION FOR POS APARTMENT

### For MVP (Current Stage):

**Use: Standard Billplz (No Split)**

**Reasons:**
1. ✅ Simple setup
2. ✅ No enterprise plan needed
3. ✅ Lower initial cost
4. ✅ Faster to implement
5. ✅ Good for testing

**Implementation Steps:**
1. Install billplz-laravel package
2. Create BillplzService class
3. Update OrderController to create bills
4. Update webhook handler
5. Test in sandbox
6. Go live

**Manual Payout Process:**
1. Weekly/Monthly generate payout reports
2. Bank transfer to sellers
3. Mark as paid in system
4. Send notification to sellers

### For Scale (Future):

**Upgrade to:**
- Billplz Enterprise (if many sellers)
- OR Stripe Connect (for automation)
- OR Build custom payout system

---

## 📊 COST ANALYSIS

### Scenario: 100 orders/month @ RM 50 average

**Standard Billplz (FPX):**
```
Transaction fee: 100 orders × RM 1.50 = RM 150/month
Manual payout: 10 sellers × RM 1.50 = RM 15/month
Total: RM 165/month
```

**Billplz Enterprise (Split Payment):**
```
Monthly fee: RM 500/month (estimated)
Transaction fee: 100 orders × RM 1.50 = RM 150/month
Total: RM 650/month
```

**Breakeven Point:**
- Enterprise only worth it if > 300 orders/month
- OR if manual payout too time-consuming

---

## 📚 USEFUL RESOURCES

1. **Billplz Documentation**: https://www.billplz.com/api
2. **Billplz PHP SDK**: https://github.com/billplz/billplz-laravel
3. **Sandbox**: https://www.billplz-sandbox.com
4. **Support**: support@billplz.com
5. **Status Page**: https://status.billplz.com

---

## ✍️ CONCLUSION

**Untuk sistem POS Apartment:**

1. **Start with Standard Billplz** (no split)
   - Implement basic payment flow
   - Test thoroughly
   - Launch MVP

2. **Build Payout Tracking System**
   - Track seller earnings
   - Generate payout reports
   - Manual transfer for now

3. **Future Upgrade Options:**
   - If scale up: Billplz Enterprise
   - If automate: Stripe Connect
   - If custom: Build payout API

**Priority: Get MVP working first, optimize later! 🚀**

---

**Document Created:** 2025-12-13  
**Last Updated:** 2025-12-13  
**Version:** 1.0

