@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Super Admin Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Apartments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Apartments</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_apartments'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_sellers'] }} sellers</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Payment Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Platform Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Platform Revenue</h3>
                <i class="fas fa-chart-line text-gray-400"></i>
            </div>
            <p class="text-3xl font-bold text-green-600">
                RM {{ number_format($stats['total_revenue'], 2) }}
            </p>
            <p class="text-xs text-gray-600 mt-2">Total platform fees collected</p>
        </div>

        <!-- Payment Gateway Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Payment Gateway</h3>
                <i class="fas fa-credit-card text-gray-400"></i>
            </div>
            
            @if($stats['billplz_status'] === 'active')
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-green-600 font-semibold">Billplz Active</span>
                </div>
                <p class="text-xs text-gray-600 mt-2">Payment gateway is configured and ready</p>
            @else
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span class="text-red-600 font-semibold">Not Configured</span>
                </div>
                <p class="text-xs text-gray-600 mt-2">Billplz needs configuration</p>
                <a href="{{ route('super.settings') }}" 
                    class="inline-block mt-3 text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Configure Now
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-800 mb-4">
            <i class="fas fa-bolt mr-2"></i>Quick Actions
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('super.settings') }}" 
                class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <i class="fas fa-cog text-blue-600 text-2xl"></i>
                <div>
                    <p class="font-semibold text-gray-800">Platform Settings</p>
                    <p class="text-xs text-gray-600">Configure Billplz & more</p>
                </div>
            </a>

            <a href="{{ route('super.apartments') }}" 
                class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <i class="fas fa-building text-green-600 text-2xl"></i>
                <div>
                    <p class="font-semibold text-gray-800">Manage Apartments</p>
                    <p class="text-xs text-gray-600">View all apartments</p>
                </div>
            </a>

            <a href="{{ route('super.users') }}" 
                class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
                <div>
                    <p class="font-semibold text-gray-800">Manage Users</p>
                    <p class="text-xs text-gray-600">View all users</p>
                </div>
            </a>

            <a href="#" 
                class="flex items-center space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition opacity-50 cursor-not-allowed">
                <i class="fas fa-chart-bar text-orange-600 text-2xl"></i>
                <div>
                    <p class="font-semibold text-gray-800">Reports</p>
                    <p class="text-xs text-gray-600">Coming soon</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
