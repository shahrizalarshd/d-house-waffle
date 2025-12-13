<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - D'house Waffle</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-amber-500 via-orange-500 to-amber-600 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <div class="text-6xl mb-4">🧇</div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">D'house Waffle</h1>
            <p class="text-gray-600 mb-2">Freshly Made, Delivered Fresh</p>
            <p class="text-sm text-gray-500 mb-8">Order delicious handmade waffles for your apartment</p>
            
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg hover:from-amber-600 hover:to-orange-600 transition font-semibold">
                    Login to Order
                </a>
                <a href="{{ route('register') }}" class="block w-full bg-white text-amber-600 border-2 border-amber-500 py-3 rounded-lg hover:bg-amber-50 transition font-semibold">
                    Register Now
                </a>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 mb-2">🕐 Operating Hours: 9:00 AM - 9:00 PM</p>
                <p class="text-xs text-gray-500">📍 Pickup at Lobby Utama (Ground Floor)</p>
            </div>
        </div>
    </div>
</body>
</html>
