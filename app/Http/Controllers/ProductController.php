<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create()
    {
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        return view('seller.product-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'apartment_id' => auth()->user()->apartment_id,
            'seller_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $imagePath,
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('seller.products')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::where('seller_id', auth()->id())->findOrFail($id);
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        return view('seller.product-edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('seller_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('seller.products')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::where('seller_id', auth()->id())->findOrFail($id);
        
        // Delete image if exists
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }

    public function toggleStatus($id)
    {
        $product = Product::where('seller_id', auth()->id())->findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return back()->with('success', 'Product status updated');
    }
}
