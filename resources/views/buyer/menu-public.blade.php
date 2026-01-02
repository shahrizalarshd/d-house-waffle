<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🧇 D'house Waffle - Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .waffle-gradient {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen pb-20">
    <!-- Header -->
    <header class="waffle-gradient text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🧇</span>
                <h1 class="text-xl font-bold">D'house Waffle</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cart') }}" class="relative bg-white/20 p-2 rounded-full hover:bg-white/30 transition">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                </a>
                <a href="{{ route('login') }}" class="bg-white text-amber-600 px-4 py-2 rounded-lg font-semibold hover:bg-amber-50 transition">
                    Login
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Banner Carousel -->
        @if($banners->count() > 0)
        <div class="relative rounded-xl overflow-hidden shadow-lg mb-6" id="banner-carousel">
            <!-- Slides -->
            <div class="relative h-48 md:h-64 lg:h-80">
                @foreach($banners as $index => $banner)
                <div class="banner-slide absolute inset-0 transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                     data-index="{{ $index }}">
                    @if($banner->link_url && $banner->link_type !== 'none')
                    <a href="{{ $banner->link_url }}" target="{{ $banner->link_type === 'external' ? '_blank' : '_self' }}" class="block w-full h-full">
                    @endif
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" 
                             class="w-full h-full object-cover">
                        <!-- Optional overlay with title -->
                        @if($banner->title || $banner->subtitle)
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end">
                            <div class="p-4 md:p-6 text-white">
                                @if($banner->title)
                                <h2 class="text-xl md:text-2xl font-bold">{{ $banner->title }}</h2>
                                @endif
                                @if($banner->subtitle)
                                <p class="text-sm md:text-base text-white/90">{{ $banner->subtitle }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    @if($banner->link_url && $banner->link_type !== 'none')
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
            
            <!-- Navigation Dots -->
            @if($banners->count() > 1)
            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex gap-2">
                @foreach($banners as $index => $banner)
                <button class="banner-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white w-6' : 'bg-white/50' }}"
                        onclick="goToSlide({{ $index }})" data-index="{{ $index }}"></button>
                @endforeach
            </div>
            
            <!-- Arrow Navigation (for desktop) -->
            <button onclick="prevSlide()" class="hidden md:flex absolute left-3 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white w-10 h-10 rounded-full items-center justify-center transition">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextSlide()" class="hidden md:flex absolute right-3 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white w-10 h-10 rounded-full items-center justify-center transition">
                <i class="fas fa-chevron-right"></i>
            </button>
            @endif
        </div>
        @else
        <!-- Default Welcome Banner (when no banners uploaded) -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 mb-6 text-white">
            <h1 class="text-3xl font-bold mb-2">🧇 Welcome to D'house Waffle!</h1>
            <p class="text-amber-50 mb-4">Delicious handmade waffles delivered fresh to your doorstep</p>
            
            @if($loyaltySettings && $loyaltySettings->loyalty_enabled)
            <div class="bg-white/20 rounded-lg p-3 inline-block">
                <p class="text-sm">
                    <i class="fas fa-gift mr-1"></i>
                    <strong>Loyalty Program:</strong> Order {{ $loyaltySettings->stamps_required }} times → Get {{ $loyaltySettings->stamp_discount_percent }}% OFF!
                    <a href="{{ route('register') }}" class="underline ml-1">Register now</a>
                </p>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Loyalty Program Info (shown below carousel if banners exist) -->
        @if($banners->count() > 0 && $loyaltySettings && $loyaltySettings->loyalty_enabled)
        <div class="bg-gradient-to-r from-amber-100 to-orange-100 border border-amber-200 rounded-lg p-4 mb-6">
            <p class="text-amber-800 text-center">
                <i class="fas fa-gift text-amber-500 mr-2"></i>
                <strong>Loyalty Program:</strong> Order {{ $loyaltySettings->stamps_required }} times → Get {{ $loyaltySettings->stamp_discount_percent }}% OFF!
                <a href="{{ route('register') }}" class="text-amber-600 underline ml-2 hover:text-amber-700">Register now</a>
            </p>
        </div>
        @endif

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('menu') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Search products..." 
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                </div>
                <div class="flex gap-2">
                    <select name="category" 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="waffle-gradient text-white px-6 py-3 rounded-lg hover:opacity-90">
                        <i class="fas fa-filter"></i>
                    </button>
                    @if(request('search') || request('category'))
                    <a href="{{ route('menu') }}" class="bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>

        @if($products->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="text-6xl mb-4">🧇</div>
            <p class="text-gray-600">No waffles available right now. Check back soon!</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                    class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                    <span class="text-6xl">🧇</span>
                </div>
                @endif
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                        @if($product->category)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                            {{ $product->category->icon }} {{ $product->category->name }}
                        </span>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xl font-bold text-amber-600">RM {{ number_format($product->price, 2) }}</span>
                        <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                            class="waffle-gradient text-white px-4 py-2 rounded hover:opacity-90 transition">
                            Add to Cart
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-check-circle text-green-500"></i> Fresh & Handmade
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 py-2 z-50">
        <div class="max-w-7xl mx-auto flex justify-around">
            <a href="{{ route('menu') }}" class="flex flex-col items-center text-amber-600">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs mt-1">Menu</span>
            </a>
            <a href="{{ route('cart') }}" class="flex flex-col items-center text-gray-600 hover:text-amber-600 relative">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span class="text-xs mt-1">Cart</span>
                <span id="cart-badge-bottom" class="absolute -top-1 right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
            </a>
            <a href="{{ route('login') }}" class="flex flex-col items-center text-gray-600 hover:text-amber-600">
                <i class="fas fa-user text-xl"></i>
                <span class="text-xs mt-1">Login</span>
            </a>
        </div>
    </nav>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    <script>
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');

        function addToCart(productId, productName, price) {
            const existingItem = cart.find(item => item.product_id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    product_id: productId,
                    name: productName,
                    price: price,
                    quantity: 1
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartBadge();
            showToast('🧇 Added to cart!', 'success');
        }

        function updateCartBadge() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badges = [
                document.getElementById('cart-badge'),
                document.getElementById('cart-badge-bottom')
            ];
            
            badges.forEach(badge => {
                if (badge) {
                    if (totalItems > 0) {
                        badge.textContent = totalItems > 99 ? '99+' : totalItems;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            
            toast.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            toast.textContent = message;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => toast.classList.remove('translate-x-full'), 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Initialize cart badge on page load
        updateCartBadge();
        
        // Banner Carousel
        const slides = document.querySelectorAll('.banner-slide');
        const dots = document.querySelectorAll('.banner-dot');
        let currentSlide = 0;
        let slideInterval;
        
        function goToSlide(index) {
            // Hide current slide
            slides[currentSlide]?.classList.remove('opacity-100');
            slides[currentSlide]?.classList.add('opacity-0');
            dots[currentSlide]?.classList.remove('bg-white', 'w-6');
            dots[currentSlide]?.classList.add('bg-white/50');
            
            // Show new slide
            currentSlide = index;
            slides[currentSlide]?.classList.add('opacity-100');
            slides[currentSlide]?.classList.remove('opacity-0');
            dots[currentSlide]?.classList.add('bg-white', 'w-6');
            dots[currentSlide]?.classList.remove('bg-white/50');
            
            // Reset interval
            resetInterval();
        }
        
        function nextSlide() {
            const next = (currentSlide + 1) % slides.length;
            goToSlide(next);
        }
        
        function prevSlide() {
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            goToSlide(prev);
        }
        
        function resetInterval() {
            clearInterval(slideInterval);
            if (slides.length > 1) {
                slideInterval = setInterval(nextSlide, 5000);
            }
        }
        
        // Start auto-rotation if multiple slides
        if (slides.length > 1) {
            slideInterval = setInterval(nextSlide, 5000);
        }
        
        // Pause on hover
        const carousel = document.getElementById('banner-carousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', () => clearInterval(slideInterval));
            carousel.addEventListener('mouseleave', resetInterval);
        }
    </script>
</body>
</html>

