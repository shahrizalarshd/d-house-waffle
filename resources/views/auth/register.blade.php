<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - D'house Waffle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .waffle-gradient { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 min-h-screen py-8">
    <div class="max-w-md w-full mx-auto px-4">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-6">
                <div class="text-6xl mb-3">🧇</div>
                <h2 class="text-3xl font-bold text-gray-800">Join Us!</h2>
                <p class="text-gray-600 text-sm mt-2">Create account to start ordering waffles</p>
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

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        <i class="fas fa-user mr-1 text-amber-600"></i> Full Name
                    </label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Your name"
                        value="{{ old('name') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        <i class="fas fa-envelope mr-1 text-amber-600"></i> Email
                    </label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="your@email.com"
                        value="{{ old('email') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                        <i class="fas fa-phone mr-1 text-amber-600"></i> Phone Number
                    </label>
                    <input type="text" name="phone" id="phone" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="01X-XXXXXXX"
                        value="{{ old('phone') }}">
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <p class="text-xs text-amber-800 font-semibold mb-3">
                        <i class="fas fa-building mr-1"></i> Apartment Details (Optional)
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="block">
                                Block
                            </label>
                            <input type="text" name="block" id="block"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition text-sm"
                                placeholder="A, B, C..."
                                value="{{ old('block') }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="unit_no">
                                Unit No
                            </label>
                            <input type="text" name="unit_no" id="unit_no"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition text-sm"
                                placeholder="01-01"
                                value="{{ old('unit_no') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        <i class="fas fa-lock mr-1 text-amber-600"></i> Password
                    </label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Create password">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                        <i class="fas fa-lock mr-1 text-amber-600"></i> Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                        placeholder="Confirm password">
                </div>

                <button type="submit" class="w-full waffle-gradient text-white py-4 rounded-lg hover:shadow-xl transition font-bold text-lg">
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm mb-3">Already have an account?</p>
                <a href="{{ route('login') }}" class="text-amber-600 font-semibold hover:text-amber-700 transition">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login Here
                </a>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <p class="text-xs text-gray-500">
                    🧇 Join D'house Waffle community<br>
                    Fresh waffles delivered daily
                </p>
            </div>
        </div>
    </div>
</body>
</html>

