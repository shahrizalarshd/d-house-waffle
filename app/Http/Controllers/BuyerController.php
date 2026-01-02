<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Apartment;
use App\Models\Category;
use App\Models\LoyaltySetting;
use App\Models\Banner;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerController extends Controller
{
    /**
     * Public menu - accessible without login (for Guest Checkout)
     */
    public function publicMenu(Request $request)
    {
        // Get default apartment (for single-tenant setup)
        $apartment = Apartment::first();
        
        if (!$apartment) {
            return view('buyer.menu-public', [
                'products' => collect(),
                'categories' => collect(),
                'loyaltySettings' => null,
                'banners' => collect(),
            ]);
        }
        
        $query = Product::with(['seller', 'category'])
            ->where('apartment_id', $apartment->id)
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
        $categories = Category::active()->orderBy('name')->get();
        $loyaltySettings = LoyaltySetting::getForApartment($apartment->id);
        
        // Get active banners for carousel
        $banners = Banner::where('apartment_id', $apartment->id)
            ->active()
            ->ordered()
            ->get();

        return view('buyer.menu-public', compact('products', 'categories', 'loyaltySettings', 'apartment', 'banners'));
    }

    /**
     * Home page for logged in users
     */
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
        $categories = Category::active()->orderBy('name')->get();
        
        // Get loyalty info for logged in user
        $loyaltyService = app(LoyaltyService::class);
        $loyaltySummary = $loyaltyService->getLoyaltySummary(auth()->user());

        return view('buyer.home', compact('products', 'categories', 'loyaltySummary'));
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
        $categories = Category::active()->orderBy('name')->get();

        return view('buyer.products', compact('products', 'categories'));
    }

    public function cart()
    {
        // Get apartment for guest or logged in user
        if (auth()->check()) {
            $apartment = auth()->user()->apartment;
        } else {
            $apartment = Apartment::first();
        }
        
        $loyaltySettings = $apartment ? LoyaltySetting::getForApartment($apartment->id) : null;
        
        return view('buyer.cart', compact('loyaltySettings'));
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
        
        // Get loyalty summary
        $loyaltyService = app(LoyaltyService::class);
        $loyaltySummary = $loyaltyService->getLoyaltySummary(auth()->user());
        $recentTransactions = $loyaltyService->getRecentTransactions(auth()->user(), 5);
        
        return view('buyer.profile', compact('loyaltySummary', 'recentTransactions'));
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

    /**
     * Loyalty page - show full loyalty details
     */
    public function loyalty()
    {
        $loyaltyService = app(LoyaltyService::class);
        $loyaltySummary = $loyaltyService->getLoyaltySummary(auth()->user());
        $recentTransactions = $loyaltyService->getRecentTransactions(auth()->user(), 20);
        $settings = $loyaltyService->getSettings(auth()->user()->apartment_id);
        
        return view('buyer.loyalty', compact('loyaltySummary', 'recentTransactions', 'settings'));
    }
}
