<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    /**
     * Setup essential data for the application.
     * Safe to run multiple times - uses updateOrCreate.
     */
    public function run(): void
    {
        // 1. Create or get default apartment
        $apartment = Apartment::first();
        if (!$apartment) {
            $apartment = Apartment::create([
                'name' => "D'house Waffle - Sri Harmonis",
                'address' => 'Apartment Sri Harmonis, Gombak, Selangor',
                'service_fee_percent' => 0.00,
                'pickup_location' => 'Lobby Utama (Ground Floor)',
                'pickup_start_time' => '09:00:00',
                'pickup_end_time' => '21:00:00',
                'status' => 'active',
            ]);
            $this->command->info('✅ Apartment created: ' . $apartment->name);
        } else {
            $this->command->info('ℹ️  Apartment exists: ' . $apartment->name);
        }

        // 2. Fix all users without apartment_id
        $usersFixed = User::whereNull('apartment_id')->update(['apartment_id' => $apartment->id]);
        if ($usersFixed > 0) {
            $this->command->info("✅ Fixed {$usersFixed} users without apartment_id");
        }

        // 3. Create categories
        $categories = [
            ['name' => 'Classic Waffles', 'slug' => 'classic-waffles', 'icon' => '🧇', 'is_active' => true],
            ['name' => 'Premium Waffles', 'slug' => 'premium-waffles', 'icon' => '✨', 'is_active' => true],
            ['name' => 'Special Toppings', 'slug' => 'special-toppings', 'icon' => '🍓', 'is_active' => true],
            ['name' => 'Beverages', 'slug' => 'beverages', 'icon' => '🥤', 'is_active' => true],
            ['name' => 'Combo Sets', 'slug' => 'combo-sets', 'icon' => '🎁', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
        $this->command->info('✅ Categories seeded: ' . count($categories));

        // 4. Ensure at least one owner exists
        $owner = User::where('role', 'owner')->first();
        if (!$owner) {
            $this->command->warn('⚠️  No owner found. Create one via register or tinker.');
        } else {
            $this->command->info('ℹ️  Owner exists: ' . $owner->email);
        }

        $this->command->info('');
        $this->command->info('🎉 Setup complete!');
    }
}
