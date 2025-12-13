@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <div class="flex items-center gap-3 mb-6">
        <span class="text-3xl">🛒</span>
        <h1 class="text-2xl font-bold text-gray-800">Your Order</h1>
    </div>

    <div id="cart-items" class="space-y-4 mb-6">
        <!-- Cart items will be loaded by JavaScript -->
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex justify-between items-center text-lg font-bold">
            <span>Total:</span>
            <span id="cart-total">RM 0.00</span>
        </div>
    </div>

    <button onclick="proceedToCheckout()" 
        class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg hover:from-amber-600 hover:to-orange-600 transition font-semibold">
        🧇 Proceed to Checkout
    </button>
</div>

@push('scripts')
<script>
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

function renderCart() {
    const container = document.getElementById('cart-items');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-6xl mb-4">🛒</div>
                <p class="text-gray-600 mb-4">Your cart is empty</p>
                <a href="{{ route('home') }}" class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-2 rounded-lg hover:from-amber-600 hover:to-orange-600">
                    Browse Waffles
                </a>
            </div>
        `;
        document.getElementById('cart-total').textContent = 'RM 0.00';
        return;
    }

    container.innerHTML = cart.map((item, index) => `
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold">${item.name}</h3>
                <button onclick="removeFromCart(${index})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <button onclick="updateQuantity(${index}, -1)" class="bg-gray-200 px-2 py-1 rounded">-</button>
                    <span class="font-semibold">${item.quantity}</span>
                    <button onclick="updateQuantity(${index}, 1)" class="bg-gray-200 px-2 py-1 rounded">+</button>
                </div>
                <span class="font-bold text-amber-600">RM ${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        </div>
    `).join('');

    updateTotal();
}

function updateQuantity(index, change) {
    cart[index].quantity += change;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge(); // Update cart badge
    renderCart();
}

function removeFromCart(index) {
    customConfirm('🗑️ Remove this item from cart?', function() {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge(); // Update cart badge
        renderCart();
        showToast('Item removed from cart', 'info');
    });
}

function updateTotal() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('cart-total').textContent = `RM ${total.toFixed(2)}`;
}

function proceedToCheckout() {
    if (cart.length === 0) {
        showToast('Your cart is empty', 'warning');
        return;
    }
    window.location.href = '{{ route("checkout") }}';
}

renderCart();
</script>
@endpush
@endsection

