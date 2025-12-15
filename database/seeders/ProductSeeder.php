<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed waffle products.
     */
    public function run(): void
    {
        // Get or create apartment
        $apartment = Apartment::first();
        if (!$apartment) {
            $this->command->error('❌ No apartment found. Run SetupSeeder first.');
            return;
        }

        // Get owner
        $owner = User::where('role', 'owner')->first();
        if (!$owner) {
            $this->command->error('❌ No owner found. Create owner account first.');
            return;
        }

        // Get or create Classic Waffles category
        $category = Category::where('slug', 'classic-waffles')->first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Classic Waffles',
                'slug' => 'classic-waffles',
                'icon' => '🧇',
                'is_active' => true,
            ]);
        }

        // Waffle products from pricelist
        $products = [
            [
                'name' => 'Choc Waffle',
                'description' => 'Waffle dengan topping coklat yang sedap',
                'price' => 5.00,
            ],
            [
                'name' => 'Peanut Waffle',
                'description' => 'Waffle dengan topping kacang rangup',
                'price' => 5.00,
            ],
            [
                'name' => 'Strawberry Waffle',
                'description' => 'Waffle dengan topping strawberry segar',
                'price' => 5.00,
            ],
            [
                'name' => 'Blueberry Waffle',
                'description' => 'Waffle dengan topping blueberry manis',
                'price' => 5.00,
            ],
            [
                'name' => 'Mix 2 Flavour Waffle',
                'description' => 'Pilih mana-mana 2 perisa kegemaran anda',
                'price' => 6.00,
            ],
            [
                'name' => 'Cheese Series Waffle',
                'description' => 'Waffle premium dengan keju berkrim',
                'price' => 8.00,
            ],
            [
                'name' => 'Kunafa Pistachio Waffle',
                'description' => 'Waffle istimewa dengan kunafa dan pistachio',
                'price' => 10.00,
            ],
            [
                'name' => 'Choc Pistachio Waffle',
                'description' => 'Gabungan coklat dan pistachio yang mewah',
                'price' => 8.00,
            ],
            [
                'name' => 'Chicken Floss Waffle',
                'description' => 'Waffle dengan serunding ayam rangup',
                'price' => 7.00,
            ],
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['name' => $productData['name'], 'seller_id' => $owner->id],
                [
                    'apartment_id' => $apartment->id,
                    'seller_id' => $owner->id,
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ ' . count($products) . ' waffle products seeded!');
    }
}
