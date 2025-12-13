@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="text-3xl">📊</span>
            <h1 class="text-2xl font-bold text-gray-800">Sales Report</h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-filter text-amber-600"></i>
            Filter Sales Data
        </h3>
        
        <form method="GET" action="{{ route('owner.sales-report') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date From -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i>Date From
                    </label>
                    <input type="date" 
                           name="date_from" 
                           value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-calendar-check mr-1"></i>Date To
                    </label>
                    <input type="date" 
                           name="date_to" 
                           value="{{ request('date_to', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-tasks mr-1"></i>Order Status
                    </label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-credit-card mr-1"></i>Payment Method
                    </label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="qr" {{ request('payment_method') === 'qr' ? 'selected' : '' }}>QR Payment</option>
                        <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-money-check-alt mr-1"></i>Payment Status
                    </label>
                    <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="fas fa-search mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('owner.sales-report') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-4">
            <p class="text-sm opacity-90">Total Orders</p>
            <p class="text-3xl font-bold">{{ $stats['total_orders'] }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-4">
            <p class="text-sm opacity-90">Total Revenue</p>
            <p class="text-3xl font-bold">RM {{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-4">
            <p class="text-sm opacity-90">Avg Order Value</p>
            <p class="text-3xl font-bold">RM {{ number_format($stats['avg_order_value'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-lg shadow-lg p-4">
            <p class="text-sm opacity-90">Total Items Sold</p>
            <p class="text-3xl font-bold">{{ $stats['total_items'] }}</p>
        </div>
    </div>

    <!-- Export Button -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <p class="font-bold text-gray-800">Export Sales Report</p>
                <p class="text-sm text-gray-600">Download filtered data as Excel file</p>
            </div>
            <a href="{{ route('owner.sales-report.export', request()->all()) }}" 
               class="w-full md:w-auto bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold inline-flex items-center justify-center gap-2">
                <i class="fas fa-file-excel text-xl"></i>
                <span>Download Excel</span>
            </a>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h3 class="font-bold text-lg">
                Orders List 
                <span class="text-sm font-normal text-gray-600">({{ $orders->total() }} records)</span>
            </h3>
        </div>

        @if($orders->isEmpty())
        <div class="p-8 text-center text-gray-600">
            <i class="fas fa-inbox text-4xl mb-2"></i>
            <p>No orders found for selected filters</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Items</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Payment</th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-sm">{{ $order->order_no }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium">{{ $order->buyer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->buyer->unit_no }} - {{ $order->buyer->block }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm">{{ $order->items->sum('quantity') }} items</p>
                            <p class="text-xs text-gray-500">{{ $order->items->pluck('product_name')->take(2)->join(', ') }}{{ $order->items->count() > 2 ? '...' : '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="font-bold text-green-600">RM {{ number_format($order->total_amount, 2) }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->payment_method === 'cash') bg-green-100 text-green-800
                                @elseif($order->payment_method === 'qr') bg-purple-100 text-purple-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ strtoupper($order->payment_method) }}
                            </span>
                            <br>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold mt-1 inline-block
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status === 'ready') bg-amber-100 text-amber-800
                                @elseif($order->status === 'preparing') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t bg-gray-50">
            {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

