@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">🛒</span>
        <h1 class="text-2xl font-bold text-gray-800">Checkout</h1>
    </div>

    @guest
    <!-- Guest Checkout Option -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">How would you like to order?</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Login Option -->
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-amber-500 transition cursor-pointer" onclick="window.location.href='{{ route('login') }}?redirect=checkout'">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Login / Register</h4>
                        <p class="text-sm text-gray-500">Earn loyalty rewards!</p>
                    </div>
                </div>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Collect stamps for discounts</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Track order history</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Faster checkout next time</li>
                </ul>
                <button class="w-full mt-4 bg-amber-500 text-white py-2 rounded-lg hover:bg-amber-600 transition">
                    Login Now
                </button>
            </div>

            <!-- Guest Option -->
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-500 transition cursor-pointer" id="guest-option" onclick="showGuestForm()">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bolt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold">Quick Order</h4>
                        <p class="text-sm text-gray-500">No account needed</p>
                    </div>
                </div>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Order in seconds</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Just enter your details</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Get tracking link</li>
                </ul>
                <button class="w-full mt-4 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
                    Continue as Guest
                </button>
            </div>
        </div>
    </div>

    <!-- Guest Details Form (Hidden by default) -->
    <div id="guest-form" class="bg-white rounded-lg shadow p-6 mb-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">📝 Your Details</h3>
            <button onclick="hideGuestForm()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" id="guest_name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                    placeholder="Your name">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                <input type="tel" id="guest_phone" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                    placeholder="012-3456789">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Block *</label>
                <input type="text" id="guest_block" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                    placeholder="A">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit No *</label>
                <input type="text" id="guest_unit_no" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-amber-500"
                    placeholder="12-05">
            </div>
        </div>
        
        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm text-amber-800">
                <i class="fas fa-lightbulb mr-1"></i>
                <strong>Tip:</strong> Register to earn loyalty stamps and get discounts on future orders!
            </p>
        </div>
    </div>
    @endguest

    @auth
    <!-- Logged in user info -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-amber-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-sm text-gray-500">
                    Block {{ auth()->user()->block ?? '-' }}, Unit {{ auth()->user()->unit_no ?? '-' }}
                </p>
            </div>
        </div>
        
        @if($loyaltySummary && $loyaltySummary['enabled'])
        <div class="mt-4 p-3 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-800">
                        {{ $loyaltySummary['tier_emoji'] }} {{ ucfirst($loyaltySummary['tier']) }} Member
                    </p>
                    <p class="text-xs text-amber-600">
                        {{ $loyaltySummary['stamps'] }}/{{ $loyaltySummary['stamps_required'] }} stamps collected
                    </p>
                </div>
                @if($loyaltySummary['has_discount'])
                <div class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold animate-pulse">
                    {{ $loyaltySummary['discount_percent'] }}% OFF!
                </div>
                @elseif($loyaltySummary['is_close_to_discount'])
                <div class="text-amber-600 text-sm">
                    <i class="fas fa-fire"></i> Almost there!
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    @endauth

    <div id="checkout-items" class="space-y-4 mb-6">
        <!-- Items will be loaded by JavaScript -->
    </div>

    <!-- Payment Method Selection -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Payment Method</h3>
        
        <div class="space-y-3">
            @php
                $hasAnyPaymentMethod = ($apartment->payment_online_enabled ?? true) || 
                                       ($apartment->payment_qr_enabled ?? true) || 
                                       ($apartment->payment_cash_enabled ?? true);
                $firstEnabled = null;
                if ($apartment->payment_online_enabled ?? true) $firstEnabled = 'online';
                elseif ($apartment->payment_qr_enabled ?? true) $firstEnabled = 'qr';
                elseif ($apartment->payment_cash_enabled ?? true) $firstEnabled = 'cash';
            @endphp

            @if(!$hasAnyPaymentMethod)
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                    <div>
                        <p class="font-bold">Payment methods are currently unavailable</p>
                        <p class="text-sm">Please contact the seller to enable payment options.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Online Payment -->
            @if($apartment->payment_online_enabled ?? true)
            <label class="flex items-center p-4 border-2 {{ $firstEnabled === 'online' ? 'border-amber-500' : '' }} rounded-lg cursor-pointer hover:border-amber-500 transition payment-method-option" data-method="online">
                <input type="radio" name="payment_method" value="online" {{ $firstEnabled === 'online' ? 'checked' : '' }}
                    class="w-5 h-5 text-amber-600">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">💳 Online Payment</div>
                    <div class="text-sm text-gray-600">
                        Pay via FPX, Card, or E-wallet (Billplz)
                    </div>
                </div>
                <i class="fas fa-credit-card text-amber-600 text-2xl"></i>
            </label>
            @endif

            <!-- QR Payment -->
            @if($apartment->payment_qr_enabled ?? true)
            <label class="flex items-center p-4 border-2 {{ $firstEnabled === 'qr' ? 'border-purple-500' : '' }} rounded-lg cursor-pointer hover:border-purple-500 transition payment-method-option" data-method="qr">
                <input type="radio" name="payment_method" value="qr" {{ $firstEnabled === 'qr' ? 'checked' : '' }}
                    class="w-5 h-5 text-purple-600">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">📱 QR Payment</div>
                    <div class="text-sm text-gray-600">
                        Scan QR & pay with DuitNow/TNG
                    </div>
                </div>
                <i class="fas fa-qrcode text-purple-600 text-2xl"></i>
            </label>
            @endif

            <!-- Cash Payment -->
            @if($apartment->payment_cash_enabled ?? true)
            <label class="flex items-center p-4 border-2 {{ $firstEnabled === 'cash' ? 'border-green-500' : '' }} rounded-lg cursor-pointer hover:border-green-500 transition payment-method-option" data-method="cash">
                <input type="radio" name="payment_method" value="cash" {{ $firstEnabled === 'cash' ? 'checked' : '' }}
                    class="w-5 h-5 text-green-600">
                <div class="ml-3 flex-1">
                    <div class="font-semibold text-gray-800">💵 Cash on Pickup</div>
                    <div class="text-sm text-gray-600">
                        Pay cash when collecting your waffles
                    </div>
                </div>
                <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
            </label>
            @endif
        </div>

        <!-- QR Payment Note -->
        <div id="qr-payment-note" class="bg-purple-50 border border-purple-200 p-4 rounded-lg mt-4 hidden">
            <div class="flex items-start">
                <i class="fas fa-qrcode text-purple-600 mt-1 mr-3 text-2xl"></i>
                <div class="text-sm text-purple-800">
                    <strong>QR Payment Instructions:</strong>
                    <ul class="list-disc ml-5 mt-2 space-y-1">
                        <li>You will see seller's QR code after placing order</li>
                        <li>Scan with any e-wallet or banking app</li>
                        <li>Pay exact amount: <strong id="qr-total">RM 0.00</strong></li>
                        <li>Upload payment screenshot as proof</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cash Payment Note -->
        <div id="cash-payment-note" class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mt-4 hidden">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                <div class="text-sm text-yellow-800">
                    <strong>Cash on Pickup Instructions:</strong>
                    <ul class="list-disc ml-5 mt-2 space-y-1">
                        <li>Prepare exact amount: <strong id="cash-total">RM 0.00</strong></li>
                        <li>Meet seller at pickup location</li>
                        <li>Pay cash and collect your order</li>
                        <li>Seller will confirm payment received</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-bold mb-3">Order Summary</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span id="subtotal">RM 0.00</span>
            </div>
            
            @auth
            @if($loyaltySummary && $loyaltySummary['has_discount'])
            <div class="flex justify-between text-green-600">
                <span>🎁 Loyalty Discount ({{ $loyaltySummary['discount_percent'] }}%):</span>
                <span id="loyalty-discount">- RM 0.00</span>
            </div>
            @endif
            @endauth
            
            <div class="flex justify-between text-gray-500">
                <span>Service Fee:</span>
                <span id="platform-fee">RM 0.00</span>
            </div>
            <div class="flex justify-between text-lg font-bold mt-4 pt-4 border-t-2 border-amber-500">
                <span>🧇 Total:</span>
                <span class="text-amber-600" id="total">RM 0.00</span>
            </div>
        </div>
    </div>

    <button onclick="placeOrder()" id="place-order-btn"
        class="w-full waffle-gradient text-white py-4 rounded-lg hover:shadow-lg transition font-bold text-lg">
        🧇 Place Order Now
    </button>
