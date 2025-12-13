@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">📋</span>
        <h1 class="text-2xl font-bold text-gray-800">My Waffle Orders</h1>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="text-6xl mb-4">🧇</div>
        <p class="text-gray-600 mb-4">No orders yet</p>
        <a href="{{ route('home') }}" class="inline-block waffle-gradient text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
            Browse Waffles
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition border-l-4 
            @if($order->status === 'completed') border-green-500
            @elseif($order->status === 'cancelled') border-red-500
            @elseif($order->status === 'ready') border-amber-500
            @else border-yellow-500
            @endif">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-lg flex items-center gap-2">
                            <span>🧇</span>{{ $order->order_no }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($order->status === 'ready') bg-amber-100 text-amber-800
                        @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        @if($order->status === 'completed') ✓ {{ ucfirst($order->status) }}
                        @elseif($order->status === 'ready') 🎉 Ready!
                        @elseif($order->status === 'preparing') 👨‍🍳 Preparing
                        @else ⏳ {{ ucfirst($order->status) }}
                        @endif
                    </span>
                </div>

                <div class="bg-amber-50 rounded-lg p-3 mb-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700">{{ $item->quantity }}x {{ $item->product_name }}</span>
                        <span class="font-semibold text-gray-800">RM {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mb-3">
                    <span class="text-lg font-bold text-amber-600">Total: RM {{ number_format($order->total_amount, 2) }}</span>
                    <a href="{{ route('buyer.order.detail', $order->id) }}" 
                        class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition text-sm font-semibold">
                        View Details →
                    </a>
                </div>

                <div class="flex items-center gap-4 text-xs text-gray-600 border-t pt-2">
                    <span><i class="fas fa-clock mr-1"></i>{{ $order->pickup_time->format('d M Y, h:i A') }}</span>
                    <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $order->pickup_location }}</span>
                </div>
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

