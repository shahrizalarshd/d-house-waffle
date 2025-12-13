<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - D'house Waffle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .waffle-gradient { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="text-6xl mb-3">🧇</div>
                <h2 class="text-3xl font-bold text-gray-800">Welcome Back!</h2>
                <p class="text-gray-600 text-sm mt-2">Login to order delicious waffles</p>
            </div>
            
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-r mb-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-xl mr-2 mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        <i class="fas fa-envelope mr-1 text-amber-600"></i> Email
                    </label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="your@email.com"
                        value="{{ old('email') }}">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        <i class="fas fa-lock mr-1 text-amber-600"></i> Password
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Enter your password">
                </div>

                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="mr-2 w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                        <span class="text-sm text-gray-700">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full waffle-gradient text-white py-4 rounded-lg hover:shadow-xl transition font-bold text-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login to Order
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm mb-3">Don't have an account?</p>
                <a href="{{ route('register') }}" class="text-amber-600 font-semibold hover:text-amber-700 transition">
                    Register Now <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-clock mr-1"></i>Operating: 9:00 AM - 9:00 PM<br>
                    <i class="fas fa-map-marker-alt mr-1"></i>Pickup: Lobby Utama
                </p>
            </div>
        </div>
    </div>
</body>
</html>

