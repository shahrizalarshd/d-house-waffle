<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->products->contains($product));
    }

    public function test_scope_active_returns_only_active_categories(): void
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $activeCategories = Category::active()->get();

        $this->assertTrue($activeCategories->contains($activeCategory));
        $this->assertFalse($activeCategories->contains($inactiveCategory));
    }

    public function test_is_active_is_cast_to_boolean(): void
    {
        $category = Category::factory()->create(['is_active' => 1]);

        $this->assertTrue($category->is_active);
        $this->assertIsBool($category->is_active);
    }
}
