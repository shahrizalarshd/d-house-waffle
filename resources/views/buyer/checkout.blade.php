@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">🛒</span>
        <h1 class="text-2xl font-bold text-gray-800">Checkout</h1>
    </div>

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

    <button onclick="placeOrder()" 
        class="w-full waffle-gradient text-white py-4 rounded-lg hover:shadow-lg transition font-bold text-lg">
        🧇 Place Order Now
    </button>
</div>

@push('scripts')
<script>
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

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
    const platformFee = subtotal * 0.05;
    const total = subtotal + platformFee;

    document.getElementById('subtotal').textContent = `RM ${subtotal.toFixed(2)}`;
    document.getElementById('platform-fee').textContent = `RM ${platformFee.toFixed(2)}`;
    document.getElementById('total').textContent = `RM ${total.toFixed(2)}`;
    document.getElementById('cash-total').textContent = `RM ${total.toFixed(2)}`;
    document.getElementById('qr-total').textContent = `RM ${total.toFixed(2)}`;
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
    
    // Initialize first available option (find checked radio and highlight its container)
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
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    const orderData = {
        cart: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity
        })),
        payment_method: paymentMethod
    };

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

        if (response.ok) {
            localStorage.removeItem('cart');
            const data = await response.json();
            window.location.href = data.redirect || '{{ route("buyer.orders") }}';
        } else {
            showToast('Failed to place order. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    }
}

renderCheckout();
</script>
@endpush
@endsection

