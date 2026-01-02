<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🧇 Track Order - {{ $order->order_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .waffle-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header -->
    <header class="waffle-gradient text-white p-4 shadow-lg">
        <div class="max-w-2xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🧇</span>
                <h1 class="text-xl font-bold">D'house Waffle</h1>
            </div>
            <a href="{{ route('menu') }}" class="bg-white/20 px-4 py-2 rounded-lg hover:bg-white/30 transition">
                Order Again
            </a>
        </div>
    </header>

    <div class="max-w-2xl mx-auto px-4 py-6">
        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Order Status Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="waffle-gradient text-white p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-amber-100 text-sm">Order Number</p>
                        <h2 class="text-2xl font-bold">{{ $order->order_no }}</h2>
                    </div>
                    <div class="text-right">
                        <p class="text-amber-100 text-sm">Total</p>
                        <p class="text-2xl font-bold">RM {{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Status Timeline -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-4">Order Status</h3>
                    
                    <div class="flex items-center justify-between">
                        @php
                            $statuses = ['pending', 'preparing', 'ready', 'completed'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($order->status === 'cancelled') $currentIndex = -1;
                        @endphp
                        
                        @foreach(['Pending', 'Preparing', 'Ready', 'Completed'] as $index => $statusLabel)
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                @if($index < $currentIndex)
                                    <i class="fas fa-check"></i>
                                @elseif($index == $currentIndex)
                                    <i class="fas fa-circle animate-pulse"></i>
                                @else
                                    <span>{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <p class="text-xs mt-2 {{ $index <= $currentIndex ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                {{ $statusLabel }}
                            </p>
                        </div>
                        @if($index < 3)
                        <div class="flex-1 h-1 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }} -mt-6"></div>
                        @endif
                        @endforeach
                    </div>
                    
                    @if($order->status === 'cancelled')
                    <div class="mt-4 bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg text-center">
                        <i class="fas fa-times-circle mr-2"></i>
                        This order has been cancelled
                    </div>
                    @endif
                </div>

                <!-- Current Status Message -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    @switch($order->status)
                        @case('pending')
                            <div class="flex items-center text-amber-600">
                                <i class="fas fa-clock text-2xl mr-3"></i>
                                <div>
                                    <p class="font-semibold">Order Received!</p>
                                    <p class="text-sm text-gray-600">We're preparing to make your waffles</p>
                                </div>
                            </div>
                            @break
                        @case('preparing')
                            <div class="flex items-center text-orange-600">
                                <i class="fas fa-fire text-2xl mr-3 animate-pulse"></i>
                                <div>
                                    <p class="font-semibold">Cooking in Progress!</p>
                                    <p class="text-sm text-gray-600">Your delicious waffles are being made</p>
                                </div>
                            </div>
                            @break
                        @case('ready')
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-check-circle text-2xl mr-3"></i>
                                <div>
                                    <p class="font-semibold">Ready for Pickup!</p>
                                    <p class="text-sm text-gray-600">Your order is ready at {{ $order->pickup_location }}</p>
                                </div>
                            </div>
                            @break
                        @case('completed')
                            <div class="flex items-center text-green-600">
                                <i class="fas fa-smile-beam text-2xl mr-3"></i>
                                <div>
                                    <p class="font-semibold">Order Completed!</p>
                                    <p class="text-sm text-gray-600">Thank you for ordering! Enjoy your waffles!</p>
                                </div>
                            </div>
                            @break
                        @default
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-info-circle text-2xl mr-3"></i>
                                <p>Status: {{ ucfirst($order->status) }}</p>
                            </div>
                    @endswitch
                </div>

                <!-- Payment Status -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-3">Payment</h3>
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center">
                            @if($order->payment_method === 'cash')
                                <i class="fas fa-money-bill-wave text-green-500 text-xl mr-3"></i>
                                <span>Cash on Pickup</span>
                            @elseif($order->payment_method === 'qr')
                                <i class="fas fa-qrcode text-purple-500 text-xl mr-3"></i>
                                <span>QR Payment</span>
                            @else
                                <i class="fas fa-credit-card text-blue-500 text-xl mr-3"></i>
                                <span>Online Payment</span>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 
                               ($order->payment_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    
                    @if($order->payment_method === 'qr' && $order->payment_status === 'pending' && !$order->hasPaymentProof())
                    <a href="{{ route('orders.qr-payment', $order->id) }}" 
                       class="block mt-3 text-center bg-purple-500 text-white py-2 rounded-lg hover:bg-purple-600 transition">
                        <i class="fas fa-qrcode mr-2"></i>
                        Pay Now with QR
                    </a>
                    @elseif($order->hasPaymentProof() && $order->payment_status === 'pending')
                    <p class="mt-3 text-sm text-purple-600 text-center">
                        <i class="fas fa-clock mr-1"></i>
                        Payment proof uploaded. Waiting for verification.
                    </p>
                    @endif
                </div>

                <!-- Order Items -->
                <div class="mb-6">
                    <h3 class="font-bold text-gray-800 mb-3">Order Items</h3>
                    <div class="space-y-2">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                            <div>
                                <p class="font-medium">{{ $item->product_name }}</p>
                                <p class="text-sm text-gray-500">× {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold text-amber-600">RM {{ number_format($item->subtotal, 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pickup Info -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="font-bold text-amber-800 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Pickup Location
                    </h3>
                    <p class="text-amber-700">{{ $order->pickup_location }}</p>
                    <p class="text-sm text-amber-600 mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $order->pickup_time->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <h3 class="font-bold text-gray-800 mb-3">Your Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Name</p>
                    <p class="font-medium">{{ $order->getCustomerName() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Phone</p>
                    <p class="font-medium">{{ $order->getCustomerPhone() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Block</p>
                    <p class="font-medium">{{ $order->getCustomerBlock() }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Unit</p>
                    <p class="font-medium">{{ $order->getCustomerUnitNo() }}</p>
                </div>
            </div>
        </div>

        <!-- Save Link Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-bookmark text-blue-500 mt-1 mr-3"></i>
                <div>
                    <p class="font-semibold text-blue-800">Save this page!</p>
                    <p class="text-sm text-blue-600">
                        Bookmark this page or save the link to check your order status anytime.
                    </p>
                </div>
            </div>
        </div>

        <!-- Register CTA -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg p-6 text-white text-center">
            <h3 class="text-xl font-bold mb-2">🎁 Join Our Loyalty Program!</h3>
            <p class="text-amber-100 mb-4">
                Register now to earn stamps and get discounts on future orders!
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-amber-600 px-6 py-2 rounded-lg font-semibold hover:bg-amber-50 transition">
                Register Now
            </a>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-3 z-50">
        <div class="max-w-2xl mx-auto flex justify-around">
            <a href="{{ route('menu') }}" class="flex flex-col items-center text-gray-600 hover:text-amber-600">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs mt-1">Menu</span>
            </a>
            <button onclick="location.reload()" class="flex flex-col items-center text-amber-600">
                <i class="fas fa-sync-alt text-xl"></i>
                <span class="text-xs mt-1">Refresh</span>
            </button>
            <a href="{{ route('login') }}" class="flex flex-col items-center text-gray-600 hover:text-amber-600">
                <i class="fas fa-user text-xl"></i>
                <span class="text-xs mt-1">Login</span>
            </a>
        </div>
    </nav>

    <div class="h-20"></div><!-- Spacer for bottom nav -->
</body>
</html>

