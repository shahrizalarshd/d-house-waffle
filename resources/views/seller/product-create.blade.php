@extends('layouts.app')

@section('title', 'Add Waffle')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6">
    <a href="{{ route('owner.products') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 mb-4 font-semibold">
        <i class="fas fa-arrow-left"></i>Back to Menu
    </a>

    <div class="flex items-center gap-3 mb-6">
        <span class="text-4xl">🧇</span>
        <h1 class="text-2xl font-bold text-gray-800">Add New Waffle Item</h1>
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

        <form method="POST" action="{{ route('owner.products.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="category_id">
                    <i class="fas fa-tag mr-1 text-amber-600"></i> Category
                </label>
                <select name="category_id" id="category_id"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    placeholder="e.g., Chocolate Banana Waffle"
                    value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="description">
                    <i class="fas fa-align-left mr-1 text-amber-600"></i> Description
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition"
                    placeholder="Describe your delicious waffle...">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="image">
                    <i class="fas fa-camera mr-1 text-amber-600"></i> Waffle Image (Optional)
                </label>
                <input type="file" name="image" id="image" accept="image/*"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition">
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Max size: 2MB. Formats: JPG, PNG, GIF</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="price">
                    <i class="fas fa-tag mr-1 text-amber-600"></i> Price (RM)
                </label>
                <input type="number" step="0.01" name="price" id="price" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-amber-500 transition text-lg font-semibold"
                    placeholder="0.00"
                    value="{{ old('price') }}">
            </div>

            <div class="mb-6 bg-amber-50 border border-amber-200 p-4 rounded-lg">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="mr-3 w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                    <span class="text-gray-700 font-semibold">✓ Make this item visible to customers</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" 
                    class="flex-1 waffle-gradient text-white py-4 rounded-lg hover:shadow-xl transition font-bold text-lg">
                    <i class="fas fa-plus-circle mr-2"></i>Add to Menu
                </button>
                <a href="{{ route('owner.products') }}" 
                    class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-400 text-center font-semibold flex items-center justify-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

