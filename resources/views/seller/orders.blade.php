@extends('layouts.app')

@section('title', 'Waffle Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">📋</span>
        <h1 class="text-2xl font-bold text-gray-800">Incoming Orders</h1>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="text-6xl mb-4">🧇</div>
        <p class="text-gray-600">No orders received yet</p>
        <p class="text-sm text-gray-500 mt-2">Orders will appear here when customers place orders</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-lg border-l-4
            @if($order->status === 'completed') border-green-500
            @elseif($order->status === 'cancelled') border-red-500
            @elseif($order->status === 'ready') border-amber-500
            @else border-yellow-500
            @endif">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <p class="font-bold text-lg flex items-center gap-2 mb-2">
                            <span>🧇</span>{{ $order->order_no }}
                        </p>
                        <div class="space-y-1">
                            <p class="text-sm text-gray-800 flex items-center gap-2">
                                <i class="fas fa-user text-amber-600 w-4"></i>
                                <span class="font-semibold">{{ $order->getCustomerName() }}</span>
                                @if($order->isGuestOrder())
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">Guest</span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-700 flex items-center gap-2">
                                <i class="fas fa-home text-amber-600 w-4"></i>
                                <span>{{ $order->getCustomerAddress() }}</span>
                            </p>
                            <p class="text-sm text-gray-700 flex items-center gap-2">
                                <i class="fas fa-phone text-amber-600 w-4"></i>
                                <a href="tel:{{ $order->getCustomerPhone() }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $order->getCustomerPhone() }}
                                </a>
                            </p>
                            <p class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                <i class="fas fa-clock w-4"></i>
                                {{ $order->created_at->format('d M Y, h:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase
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
                        @if($order->payment_method === 'cash')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="fas fa-money-bill-wave mr-1"></i>CASH
                            </span>
                        @elseif($order->payment_method === 'qr')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                <i class="fas fa-qrcode mr-1"></i>QR
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-credit-card mr-1"></i>ONLINE
                            </span>
                        @endif
                    </div>
                </div>

                <div class="border-t pt-3 mb-3">
                    @foreach($order->items as $item)
                    <p class="text-sm text-gray-700">{{ $item->quantity }}x {{ $item->product_name }}</p>
                    @endforeach
                </div>

                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg p-3 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-amber-700">💰 RM {{ number_format($order->seller_amount, 2) }}</span>
                        <span class="text-sm text-gray-600">Total: RM {{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Status & Actions -->
                @if($order->payment_method === 'cash')
                    @if($order->payment_status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800 mb-3">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Cash Payment Pending</strong><br>
                            Collect <strong>RM {{ number_format($order->total_amount, 2) }}</strong> cash from buyer at pickup.
                        </p>
                        
                        <form method="POST" action="{{ route(auth()->user()->isOwner() ? 'owner.orders.mark-paid' : 'staff.orders.mark-paid', $order->id) }}" 
                            onsubmit="event.preventDefault(); customConfirm('💵 Confirm cash payment received from buyer?', () => this.submit());">
                            @csrf
                            <button type="submit" 
                                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2 rounded-lg hover:from-amber-600 hover:to-orange-600 text-sm font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>
                                Confirm Cash Received & Complete Order
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-green-50 border border-green-200 p-3 rounded-lg mb-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Cash payment received on {{ $order->paid_at?->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    @endif
                @elseif($order->payment_method === 'qr')
                    @if($order->payment_status === 'pending')
                        @if($order->hasPaymentProof())
                        <div class="bg-purple-50 border border-purple-200 p-4 rounded-lg mb-4">
                            <p class="text-sm text-purple-800 mb-3">
                                <i class="fas fa-camera mr-2"></i>
                                <strong>QR Payment Proof Received!</strong><br>
                                Buyer uploaded payment screenshot. Check your bank and verify.
                            </p>
                            
                            <!-- View Payment Proof -->
                            <a href="{{ $order->getPaymentProofUrl() }}" target="_blank" 
                               class="block bg-white border border-purple-300 p-2 rounded-lg mb-3 text-center hover:bg-purple-50">
                                <img src="{{ $order->getPaymentProofUrl() }}" 
                                     alt="Payment Proof" 
                                     class="max-w-full max-h-48 mx-auto rounded">
                                <p class="text-xs text-purple-700 mt-2">
                                    <i class="fas fa-external-link-alt mr-1"></i>Click to view full size
                                </p>
                            </a>
                            
                            @if($order->payment_notes)
                            <p class="text-xs text-gray-700 mb-3 bg-gray-50 p-2 rounded">
                                <strong>Buyer notes:</strong> {{ $order->payment_notes }}
                            </p>
                            @endif
                            
                            <form method="POST" action="{{ route(auth()->user()->isOwner() ? 'owner.orders.verify-qr' : 'staff.orders.verify-qr', $order->id) }}" class="space-y-2">
                                @csrf
                                <button type="button"
                                    onclick="customConfirm('✅ Confirm payment received in your bank account?', () => { const form = this.closest('form'); const input = document.createElement('input'); input.type='hidden'; input.name='action'; input.value='approve'; form.appendChild(input); form.submit(); });"
                                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2 rounded-lg hover:from-amber-600 hover:to-orange-600 text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Verify Payment & Complete Order
                                </button>
                                <button type="button"
                                    onclick="customConfirm('❌ Are you sure payment was NOT received?', () => { const form = this.closest('form'); const input = document.createElement('input'); input.type='hidden'; input.name='action'; input.value='reject'; form.appendChild(input); form.submit(); });"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 text-sm">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Reject Payment
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mb-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-clock mr-2"></i>
                                Waiting for buyer to upload payment proof...
                            </p>
                        </div>
                        @endif
                    @else
                    <div class="bg-green-50 border border-green-200 p-3 rounded-lg mb-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            QR payment verified on {{ $order->paid_at?->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    @endif
                @elseif($order->payment_method === 'online')
                    @if($order->payment_status === 'paid')
                    <div class="bg-green-50 border border-green-200 p-3 rounded-lg mb-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Paid online
                        </p>
                    </div>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-clock mr-2"></i>Waiting for online payment
                        </p>
                    </div>
                    @endif
                @endif

                {{-- Status Update Dropdown --}}
                @php
                    // Allow status update for:
                    // 1. Cash orders (any time, they pay at pickup)
                    // 2. Other payment methods (only after paid)
                    $canUpdateStatus = $order->status !== 'completed' 
                                    && $order->status !== 'cancelled'
                                    && ($order->payment_method === 'cash' || $order->payment_status === 'paid');
                @endphp
                
                @if($canUpdateStatus)
                <form method="POST" action="{{ route(auth()->user()->isOwner() ? 'owner.orders.status' : 'staff.orders.status', $order->id) }}" class="mt-4">
                    @csrf
                    <div class="space-y-2">
                        @if($order->payment_method === 'cash' && $order->payment_status === 'pending')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                            <p class="text-xs text-blue-800">
                                💡 <strong>Tip:</strong> Update status while preparing. Customer will pay cash at pickup.
                            </p>
                        </div>
                        @endif
                        
                        <div class="flex gap-2">
                            <select name="status" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>👨‍🍳 Preparing</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>🎉 Ready for Pickup</option>
                                @if($order->payment_method !== 'cash' || $order->payment_status === 'paid')
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                @endif
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                            <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:shadow-lg transition text-sm font-semibold">
                                <i class="fas fa-sync-alt mr-1"></i>Update
                            </button>
                        </div>
                        
                        @if($order->payment_method === 'cash' && $order->payment_status === 'pending' && $order->status === 'ready')
                        <p class="text-xs text-amber-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Order ready! Use "Confirm Cash Received" button above when customer pays & collects.
                        </p>
                        @endif
                    </div>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection

