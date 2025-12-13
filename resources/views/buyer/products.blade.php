@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">All Products</h1>

    <!-- Search and Filter -->
    <form method="GET" action="{{ route('products') }}" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Search products..." 
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <select name="category" 
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-3 rounded-lg hover:from-amber-600 hover:to-orange-600">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request('search') || request('category'))
                <a href="{{ route('products') }}" class="bg-gray-200 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </div>
    </form>

    @if($products->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-600">No products available yet</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-4xl"></i>
            </div>
            @endif
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                    @if($product->category)
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                        {{ $product->category->icon }} {{ $product->category->name }}
                    </span>
                    @endif
                </div>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xl font-bold text-amber-600">RM {{ number_format($product->price, 2) }}</span>
                    <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                        class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded hover:from-amber-600 hover:to-orange-600 transition">
                        Add to Cart
                    </button>
                </div>
                <p class="text-xs text-gray-500">Sold by: {{ $product->seller->name }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
let cart = JSON.parse(localStorage.getItem('cart') || '[]');

function addToCart(productId, productName, price) {
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            name: productName,
            price: price,
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge(); // Update cart badge
    showToast('🧇 Added to cart!', 'success');
}
</script>
@endpush
@endsection

