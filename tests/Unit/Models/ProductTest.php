<?php

namespace Tests\Unit\Models;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_apartment(): void
    {
        $apartment = Apartment::factory()->create();
        $product = Product::factory()->create(['apartment_id' => $apartment->id]);

        $this->assertInstanceOf(Apartment::class, $product->apartment);
        $this->assertEquals($apartment->id, $product->apartment->id);
    }

    public function test_product_belongs_to_seller(): void
    {
        $seller = User::factory()->owner()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'apartment_id' => $seller->apartment_id,
        ]);

        $this->assertInstanceOf(User::class, $product->seller);
        $this->assertEquals($seller->id, $product->seller->id);
    }

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_scope_active_returns_only_active_products(): void
    {
        $activeProduct = Product::factory()->create(['is_active' => true]);
        $inactiveProduct = Product::factory()->create(['is_active' => false]);

        $activeProducts = Product::active()->get();

        $this->assertTrue($activeProducts->contains($activeProduct));
        $this->assertFalse($activeProducts->contains($inactiveProduct));
    }

    public function test_scope_search_filters_by_name_or_description(): void
    {
        $product1 = Product::factory()->create([
            'name' => 'Chocolate Waffle',
            'description' => 'Delicious chocolate',
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Strawberry Cake',
            'description' => 'Fresh strawberry',
        ]);

        $results = Product::search('chocolate')->get();

        $this->assertTrue($results->contains($product1));
        $this->assertFalse($results->contains($product2));
    }

    public function test_scope_by_category_filters_by_category_id(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        $results = Product::byCategory($category1->id)->get();

        $this->assertTrue($results->contains($product1));
        $this->assertFalse($results->contains($product2));
    }

    public function test_price_is_cast_to_decimal(): void
    {
        $product = Product::factory()->create(['price' => 10.50]);

        $this->assertEquals('10.50', $product->price);
    }

    public function test_is_active_is_cast_to_boolean(): void
    {
        $product = Product::factory()->create(['is_active' => 1]);

        $this->assertTrue($product->is_active);
        $this->assertIsBool($product->is_active);
    }
}
