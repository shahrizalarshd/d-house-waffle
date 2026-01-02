@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">⚙️</span>
        <h1 class="text-2xl font-bold text-gray-800">D'house Waffle Settings</h1>
    </div>

    <!-- Quick Access Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <a href="{{ route('owner.banners') }}" class="bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-xl p-4 text-center hover:shadow-lg transition transform hover:-translate-y-0.5">
            <i class="fas fa-images text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Banners</p>
        </a>
        <a href="{{ route('owner.loyalty-settings') }}" class="bg-gradient-to-br from-amber-500 to-orange-500 text-white rounded-xl p-4 text-center hover:shadow-lg transition transform hover:-translate-y-0.5">
            <i class="fas fa-gift text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Loyalty</p>
        </a>
        <a href="{{ route('owner.products') }}" class="bg-gradient-to-br from-green-500 to-teal-500 text-white rounded-xl p-4 text-center hover:shadow-lg transition transform hover:-translate-y-0.5">
            <i class="fas fa-utensils text-2xl mb-2"></i>
            <p class="font-semibold text-sm">Products</p>
        </a>
        <a href="{{ route('owner.profile') }}" class="bg-gradient-to-br from-blue-500 to-indigo-500 text-white rounded-xl p-4 text-center hover:shadow-lg transition transform hover:-translate-y-0.5">
            <i class="fas fa-qrcode text-2xl mb-2"></i>
            <p class="font-semibold text-sm">QR Setup</p>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-xl p-6">
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

        <form method="POST" action="{{ route('owner.settings.update') }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="service_fee_percent">
                    <i class="fas fa-percentage mr-1 text-amber-600"></i> Service Fee (%)
                </label>
                <input type="number" step="0.01" min="0" max="100" name="service_fee_percent" id="service_fee_percent" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition text-lg font-semibold"
                    value="{{ old('service_fee_percent', $apartment->service_fee_percent) }}">
                <p class="text-xs text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Platform fee charged on each order (0-100%)
                </p>
                <p class="text-xs text-amber-600 mt-1">
                    💡 <strong>Tip:</strong> Set to 0% for no-fee direct sales
                </p>
                
                @if($apartment->service_fee_percent == 0)
                <div class="mt-3 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-r p-3">
                    <p class="text-sm text-green-800">
                        <i class="fas fa-gift mr-1"></i>
                        <strong>Direct Sales Mode:</strong> 100% revenue goes to D'house Waffle!
                    </p>
                </div>
                @endif
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="pickup_location">
                    Pickup Location
                </label>
                <input type="text" name="pickup_location" id="pickup_location" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    value="{{ old('pickup_location', $apartment->pickup_location) }}">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2" for="pickup_start_time">
                        Pickup Start Time
                    </label>
                    <input type="time" name="pickup_start_time" id="pickup_start_time" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                        value="{{ old('pickup_start_time', $apartment->pickup_start_time ? substr($apartment->pickup_start_time, 0, 5) : '') }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2" for="pickup_end_time">
                        Pickup End Time
                    </label>
                    <input type="time" name="pickup_end_time" id="pickup_end_time" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                        value="{{ old('pickup_end_time', $apartment->pickup_end_time ? substr($apartment->pickup_end_time, 0, 5) : '') }}">
                </div>
            </div>

            <!-- Payment Methods Settings -->
            <div class="border-t-2 border-gray-200 pt-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-amber-600 mr-2"></i>Payment Methods
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Enable or disable payment methods for customers. Disabled methods will be hidden from checkout.
                </p>

                <!-- Online Payment -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-3 border-2 border-transparent hover:border-blue-300 transition">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="payment_online_enabled" value="1" 
                            {{ old('payment_online_enabled', $apartment->payment_online_enabled ?? true) ? 'checked' : '' }}
                            class="w-6 h-6 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">💳</span>
                                <span class="font-bold text-gray-800">Online Payment</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Pay via FPX, Card, or E-wallet (Billplz)
                            </p>
                        </div>
                    </label>
                </div>

                <!-- QR Payment -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-3 border-2 border-transparent hover:border-purple-300 transition">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="payment_qr_enabled" value="1" 
                            {{ old('payment_qr_enabled', $apartment->payment_qr_enabled ?? true) ? 'checked' : '' }}
                            class="w-6 h-6 rounded border-gray-300 text-purple-600 focus:ring-purple-500 focus:ring-offset-0 cursor-pointer">
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">📱</span>
                                <span class="font-bold text-gray-800">QR Payment</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Scan QR & pay with DuitNow/TNG
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Cash Payment -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 mb-3 border-2 border-transparent hover:border-green-300 transition">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="payment_cash_enabled" value="1" 
                            {{ old('payment_cash_enabled', $apartment->payment_cash_enabled ?? true) ? 'checked' : '' }}
                            class="w-6 h-6 rounded border-gray-300 text-green-600 focus:ring-green-500 focus:ring-offset-0 cursor-pointer">
                        <div class="ml-4 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">💵</span>
                                <span class="font-bold text-gray-800">Cash on Pickup</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Pay cash when collecting your waffles
                            </p>
                        </div>
                    </label>
                </div>

                <div class="mt-4 bg-amber-50 border-l-4 border-amber-500 rounded-r p-3">
                    <p class="text-sm text-amber-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Note:</strong> At least one payment method must be enabled for customers to place orders.
                    </p>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg hover:shadow-lg transition font-bold">
                <i class="fas fa-save mr-2"></i>Update Settings
            </button>
        </form>
    </div>
</div>
@endsection

