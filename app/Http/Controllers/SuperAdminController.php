<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Models\Apartment;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Super admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_apartments' => Apartment::count(),
            'total_users' => User::count(),
            'total_sellers' => User::where('role', 'seller')->count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('platform_fee'),
            'billplz_status' => PlatformSetting::isBillplzReady() ? 'active' : 'not_configured',
        ];

        return view('super.dashboard', compact('stats'));
    }

    /**
     * Platform settings page
     */
    public function settings()
    {
        $settings = PlatformSetting::all()->keyBy('key');
        
        return view('super.settings', compact('settings'));
    }

    /**
     * Update platform settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'billplz_enabled' => 'nullable|boolean',
            'billplz_api_key' => 'nullable|string|max:255',
            'billplz_collection_id' => 'nullable|string|max:255',
            'billplz_x_signature' => 'nullable|string|max:255',
            'billplz_sandbox_mode' => 'nullable|boolean',
            'toyyibpay_enabled' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            PlatformSetting::set($key, $value ?? '');
        }

        return back()->with('success', 'Platform settings updated successfully');
    }

    /**
     * Test Billplz connection
     */
    public function testBillplzConnection()
    {
        $settings = PlatformSetting::getBillplzSettings();
        
        if (empty($settings['api_key']) || empty($settings['collection_id'])) {
            return back()->with('error', 'Billplz API credentials not configured');
        }

        try {
            // Test API connection by fetching collection info
            $baseUrl = $settings['sandbox_mode'] 
                ? 'https://www.billplz-sandbox.com/api/v3'
                : 'https://www.billplz.com/api/v3';

            $response = \Illuminate\Support\Facades\Http::withBasicAuth($settings['api_key'], '')
                ->get("{$baseUrl}/collections/{$settings['collection_id']}");

            if ($response->successful()) {
                $collection = $response->json();
                return back()->with('success', "Billplz connection successful! Collection: {$collection['title']}");
            } else {
                return back()->with('error', 'Billplz connection failed: ' . $response->json()['error']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Billplz connection error: ' . $e->getMessage());
        }
    }

    /**
     * List all apartments
     */
    public function apartments()
    {
        $apartments = Apartment::withCount(['users', 'orders'])->paginate(15);
        
        return view('super.apartments', compact('apartments'));
    }

    /**
     * View all users (across apartments)
     */
    public function users()
    {
        $users = User::with('apartment')->latest()->paginate(20);
        
        return view('super.users', compact('users'));
    }
}