</div>

@push('scripts')
<script>
let cart = JSON.parse(localStorage.getItem('cart') || '[]');
const isGuest = {{ auth()->check() ? 'false' : 'true' }};
const hasLoyaltyDiscount = {{ (auth()->check() && isset($loyaltySummary) && $loyaltySummary['has_discount']) ? 'true' : 'false' }};
const loyaltyDiscountPercent = {{ (auth()->check() && isset($loyaltySummary)) ? $loyaltySummary['discount_percent'] : 0 }};
let guestFormShown = false;

function showGuestForm() {
    document.getElementById('guest-form').classList.remove('hidden');
    document.getElementById('guest-option').classList.add('hidden');
    guestFormShown = true;
}

function hideGuestForm() {
    document.getElementById('guest-form').classList.add('hidden');
    document.getElementById('guest-option').classList.remove('hidden');
    guestFormShown = false;
}

function renderCheckout() {
    const container = document.getElementById('checkout-items');
    
    if (cart.length === 0) {
        window.location.href = '{{ route("cart") }}';
        return;
    }

    container.innerHTML = cart.map(item => `
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold">${item.name}</h3>
                    <p class="text-sm text-gray-600">Quantity: ${item.quantity}</p>
                </div>
                <span class="font-bold text-amber-600">RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = 0;
    
    if (hasLoyaltyDiscount) {
        discount = subtotal * (loyaltyDiscountPercent / 100);
        const discountEl = document.getElementById('loyalty-discount');
        if (discountEl) {
            discountEl.textContent = `- RM ${discount.toFixed(2)}`;
        }
    }
    
    const afterDiscount = subtotal - discount;
    const platformFee = afterDiscount * {{ $apartment->service_fee_percent ?? 0 }} / 100;
    const total = afterDiscount + platformFee;

    document.getElementById('subtotal').textContent = `RM ${subtotal.toFixed(2)}`;
    document.getElementById('platform-fee').textContent = `RM ${platformFee.toFixed(2)}`;
    document.getElementById('total').textContent = `RM ${total.toFixed(2)}`;
    
    const cashTotal = document.getElementById('cash-total');
    const qrTotal = document.getElementById('qr-total');
    if (cashTotal) cashTotal.textContent = `RM ${total.toFixed(2)}`;
    if (qrTotal) qrTotal.textContent = `RM ${total.toFixed(2)}`;
}

// Payment method selection handler
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cashNote = document.getElementById('cash-payment-note');
    const qrNote = document.getElementById('qr-payment-note');
    const paymentOptions = document.querySelectorAll('.payment-method-option');
    
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Update border highlight
            paymentOptions.forEach(opt => {
                opt.classList.remove('border-amber-500', 'bg-amber-50', 'border-green-500', 'bg-green-50', 'border-purple-500', 'bg-purple-50');
            });
            
            // Show/hide payment notes
            if (cashNote) cashNote.classList.add('hidden');
            if (qrNote) qrNote.classList.add('hidden');
            
            if (this.value === 'qr') {
                if (qrNote) qrNote.classList.remove('hidden');
                const selectedOpt = document.querySelector('[data-method="qr"]');
                if (selectedOpt) selectedOpt.classList.add('border-purple-500', 'bg-purple-50');
            } else if (this.value === 'cash') {
                if (cashNote) cashNote.classList.remove('hidden');
                const selectedOpt = document.querySelector('[data-method="cash"]');
                if (selectedOpt) selectedOpt.classList.add('border-green-500', 'bg-green-50');
            } else {
                const selectedOpt = document.querySelector('[data-method="online"]');
                if (selectedOpt) selectedOpt.classList.add('border-amber-500', 'bg-amber-50');
            }
        });
    });
    
    // Initialize first available option
    const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
    if (checkedRadio) {
        const method = checkedRadio.value;
        const selectedOpt = document.querySelector(`[data-method="${method}"]`);
        if (selectedOpt) {
            if (method === 'online') {
                selectedOpt.classList.add('border-amber-500', 'bg-amber-50');
            } else if (method === 'qr') {
                selectedOpt.classList.add('border-purple-500', 'bg-purple-50');
            } else if (method === 'cash') {
                selectedOpt.classList.add('border-green-500', 'bg-green-50');
            }
        }
    }
});

async function placeOrder() {
    const paymentMethodEl = document.querySelector('input[name="payment_method"]:checked');
    if (!paymentMethodEl) {
        showToast('Please select a payment method', 'error');
        return;
    }
    const paymentMethod = paymentMethodEl.value;
    
    const orderData = {
        cart: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
        })),
        payment_method: paymentMethod
    };

    // Add guest data if guest checkout
    if (isGuest) {
        if (!guestFormShown) {
            showToast('Please choose how you want to order', 'error');
            return;
        }
        
        const guestName = document.getElementById('guest_name').value.trim();
        const guestPhone = document.getElementById('guest_phone').value.trim();
        const guestBlock = document.getElementById('guest_block').value.trim();
        const guestUnitNo = document.getElementById('guest_unit_no').value.trim();
        
        if (!guestName || !guestPhone || !guestBlock || !guestUnitNo) {
            showToast('Please fill in all your details', 'error');
            return;
        }
        
        orderData.guest_name = guestName;
        orderData.guest_phone = guestPhone;
        orderData.guest_block = guestBlock;
        orderData.guest_unit_no = guestUnitNo;
    }

    // Disable button
    const btn = document.getElementById('place-order-btn');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    try {
        const response = await fetch('{{ route("orders.place") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(orderData)
        });

        const data = await response.json();

        if (response.ok && data.success) {
            localStorage.removeItem('cart');
            showToast(data.message || 'Order placed successfully!', 'success');
            
            setTimeout(() => {
                window.location.href = data.redirect || '{{ route("menu") }}';
            }, 1000);
        } else {
            showToast(data.message || 'Failed to place order. Please try again.', 'error');
            btn.disabled = false;
            btn.textContent = '🧇 Place Order Now';
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        btn.disabled = false;
        btn.textContent = '🧇 Place Order Now';
    }
}

renderCheckout();
</script>
@endpush
@endsection
