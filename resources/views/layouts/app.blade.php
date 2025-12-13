<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', "D'house Waffle") }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        .waffle-gradient { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
        .waffle-gradient-light { background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 to-amber-50 min-h-screen">
    <!-- Top Navigation -->
    @auth
    <nav class="waffle-gradient shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🧇</span>
                    <a href="{{ route('home') }}" class="text-xl font-bold text-white hover:text-amber-100 transition">
                        D'house Waffle
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('buyer.profile') }}" class="text-sm text-white hover:text-amber-100 font-medium hidden md:inline transition">
                        <i class="fas fa-user-circle mr-1"></i>{{ auth()->user()->name }}
                    </a>
                    @else
                    <span class="text-sm text-white font-medium hidden md:inline">
                        <i class="fas fa-user-circle mr-1"></i>{{ auth()->user()->name }}
                    </span>
                    @endif
                    
                    @if(auth()->user()->role === 'owner' || auth()->user()->role === 'staff')
                    @php
                        $sellerId = auth()->user()->role === 'owner' ? auth()->id() : 
                            \App\Models\User::where('apartment_id', auth()->user()->apartment_id)
                                ->where('role', 'owner')
                                ->value('id');
                        $pendingOrdersCount = \App\Models\Order::where('seller_id', $sellerId)
                            ->where('status', 'pending')
                            ->count();
                    @endphp
                    @if($pendingOrdersCount > 0)
                    <a href="{{ auth()->user()->role === 'owner' ? route('owner.orders') : route('staff.orders') }}" 
                       class="relative bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition flex items-center gap-2 text-white font-medium">
                        <i class="fas fa-bell"></i>
                        <span class="hidden sm:inline text-sm">{{ $pendingOrdersCount }} New</span>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse shadow-lg">
                            {{ $pendingOrdersCount > 9 ? '9+' : $pendingOrdersCount }}
                        </span>
                    </a>
                    @endif
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg transition font-medium">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- Flash Messages now handled by toast notifications below --}}

    @if(session('info'))
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 px-4 py-3 mx-4 mt-4 rounded-r shadow-md flex items-center" role="alert">
        <i class="fas fa-info-circle text-xl mr-3"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    <!-- Main Content -->
    <main class="min-h-screen pb-20">
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile) -->
    @auth
    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-2 border-amber-500 md:hidden z-50">
        <div class="flex justify-around py-3">
            @if(auth()->user()->role === 'customer')
            <a href="{{ route('home') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <span class="text-2xl mb-1">🧇</span>
                <span class="text-xs font-medium">Menu</span>
            </a>
            <a href="{{ route('cart') }}" class="flex flex-col items-center {{ request()->routeIs('cart') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition relative">
                <div class="relative">
                    <i class="fas fa-shopping-cart text-xl mb-1"></i>
                    <!-- Cart Badge -->
                    <span id="cart-badge-mobile" class="hidden absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg border-2 border-white">0</span>
                </div>
                <span class="text-xs font-medium">Cart</span>
            </a>
            <a href="{{ route('buyer.orders') }}" class="flex flex-col items-center {{ request()->routeIs('buyer.orders') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-receipt text-xl mb-1"></i>
                <span class="text-xs font-medium">Orders</span>
            </a>
            <a href="{{ route('buyer.profile') }}" class="flex flex-col items-center {{ request()->routeIs('buyer.profile') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-user-circle text-xl mb-1"></i>
                <span class="text-xs font-medium">Profile</span>
            </a>
            @endif

            @if(auth()->user()->role === 'staff')
            <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('staff.*') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition relative">
                <i class="fas fa-home text-xl mb-1"></i>
                <span class="text-xs font-medium">Dashboard</span>
                @php
                    // Find owner in same apartment
                    $ownerId = \App\Models\User::where('apartment_id', auth()->user()->apartment_id)
                        ->where('role', 'owner')
                        ->value('id');
                    $pendingCount = \App\Models\Order::where('seller_id', $ownerId)
                        ->where('status', 'pending')
                        ->count();
                @endphp
                @if($pendingCount > 0)
                <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
                @endif
            </a>
            <a href="{{ route('staff.orders') }}" class="flex flex-col items-center {{ request()->routeIs('staff.orders') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-clipboard-list text-xl mb-1"></i>
                <span class="text-xs font-medium">Orders</span>
            </a>
            @endif

            @if(auth()->user()->role === 'owner')
            <a href="{{ route('owner.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('owner.dashboard') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition relative">
                <i class="fas fa-chart-line text-xl mb-1"></i>
                <span class="text-xs font-medium">Dashboard</span>
                @php
                    $pendingCount = \App\Models\Order::where('seller_id', auth()->id())
                        ->where('status', 'pending')
                        ->count();
                @endphp
                @if($pendingCount > 0)
                <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
                @endif
            </a>
            <a href="{{ route('owner.sales-report') }}" class="flex flex-col items-center {{ request()->routeIs('owner.sales-report') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-chart-bar text-xl mb-1"></i>
                <span class="text-xs font-medium">Reports</span>
            </a>
            <a href="{{ route('owner.products') }}" class="flex flex-col items-center {{ request()->routeIs('owner.products') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-utensils text-xl mb-1"></i>
                <span class="text-xs font-medium">Menu</span>
            </a>
            <a href="{{ route('owner.settings') }}" class="flex flex-col items-center {{ request()->routeIs('owner.settings') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-cog text-xl mb-1"></i>
                <span class="text-xs font-medium">Settings</span>
            </a>
            @endif

            @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('super.dashboard') }}" class="flex flex-col items-center {{ request()->routeIs('super.*') ? 'text-amber-600' : 'text-gray-500' }} hover:text-amber-600 transition">
                <i class="fas fa-crown text-xl mb-1"></i>
                <span class="text-xs font-medium">Super</span>
            </a>
            @endif
        </div>
    </nav>
    @endauth

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Show Laravel Flash Messages as Toast -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success');
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('error') }}', 'error');
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('warning') }}', 'warning');
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('info') }}', 'info');
        });
    </script>
    @endif

    <!-- Toast Notification Script -->
    <script>
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const icons = {
            success: '✅',
            error: '❌',
            info: 'ℹ️',
            warning: '⚠️'
        };
        
        const colors = {
            success: 'from-green-500 to-emerald-600',
            error: 'from-red-500 to-rose-600',
            info: 'from-blue-500 to-indigo-600',
            warning: 'from-amber-500 to-orange-500'
        };
        
        toast.className = `bg-gradient-to-r ${colors[type]} text-white px-6 py-4 rounded-lg shadow-2xl transform transition-all duration-300 ease-out flex items-center gap-3 min-w-[300px] max-w-md`;
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(400px)';
        
        toast.innerHTML = `
            <span class="text-2xl">${icons[type]}</span>
            <span class="font-semibold flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 text-xl font-bold ml-2">&times;</button>
        `;
        
        container.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(400px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Legacy alert support (untuk backward compatibility)
    window.showNotification = showToast;

    // Custom Confirm Dialog (replaces browser confirm)
    window.customConfirm = function(message, callback) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        overlay.style.animation = 'fadeIn 0.2s ease-out';
        
        // Create dialog
        const dialog = document.createElement('div');
        dialog.className = 'bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all';
        dialog.style.animation = 'scaleIn 0.2s ease-out';
        
        dialog.innerHTML = `
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-amber-100 to-orange-100 rounded-full">
                    <span class="text-3xl">🤔</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-3">Confirmation</h3>
                <p class="text-gray-600 text-center mb-6">${message}</p>
                <div class="flex gap-3">
                    <button onclick="this.closest('.fixed').remove()" 
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Cancel
                    </button>
                    <button id="confirm-btn" 
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg hover:from-amber-600 hover:to-orange-600 transition font-semibold shadow-lg">
                        Confirm
                    </button>
                </div>
            </div>
        `;
        
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
        
        // Handle confirm button
        document.getElementById('confirm-btn').onclick = function() {
            overlay.remove();
            if (callback) callback();
        };
        
        // Close on overlay click
        overlay.onclick = function(e) {
            if (e.target === overlay) {
                overlay.remove();
            }
        };
    };

    // Add animations
    const animStyle = document.createElement('style');
    animStyle.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(animStyle);
    </script>

    <!-- Cart Badge Update Script -->
    <script>
    function updateCartBadge() {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        const badge = document.getElementById('cart-badge-mobile');
        
        if (badge) {
            if (totalItems > 0) {
                badge.classList.remove('hidden');
                badge.textContent = totalItems > 99 ? '99+' : totalItems;
                
                // Add bounce animation
                badge.style.animation = 'none';
                setTimeout(() => {
                    badge.style.animation = 'bounce 0.5s ease';
                }, 10);
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    // Update badge on page load
    document.addEventListener('DOMContentLoaded', updateCartBadge);

    // Update badge whenever localStorage changes (for multi-tab support)
    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            updateCartBadge();
        }
    });

    // Add bounce animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    `;
    document.head.appendChild(style);
    </script>

    {{-- Laravel Echo / Reverb Real-Time Setup --}}
    @auth
    @if(auth()->user()->role === 'owner' || auth()->user()->role === 'staff')
    <script>
    // Initialize Laravel Echo with Reverb
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: 'local-key',
        wsHost: window.location.hostname,
        wsPort: 8080,
        wssPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });

    // Get seller ID
    const sellerId = {{ auth()->user()->role === 'owner' ? auth()->id() : 
        (\App\Models\User::where('apartment_id', auth()->user()->apartment_id)
            ->where('role', 'owner')
            ->value('id') ?? 0) }};

    // Listen for new orders
    Echo.channel('seller.' + sellerId)
        .listen('.order.placed', (e) => {
            console.log('New order received!', e);
            
            // Play notification sound
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGH0fPTgjMGHm7A7+OZUQ0PVqzn77BdGAg+ltryxnMpBSuBzvLZiTYIGGe77eeeTRAMUKfj8LZjHAY4kdfyzHksBSR3x/DdkEAKFF606+uoVRQKRp/g8r5sIQUxh9Hz04IzBh5uwO/jmVENDlas5++wXRgIPpba8sZzKQUrgc7y2Yk2CBhnu+3nnk0QDFCn4/C2YxwGOJHX8sx5LAUkd8fw3ZBACh');
            audio.play().catch(e => console.log('Audio play failed:', e));
            
            // Show toast notification
            showToast('🔔 New Order: ' + e.order_no + ' - RM ' + parseFloat(e.total_amount).toFixed(2), 'success');
            
            // Show browser notification if permitted
            if (Notification.permission === "granted") {
                new Notification("🧇 D'house Waffle - New Order!", {
                    body: e.order_no + ' - RM ' + parseFloat(e.total_amount).toFixed(2) + '\nFrom: ' + e.buyer_name,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    tag: 'order-' + e.order_id,
                    requireInteraction: true
                });
            }
            
            // Update badge counts
            setTimeout(() => {
                location.reload(); // Refresh to update badges
            }, 1000);
        });

    // Request notification permission on page load
    if (Notification.permission === "default") {
        Notification.requestPermission();
    }

    console.log('✅ Real-time notifications active for seller ' + sellerId);
    </script>
    @endif
    @endauth

    @stack('scripts')
</body>
</html>

