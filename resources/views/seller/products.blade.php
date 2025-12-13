@extends('layouts.app')

@section('title', 'Waffle Menu')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-3">
            <span class="text-3xl">🧇</span>
            <h1 class="text-2xl font-bold text-gray-800">Waffle Menu</h1>
        </div>
        <a href="{{ route('owner.products.create') }}" 
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:from-amber-600 hover:to-orange-600">
            <i class="fas fa-plus mr-2"></i>Add New Item
        </a>
    </div>

    @if($products->isEmpty())
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <div class="text-6xl mb-4">🧇</div>
        <p class="text-gray-600 mb-4">No waffle items in your menu yet</p>
        <a href="{{ route('owner.products.create') }}" 
            class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-2 rounded-lg hover:from-amber-600 hover:to-orange-600">
            Create Your First Item
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                class="w-full h-48 object-cover">
            @else
            <div class="w-full h-48 bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                <div class="text-6xl">🧇</div>
            </div>
            @endif
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                    <span class="px-2 py-1 rounded text-xs font-semibold
                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                
                <p class="text-xl font-bold text-blue-600 mb-4">RM {{ number_format($product->price, 2) }}</p>
                
                <div class="flex gap-2">
                    <a href="{{ route('owner.products.edit', $product->id) }}" 
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-center py-2 rounded hover:from-amber-600 hover:to-orange-600 text-sm">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('owner.products.toggle', $product->id) }}" class="flex-1">
                        @csrf
                        <button type="submit" 
                            class="w-full {{ $product->is_active ? 'bg-gray-600' : 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' }} text-white py-2 rounded hover:opacity-80 text-sm font-semibold">
                            {{ $product->is_active ? 'Hide' : 'Show' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection

