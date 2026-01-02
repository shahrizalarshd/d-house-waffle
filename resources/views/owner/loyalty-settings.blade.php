@extends('layouts.app')

@section('title', 'Loyalty Settings')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">🏆</span>
        <h1 class="text-2xl font-bold text-gray-800">Loyalty & Guest Settings</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('owner.loyalty-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Guest Checkout Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-4 flex items-center">
                <i class="fas fa-user-clock text-green-500 mr-2"></i>
                Guest Checkout
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-semibold">Enable Guest Checkout</p>
                        <p class="text-sm text-gray-500">Allow customers to order without registering</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="guest_checkout_enabled" value="1" 
                            {{ $loyaltySettings->guest_checkout_enabled ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Max Pending Orders per Guest Phone
                    </label>
                    <input type="number" name="guest_pending_limit" min="1" max="10" 
                        value="{{ old('guest_pending_limit', $loyaltySettings->guest_pending_limit) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                    <p class="text-xs text-gray-500 mt-1">Limit pending orders per phone number to prevent spam</p>
                </div>
            </div>
        </div>

        <!-- Stamp Card Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-4 flex items-center">
                <i class="fas fa-stamp text-amber-500 mr-2"></i>
                Loyalty Stamp Card
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-semibold">Enable Loyalty Program</p>
                        <p class="text-sm text-gray-500">Reward repeat customers with discounts</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="loyalty_enabled" value="1" 
                            {{ $loyaltySettings->loyalty_enabled ? 'checked' : '' }}
                            class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Stamps Required
                        </label>
                        <input type="number" name="stamps_required" min="2" max="20" 
                            value="{{ old('stamps_required', $loyaltySettings->stamps_required) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <p class="text-xs text-gray-500 mt-1">Orders to complete card</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Discount Percentage (%)
                        </label>
                        <input type="number" name="stamp_discount_percent" min="1" max="50" step="0.5"
                            value="{{ old('stamp_discount_percent', $loyaltySettings->stamp_discount_percent) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <p class="text-xs text-gray-500 mt-1">Discount when complete</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Discount Valid (Days)
                        </label>
                        <input type="number" name="discount_validity_days" min="7" max="90" 
                            value="{{ old('discount_validity_days', $loyaltySettings->discount_validity_days) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500">
                        <p class="text-xs text-gray-500 mt-1">Days before expiry</p>
                    </div>
                </div>

                <!-- Preview -->
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-amber-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Preview:</strong> Customer orders <strong id="preview-stamps">{{ $loyaltySettings->stamps_required }}</strong> times 
                        → Gets <strong id="preview-discount">{{ $loyaltySettings->stamp_discount_percent }}%</strong> off next order 
                        → Valid for <strong id="preview-days">{{ $loyaltySettings->discount_validity_days }}</strong> days
                    </p>
                </div>
            </div>
        </div>

        <!-- Tier System Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="font-bold text-lg mb-4 flex items-center">
                <i class="fas fa-crown text-yellow-500 mr-2"></i>
                Membership Tiers (Optional)
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-semibold">Enable Tier System</p>
                        <p class="text-sm text-gray-500">Give extra bonuses to loyal customers (Bronze → Silver → Gold)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tiers_enabled" value="1" 
                            {{ $loyaltySettings->tiers_enabled ? 'checked' : '' }}
                            class="sr-only peer" id="tiers-toggle">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                    </label>
                </div>

                <div id="tier-settings" class="{{ $loyaltySettings->tiers_enabled ? '' : 'opacity-50' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Silver Tier -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <span class="text-2xl mr-2">🥈</span>
                                <h4 class="font-semibold">Silver Tier</h4>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Orders Required</label>
                                    <input type="number" name="silver_threshold" min="5" max="100" 
                                        value="{{ old('silver_threshold', $loyaltySettings->silver_threshold) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-400">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Extra Discount (%)</label>
                                    <input type="number" name="silver_bonus_percent" min="0" max="20" step="0.5"
                                        value="{{ old('silver_bonus_percent', $loyaltySettings->silver_bonus_percent) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-400">
                                </div>
                            </div>
                        </div>

                        <!-- Gold Tier -->
                        <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                            <div class="flex items-center mb-3">
                                <span class="text-2xl mr-2">🥇</span>
                                <h4 class="font-semibold">Gold Tier</h4>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Orders Required</label>
                                    <input type="number" name="gold_threshold" min="10" max="200" 
                                        value="{{ old('gold_threshold', $loyaltySettings->gold_threshold) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-400">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Extra Discount (%)</label>
                                    <input type="number" name="gold_bonus_percent" min="0" max="30" step="0.5"
                                        value="{{ old('gold_bonus_percent', $loyaltySettings->gold_bonus_percent) }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-yellow-400">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('owner.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 waffle-gradient text-white rounded-lg hover:opacity-90 transition font-semibold">
                <i class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Preview updates
    document.querySelector('input[name="stamps_required"]').addEventListener('input', function() {
        document.getElementById('preview-stamps').textContent = this.value;
    });
    document.querySelector('input[name="stamp_discount_percent"]').addEventListener('input', function() {
        document.getElementById('preview-discount').textContent = this.value + '%';
    });
    document.querySelector('input[name="discount_validity_days"]').addEventListener('input', function() {
        document.getElementById('preview-days').textContent = this.value;
    });

    // Tier toggle
    document.getElementById('tiers-toggle').addEventListener('change', function() {
        document.getElementById('tier-settings').classList.toggle('opacity-50', !this.checked);
    });
</script>
@endpush
@endsection

