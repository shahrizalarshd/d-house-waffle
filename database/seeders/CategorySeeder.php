<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Classic Waffles',
                'slug' => 'classic-waffles',
                'icon' => '🧇',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Waffles',
                'slug' => 'premium-waffles',
                'icon' => '✨',
                'is_active' => true,
            ],
            [
                'name' => 'Special Toppings',
                'slug' => 'special-toppings',
                'icon' => '🍓',
                'is_active' => true,
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'icon' => '🥤',
                'is_active' => true,
            ],
            [
                'name' => 'Combo Sets',
                'slug' => 'combo-sets',
                'icon' => '🎁',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']], // Find by slug
                $category // Update or create with these values
            );
        }

        $this->command->info('✅ Categories seeded successfully!');
    }
}
