@extends('layouts.app')

@section('title', "D'house Waffle Dashboard")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-4xl">🧇</span>
        <h1 class="text-2xl font-bold text-gray-800">D'house Waffle Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Orders</p>
            <p class="text-2xl font-bold text-blue-600">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 relative">
            <p class="text-gray-600 text-sm">Pending Orders</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingOrders }}</p>
            @if($pendingOrders > 0)
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-8 w-8 flex items-center justify-center animate-pulse shadow-lg">
                {{ $pendingOrders > 9 ? '9+' : $pendingOrders }}
            </span>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Total Earnings</p>
            <p class="text-2xl font-bold text-green-600">RM {{ number_format($totalEarnings, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-600 text-sm">Active Products</p>
            <p class="text-2xl font-bold text-purple-600">{{ $activeProducts }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @if(auth()->user()->isOwner())
        <a href="{{ route('owner.sales-report') }}" 
            class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-4 rounded-lg text-center hover:from-green-600 hover:to-emerald-700 transition">
            <i class="fas fa-chart-bar text-2xl mb-2"></i>
            <p class="font-semibold">Sales Report</p>
        </a>
        <a href="{{ route('owner.products.create') }}" 
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white p-4 rounded-lg text-center hover:from-amber-600 hover:to-orange-600 transition">
            <i class="fas fa-plus-circle text-2xl mb-2"></i>
            <p class="font-semibold">Add New Waffle</p>
        </a>
        <a href="{{ route('owner.products') }}" 
            class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 rounded-lg text-center hover:from-purple-700 hover:to-pink-700 transition">
            <i class="fas fa-box text-2xl mb-2"></i>
            <p class="font-semibold">Manage Menu</p>
        </a>
        <a href="{{ route('owner.profile') }}" 
            class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 rounded-lg text-center hover:from-purple-700 hover:to-pink-700 transition">
            <i class="fas fa-qrcode text-2xl mb-2"></i>
            <p class="font-semibold">QR Payment Setup</p>
        </a>
        <a href="{{ route('owner.loyalty-settings') }}" 
            class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white p-4 rounded-lg text-center hover:from-yellow-600 hover:to-amber-600 transition">
            <i class="fas fa-trophy text-2xl mb-2"></i>
            <p class="font-semibold">Loyalty Settings</p>
        </a>
        @endif
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b flex justify-between items-center">
            <h2 class="font-bold text-lg">Recent Orders</h2>
            <a href="{{ auth()->user()->isOwner() ? route('owner.orders') : route('staff.orders') }}" class="text-blue-600 text-sm hover:underline">View All</a>
        </div>
        
        @if($recentOrders->isEmpty())
        <div class="p-8 text-center text-gray-600">
            <i class="fas fa-inbox text-4xl mb-2"></i>
            <p>No orders yet</p>
        </div>
        @else
        <div class="divide-y">
            @foreach($recentOrders as $order)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-semibold">{{ $order->order_no }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $order->getCustomerName() }}
                            @if($order->isGuestOrder())
                            <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded ml-1">Guest</span>
                            @endif
                        </p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $order->items->count() }} item(s)</span>
                    <span class="font-bold text-green-600">RM {{ number_format($order->seller_amount, 2) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

