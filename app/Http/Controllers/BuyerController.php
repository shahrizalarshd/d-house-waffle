<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerController extends Controller
{
    public function home(Request $request)
    {
        $query = Product::with(['seller', 'category'])
            ->where('apartment_id', auth()->user()->apartment_id)
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $products = $query->latest()->get();
        $categories = \App\Models\Category::active()->orderBy('name')->get();

        return view('buyer.home', compact('products', 'categories'));
    }

    public function products(Request $request)
    {
        $query = Product::with(['seller', 'category'])
            ->where('apartment_id', auth()->user()->apartment_id)
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $products = $query->latest()->paginate(12);
        $categories = \App\Models\Category::active()->orderBy('name')->get();

        return view('buyer.products', compact('products', 'categories'));
    }

    public function cart()
    {
        return view('buyer.cart');
    }

    public function orders()
    {
        $orders = Order::with(['seller', 'items.product'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('buyer.orders', compact('orders'));
    }

    public function orderDetail($id)
    {
        $order = Order::with(['seller', 'items.product', 'payment'])
            ->where('buyer_id', auth()->id())
            ->findOrFail($id);

        return view('buyer.order-detail', compact('order'));
    }

    public function profile()
    {
        // Load orders relationship to avoid N+1 queries
        auth()->user()->load('orders');
        return view('buyer.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'block' => 'nullable|string|max:10',
            'unit_no' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // If user wants to change password
        if ($request->filled('password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
            unset($validated['current_password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}
