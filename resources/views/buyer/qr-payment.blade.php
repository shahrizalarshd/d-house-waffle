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
        
        <div class="bg-white p-4 rounded-lg inline-block mb-4 shadow-lg">
            <img src="{{ $order->seller->getQRCodeUrl() }}" 
                 alt="QR Code" 
                 class="w-64 h-64 object-contain">
        </div>

        <div class="text-sm text-purple-800 mb-4">
            <p class="font-semibold mb-2">
                <i class="fas fa-building-columns mr-1"></i>
                {{ $order->seller->qr_code_type ? ucfirst($order->seller->qr_code_type) : 'DuitNow' }} QR Code
            </p>
            <p class="text-gray-700">Pay to: <strong>{{ $order->seller->name }}</strong></p>
            @if($order->seller->qr_code_instructions)
            <p class="text-xs italic mt-2">{{ $order->seller->qr_code_instructions }}</p>
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
                <li>Take screenshot of confirmation</li>
                <li>Upload screenshot below</li>
            </ol>
        </div>
    </div>

    <!-- Upload Payment Proof -->
    @if(!$order->hasPaymentProof())
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold mb-4">
            <i class="fas fa-camera mr-2"></i>Upload Payment Proof
        </h3>

        <form method="POST" action="{{ route('orders.upload-proof', $order->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    Payment Screenshot *
                </label>
                <input type="file" 
                       name="payment_proof" 
                       accept="image/*" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                <p class="text-xs text-gray-600 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Upload screenshot showing payment confirmation from your banking app
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">
                    Notes (Optional)
                </label>
                <textarea name="payment_notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                          placeholder="Transaction reference, time, or any additional information..."></textarea>
            </div>

            <button type="submit" 
                    class="w-full waffle-gradient text-white py-3 rounded-lg hover:shadow-lg transition font-semibold">
                <i class="fas fa-upload mr-2"></i>Upload Payment Proof
            </button>
        </form>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 p-6 rounded-lg">
        <p class="text-green-800 font-semibold mb-2">
            <i class="fas fa-check-circle mr-2"></i>Payment Proof Uploaded!
        </p>
        <p class="text-sm text-green-700 mb-3">
            Waiting for seller to verify your payment...
        </p>
        <a href="{{ route('buyer.order.detail', $order->id) }}" 
           class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded hover:from-amber-600 hover:to-orange-600 text-sm font-semibold">
            <i class="fas fa-eye mr-2"></i>View Order Details
        </a>
    </div>
    @endif
    @else
    <div class="bg-red-50 border border-red-200 p-6 rounded-lg">
        <p class="text-red-800 font-semibold">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Seller has not setup QR payment yet
        </p>
        <p class="text-sm text-red-700 mt-2">
            Please contact seller or choose another payment method.
        </p>
        <a href="{{ route('buyer.orders') }}" class="inline-block mt-3 text-red-600 underline">
            Back to Orders
        </a>
    </div>
    @endif
</div>
@endsection

