@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <a href="{{ route('buyer.orders') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 mb-4 font-semibold">
        <i class="fas fa-arrow-left"></i>Back to Orders
    </a>

    <div class="bg-white rounded-lg shadow-xl p-6 mb-4 border-l-4
        @if($order->status === 'completed') border-green-500
        @elseif($order->status === 'cancelled') border-red-500
        @elseif($order->status === 'ready') border-amber-500
        @else border-yellow-500
        @endif">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <span>🧇</span>{{ $order->order_no }}
                </h1>
                <p class="text-gray-600"><i class="fas fa-clock mr-1"></i>{{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-bold uppercase
                @if($order->status === 'completed') bg-green-100 text-green-800
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                @elseif($order->status === 'ready') bg-amber-100 text-amber-800
                @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                @if($order->status === 'completed') ✓ Done
                @elseif($order->status === 'ready') 🎉 Ready
                @elseif($order->status === 'preparing') 👨‍🍳 Cooking
                @else ⏳ {{ ucfirst($order->status) }}
                @endif
            </span>
        </div>

        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-4 mb-4">
            <h3 class="font-bold mb-3 flex items-center gap-2">
                <span>🧇</span>Your Order Items
            </h3>
            @foreach($order->items as $item)
            <div class="flex justify-between mb-2 py-2 border-b border-amber-200 last:border-0">
                <span class="text-gray-700">{{ $item->quantity }}x {{ $item->product_name }}</span>
                <span class="font-semibold text-amber-700">RM {{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
        </div>

        <div class="space-y-2 mb-4 bg-gray-50 p-4 rounded-lg">
            <div class="flex justify-between text-gray-700">
                <span>Subtotal:</span>
                <span>RM {{ number_format($order->total_amount - $order->platform_fee, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-500 text-sm">
                <span>Service Fee:</span>
                <span>RM {{ number_format($order->platform_fee, 2) }}</span>
            </div>
            <div class="flex justify-between text-xl font-bold border-t-2 border-amber-500 pt-3 text-amber-600">
                <span>Total:</span>
                <span>RM {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Method Information -->
        @if($order->payment_method === 'cash')
        <div class="bg-green-50 border border-green-200 p-6 rounded-lg mb-4">
            <h3 class="font-bold text-green-800 mb-3 flex items-center">
                <i class="fas fa-money-bill-wave mr-2"></i>Cash on Pickup
            </h3>
            
            @if($order->payment_status === 'pending')
            <div class="space-y-2 text-sm text-green-800">
                <p><strong>Amount to Pay:</strong> RM {{ number_format($order->total_amount, 2) }}</p>
                <p><strong>Pickup Location:</strong> {{ $order->pickup_location }}</p>
                <p><strong>Pickup Time:</strong> {{ $order->pickup_time->format('d M Y, h:i A') }}</p>
                
                <div class="bg-white p-3 rounded mt-3">
                    <p class="font-semibold mb-2">📝 Instructions:</p>
                    <ol class="list-decimal ml-5 space-y-1">
                        <li>Prepare exact amount: <strong>RM {{ number_format($order->total_amount, 2) }}</strong></li>
                        <li>Go to pickup location at scheduled time</li>
                        <li>Meet seller: <strong>{{ $order->seller->name }}</strong></li>
                        <li>Pay cash and collect your order</li>
                    </ol>
                </div>
                
                <p class="text-xs mt-3">
                    <i class="fas fa-phone mr-1"></i>
                    Contact seller: <a href="tel:{{ $order->seller->phone }}" class="underline">{{ $order->seller->phone }}</a>
                </p>
            </div>
            @else
            <p class="text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                Cash payment completed on {{ $order->paid_at?->format('d M Y, h:i A') }}
            </p>
            @endif
        </div>
        @elseif($order->payment_method === 'qr')
        <div class="bg-purple-50 border border-purple-200 p-6 rounded-lg mb-4">
            <h3 class="font-bold text-purple-800 mb-3 flex items-center">
                <i class="fas fa-qrcode mr-2"></i>QR Payment
            </h3>
            
            @if($order->payment_status === 'paid')
            <p class="text-purple-800">
                <i class="fas fa-check-circle mr-2"></i>
                QR payment verified on {{ $order->paid_at?->format('d M Y, h:i A') }}
            </p>
            @elseif($order->hasPaymentProof())
            <div class="space-y-3">
                <p class="text-sm text-purple-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    Payment proof uploaded! Waiting for seller to verify...
                </p>
                
                <div class="bg-white p-3 rounded-lg">
                    <p class="text-xs text-gray-600 mb-2">Your payment proof:</p>
                    <img src="{{ $order->getPaymentProofUrl() }}" 
                         alt="Payment Proof" 
                         class="max-w-full max-h-48 rounded border">
                </div>
                
                @if($order->payment_notes)
                <div class="bg-white p-3 rounded-lg">
                    <p class="text-xs text-gray-600">Your notes:</p>
                    <p class="text-sm text-gray-800">{{ $order->payment_notes }}</p>
                </div>
                @endif
            </div>
            @else
            <div class="space-y-3">
                <p class="text-sm text-purple-800 mb-2">
                    <strong>Amount to Pay:</strong> RM {{ number_format($order->total_amount, 2) }}
                </p>
                
                <a href="{{ route('orders.qr-payment', $order->id) }}" 
                   class="block w-full waffle-gradient text-white text-center py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="fas fa-qrcode mr-2"></i>View QR Code & Upload Payment Proof
                </a>
                
                <div class="bg-white p-3 rounded mt-3">
                    <p class="font-semibold mb-2 text-sm">📱 Quick Steps:</p>
                    <ol class="list-decimal ml-5 space-y-1 text-xs text-gray-700">
                        <li>Click button above to see seller's QR code</li>
                        <li>Scan with your banking app or e-wallet</li>
                        <li>Pay RM {{ number_format($order->total_amount, 2) }}</li>
                        <li>Take screenshot of confirmation</li>
                        <li>Upload screenshot as proof</li>
                    </ol>
                </div>
            </div>
            @endif
        </div>
        @elseif($order->payment_method === 'online')
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-4">
            <h3 class="font-bold text-blue-800 mb-2 flex items-center">
                <i class="fas fa-credit-card mr-2"></i>Online Payment
            </h3>
            @if($order->payment_status === 'paid')
            <p class="text-sm text-blue-800">
                <i class="fas fa-check-circle mr-2"></i>
                Payment successful via Billplz
            </p>
            @elseif($order->payment_status === 'pending')
            <p class="text-sm text-blue-800">
                <i class="fas fa-clock mr-2"></i>
                Waiting for payment confirmation
            </p>
            @else
            <p class="text-sm text-red-800">
                <i class="fas fa-times-circle mr-2"></i>
                Payment failed. Please contact support.
            </p>
            @endif
        </div>
        @endif

        <div class="bg-white border-2 border-amber-200 p-4 rounded-lg">
            <h3 class="font-bold mb-3 flex items-center gap-2 text-amber-700">
                <i class="fas fa-store"></i>Seller Information
            </h3>
            <p class="text-gray-800 font-semibold">{{ $order->seller->name }}</p>
            <p class="text-sm text-gray-600"><i class="fas fa-phone mr-1"></i>{{ $order->seller->phone }}</p>
        </div>

        <div class="waffle-gradient text-white p-4 rounded-lg mt-4">
            <h3 class="font-bold mb-3 flex items-center gap-2">
                <i class="fas fa-clock"></i>Pickup Details
            </h3>
            <p class="text-lg font-semibold">{{ $order->pickup_time->format('d M Y, h:i A') }}</p>
            <p class="text-sm text-amber-100"><i class="fas fa-map-marker-alt mr-1"></i>{{ $order->pickup_location }}</p>
        </div>

        <div class="mt-4">
            <h3 class="font-bold mb-2">Order Status</h3>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($order->status === 'completed') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                    @else bg-yellow-100 text-yellow-800
                    @endif">
                    Payment: {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

