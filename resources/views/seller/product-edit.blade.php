@extends('layouts.app')

@section('title', 'Edit Waffle')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <a href="{{ route('owner.products') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 mb-4 font-semibold">
        <i class="fas fa-arrow-left"></i>Back to Menu
    </a>

    <div class="flex items-center gap-3 mb-6">
        <span class="text-4xl">🧇</span>
        <h1 class="text-2xl font-bold text-gray-800">Edit Waffle Item</h1>
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

        <form method="POST" action="{{ route('owner.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="category_id">
                    <i class="fas fa-tag mr-1 text-amber-600"></i> Category
                </label>
                <select name="category_id" id="category_id"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->icon }} {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="name">
                    <i class="fas fa-signature mr-1 text-amber-600"></i> Waffle Name
                </label>
                <input type="text" name="name" id="name" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                    value="{{ old('name', $product->name) }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="description">
                    Description
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="image">
                    Product Image (Optional)
                </label>
                @if($product->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                        class="w-32 h-32 object-cover rounded">
                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                </div>
                @endif
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Leave empty to keep current image.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="price">
                    Price (RM)
                </label>
                <input type="number" step="0.01" name="price" id="price" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                    value="{{ old('price', $product->price) }}">
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="mr-2">
                    <span class="text-gray-700">Active (visible to buyers)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                    class="flex-1 waffle-gradient text-white py-3 rounded-lg hover:shadow-lg font-semibold">
                    Update Item
                </button>
                <a href="{{ route('owner.products') }}" 
                    class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-400 text-center">
                    Cancel
                </a>
            </div>
        </form>

        <form method="POST" action="{{ route('owner.products.destroy', $product->id) }}" 
            class="mt-4" onsubmit="event.preventDefault(); customConfirm('🗑️ Are you sure you want to delete this product? This action cannot be undone.', () => this.submit());">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                <i class="fas fa-trash mr-2"></i>Delete Product
            </button>
        </form>
    </div>
</div>
@endsection

