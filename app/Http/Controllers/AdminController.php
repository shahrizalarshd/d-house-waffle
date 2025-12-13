<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Apartment;
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
}
