<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - D'house Waffle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="text-7xl mb-4">🧇</div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">D'house Waffle</h1>
            <p class="text-gray-600 mb-2">Freshly Made, Delivered Fresh</p>
            <p class="text-sm text-gray-500 mb-6">Order delicious handmade waffles for your apartment</p>
            
            <!-- Main CTA - Browse Menu (Guest Checkout) -->
            <a href="{{ route('menu') }}" class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-4 rounded-xl hover:from-amber-600 hover:to-orange-600 transition font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-4">
                <i class="fas fa-utensils mr-2"></i>
                View Menu & Order
            </a>
            
            <p class="text-xs text-gray-400 mb-4">No account needed to order!</p>
            
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">or sign in for rewards</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('login') }}" class="block bg-white text-amber-600 border-2 border-amber-500 py-3 rounded-lg hover:bg-amber-50 transition font-semibold">
                    <i class="fas fa-sign-in-alt mr-1"></i>
                    Login
                </a>
                <a href="{{ route('register') }}" class="block bg-amber-50 text-amber-700 border-2 border-amber-200 py-3 rounded-lg hover:bg-amber-100 transition font-semibold">
                    <i class="fas fa-user-plus mr-1"></i>
                    Register
                </a>
            </div>
            
            <!-- Loyalty Promo -->
            <div class="mt-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 border border-amber-200">
                <p class="text-sm text-amber-800">
                    <i class="fas fa-gift text-amber-500 mr-1"></i>
                    <strong>Loyalty Program:</strong> Order 5 times, get 10% OFF!
                </p>
                <p class="text-xs text-amber-600 mt-1">Register now to start collecting stamps</p>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-1">
                    <i class="fas fa-clock text-amber-500 mr-1"></i>
                    Operating Hours: 9:00 AM - 9:00 PM
                </p>
                <p class="text-xs text-gray-500">
                    <i class="fas fa-map-marker-alt text-amber-500 mr-1"></i>
                    Pickup at Lobby Utama (Ground Floor)
                </p>
            </div>
        </div>
    </div>
</body>
</html>
