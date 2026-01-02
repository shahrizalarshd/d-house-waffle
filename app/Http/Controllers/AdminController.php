<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Apartment;
use App\Models\LoyaltySetting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function orders()
    {
        $orders = Order::with(['buyer', 'seller', 'items'])
            ->where('apartment_id', auth()->user()->apartment_id)
            ->latest()
            ->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    public function settings()
    {
        $apartment = Apartment::find(auth()->user()->apartment_id);
        return view('admin.settings', compact('apartment'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'service_fee_percent' => 'required|numeric|min:0|max:100',
            'pickup_location' => 'required|string|max:255',
            'pickup_start_time' => 'required|date_format:H:i',
            'pickup_end_time' => 'required|date_format:H:i',
            'payment_online_enabled' => 'nullable|boolean',
            'payment_qr_enabled' => 'nullable|boolean',
            'payment_cash_enabled' => 'nullable|boolean',
        ]);

        // Ensure at least one payment method is enabled
        if (!$request->has('payment_online_enabled') && 
            !$request->has('payment_qr_enabled') && 
            !$request->has('payment_cash_enabled')) {
            return back()->withErrors(['payment' => 'At least one payment method must be enabled.'])->withInput();
        }

        // Convert checkbox values (if not checked, they won't be in request)
        $validated['payment_online_enabled'] = $request->has('payment_online_enabled');
        $validated['payment_qr_enabled'] = $request->has('payment_qr_enabled');
        $validated['payment_cash_enabled'] = $request->has('payment_cash_enabled');

        $apartment = Apartment::find(auth()->user()->apartment_id);
        $apartment->update($validated);

        return back()->with('success', '✅ Settings updated successfully');
    }

    /**
     * Show loyalty settings page
     */
    public function loyaltySettings()
    {
        $apartment = Apartment::find(auth()->user()->apartment_id);
        $loyaltySettings = LoyaltySetting::getForApartment($apartment->id);
        
        return view('owner.loyalty-settings', compact('apartment', 'loyaltySettings'));
    }

    /**
     * Update loyalty settings
     */
    public function updateLoyaltySettings(Request $request)
    {
        $validated = $request->validate([
            'guest_checkout_enabled' => 'nullable|boolean',
            'guest_pending_limit' => 'required|integer|min:1|max:10',
            'loyalty_enabled' => 'nullable|boolean',
            'stamps_required' => 'required|integer|min:2|max:20',
            'stamp_discount_percent' => 'required|numeric|min:1|max:50',
            'discount_validity_days' => 'required|integer|min:7|max:90',
            'tiers_enabled' => 'nullable|boolean',
            'silver_threshold' => 'required|integer|min:5|max:100',
            'gold_threshold' => 'required|integer|min:10|max:200',
            'silver_bonus_percent' => 'required|numeric|min:0|max:20',
            'gold_bonus_percent' => 'required|numeric|min:0|max:30',
        ]);

        // Convert checkbox values
        $validated['guest_checkout_enabled'] = $request->has('guest_checkout_enabled');
        $validated['loyalty_enabled'] = $request->has('loyalty_enabled');
        $validated['tiers_enabled'] = $request->has('tiers_enabled');

        // Validate gold threshold is greater than silver
        if ($validated['gold_threshold'] <= $validated['silver_threshold']) {
            return back()->withErrors([
                'gold_threshold' => 'Gold threshold must be greater than Silver threshold.'
            ])->withInput();
        }

        $apartment = Apartment::find(auth()->user()->apartment_id);
        $loyaltySettings = LoyaltySetting::getForApartment($apartment->id);
        $loyaltySettings->update($validated);

        return back()->with('success', '✅ Loyalty settings updated successfully');
    }
}
