<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display banner management page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get the apartment (assuming single apartment for now)
        $apartment = Apartment::first();
        
        if (!$apartment) {
            return redirect()->back()->with('error', 'No apartment configured.');
        }

        $banners = Banner::where('apartment_id', $apartment->id)
            ->ordered()
            ->get();

        return view('owner.banners', compact('banners', 'apartment'));
    }

    /**
     * Store a new banner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:255',
            'link_type' => 'required|in:none,product,category,external',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $apartment = Apartment::first();
        
        // Check max 3 banners
        $currentCount = Banner::where('apartment_id', $apartment->id)->count();
        if ($currentCount >= 3) {
            return redirect()->back()->with('error', 'Maximum 3 banners allowed. Please delete one first.');
        }

        // Handle image upload
        $imagePath = $request->file('image')->store('banners', 'public');

        // Get next display order
        $maxOrder = Banner::where('apartment_id', $apartment->id)->max('display_order') ?? 0;

        Banner::create([
            'apartment_id' => $apartment->id,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'link_type' => $request->link_type,
            'display_order' => $maxOrder + 1,
            'is_active' => true,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->back()->with('success', 'Banner added successfully!');
    }

    /**
     * Update an existing banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link_url' => 'nullable|string|max:255',
            'link_type' => 'required|in:none,product,category,external',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        $data = [
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'link_url' => $request->link_url,
            'link_type' => $request->link_type,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
        ];

        // Handle image upload if new image provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->back()->with('success', 'Banner updated successfully!');
    }

    /**
     * Toggle banner active status.
     */
    public function toggle(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        $status = $banner->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Banner {$status} successfully!");
    }

    /**
     * Delete a banner.
     */
    public function destroy(Banner $banner)
    {
        // Delete image file
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Banner deleted successfully!');
    }

    /**
     * Reorder banners.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:banners,id',
        ]);

        foreach ($request->order as $index => $bannerId) {
            Banner::where('id', $bannerId)->update(['display_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get active banners for public display (API).
     */
    public function getActiveBanners()
    {
        $apartment = Apartment::first();
        
        if (!$apartment) {
            return response()->json([]);
        }

        $banners = Banner::where('apartment_id', $apartment->id)
            ->active()
            ->ordered()
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'image_url' => $banner->image_url,
                    'link_url' => $banner->link_url,
                    'link_type' => $banner->link_type,
                ];
            });

        return response()->json($banners);
    }
}

