<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create waffle categories
        $categories = [
            ['name' => 'Classic Waffles', 'slug' => 'classic-waffles', 'icon' => '🧇', 'is_active' => true],
            ['name' => 'Premium Waffles', 'slug' => 'premium-waffles', 'icon' => '✨', 'is_active' => true],
            ['name' => 'Special Toppings', 'slug' => 'special-toppings', 'icon' => '🍓', 'is_active' => true],
            ['name' => 'Beverages', 'slug' => 'beverages', 'icon' => '🥤', 'is_active' => true],
            ['name' => 'Combo Sets', 'slug' => 'combo-sets', 'icon' => '🎁', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }

        // Create default apartment
        $apartment = Apartment::create([
            'name' => "D'house Waffle - Sri Harmonis",
            'address' => 'Apartment Sri Harmonis, Gombak, Selangor',
            'service_fee_percent' => 0.00, // No service fee for direct sales
            'pickup_location' => 'Lobby Utama (Ground Floor)',
            'pickup_start_time' => '09:00:00',
            'pickup_end_time' => '21:00:00',
            'status' => 'active',
        ]);

        // Create super admin (system owner)
        User::create([
            'apartment_id' => $apartment->id,
            'name' => 'System Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'phone' => '0123456789',
            'role' => 'super_admin',
            'unit_no' => 'ADMIN',
            'block' => 'ADMIN',
            'status' => 'active',
        ]);

        // Create D'house Waffle owner account
        $owner = User::create([
            'apartment_id' => $apartment->id,
            'name' => "Ahmad (D'house Waffle Owner)",
            'email' => 'owner@dhouse.com',
            'password' => Hash::make('password'),
            'phone' => '0123456787',
            'role' => 'owner',
            'unit_no' => 'G-01',
            'block' => 'Ground',
            'status' => 'active',
        ]);

        // Create D'house Waffle staff account
        $staff = User::create([
            'apartment_id' => $apartment->id,
            'name' => 'Sarah (Staff)',
            'email' => 'staff@dhouse.com',
            'password' => Hash::make('password'),
            'phone' => '0123456786',
            'role' => 'staff',
            'unit_no' => 'G-01',
            'block' => 'Ground',
            'status' => 'active',
        ]);

        // Create sample customers
        User::create([
            'apartment_id' => $apartment->id,
            'name' => 'Siti Abdullah',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'phone' => '0123456785',
            'role' => 'customer',
            'unit_no' => '03-10',
            'block' => 'C',
            'status' => 'active',
        ]);

        User::create([
            'apartment_id' => $apartment->id,
            'name' => 'Ali Rahman',
            'email' => 'customer2@test.com',
            'password' => Hash::make('password'),
            'phone' => '0123456784',
            'role' => 'customer',
            'unit_no' => '05-08',
            'block' => 'B',
            'status' => 'active',
        ]);

        // Create waffle products
        $classicCategory = \App\Models\Category::where('slug', 'classic-waffles')->first();
        $premiumCategory = \App\Models\Category::where('slug', 'premium-waffles')->first();
        $toppingsCategory = \App\Models\Category::where('slug', 'special-toppings')->first();
        $beveragesCategory = \App\Models\Category::where('slug', 'beverages')->first();
        $comboCategory = \App\Models\Category::where('slug', 'combo-sets')->first();

        // Classic Waffles (created by owner)
        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $classicCategory->id,
            'name' => 'Original Belgian Waffle',
            'description' => 'Light and crispy Belgian waffle with maple syrup and butter',
            'price' => 8.00,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $classicCategory->id,
            'name' => 'Chocolate Waffle',
            'description' => 'Belgian waffle topped with chocolate sauce and chocolate chips',
            'price' => 9.50,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $classicCategory->id,
            'name' => 'Strawberry Waffle',
            'description' => 'Fresh strawberries with whipped cream on golden waffle',
            'price' => 10.00,
            'is_active' => true,
        ]);

        // Premium Waffles
        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $premiumCategory->id,
            'name' => 'Nutella Banana Waffle',
            'description' => 'Nutella spread with fresh banana slices and crushed nuts',
            'price' => 12.50,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $premiumCategory->id,
            'name' => 'Mixed Berries Supreme',
            'description' => 'Strawberries, blueberries, raspberries with vanilla ice cream',
            'price' => 14.00,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $premiumCategory->id,
            'name' => 'Oreo Cheesecake Waffle',
            'description' => 'Cream cheese, crushed Oreos, and chocolate drizzle',
            'price' => 13.50,
            'is_active' => true,
        ]);

        // Beverages
        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $beveragesCategory->id,
            'name' => 'Iced Chocolate',
            'description' => 'Rich chocolate blended with ice and topped with whipped cream',
            'price' => 6.00,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $beveragesCategory->id,
            'name' => 'Fresh Orange Juice',
            'description' => 'Freshly squeezed orange juice',
            'price' => 5.00,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $beveragesCategory->id,
            'name' => 'Mineral Water',
            'description' => 'Refreshing mineral water 500ml',
            'price' => 2.00,
            'is_active' => true,
        ]);

        // Combo Sets
        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $comboCategory->id,
            'name' => 'Classic Combo',
            'description' => 'Any classic waffle + iced chocolate',
            'price' => 13.00,
            'is_active' => true,
        ]);

        \App\Models\Product::create([
            'apartment_id' => $apartment->id,
            'seller_id' => $owner->id,
            'category_id' => $comboCategory->id,
            'name' => 'Premium Combo',
            'description' => 'Any premium waffle + fresh orange juice',
            'price' => 17.00,
            'is_active' => true,
        ]);
    }
}
