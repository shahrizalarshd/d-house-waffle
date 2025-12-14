<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $owner;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apartment = Apartment::factory()->create();
        $this->owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->category = Category::factory()->create();
    }

    public function test_owner_can_view_product_list(): void
    {
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/products');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_owner_can_view_create_product_form(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/products/create');

        $response->assertStatus(200);
    }

    public function test_owner_can_create_product(): void
    {
        $response = $this->actingAs($this->owner)->post('/owner/products', [
            'name' => 'New Waffle',
            'description' => 'Delicious new waffle',
            'price' => 15.00,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('owner.products'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'New Waffle',
            'seller_id' => $this->owner->id,
            'apartment_id' => $this->apartment->id,
            'price' => 15.00,
        ]);
    }

    public function test_owner_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('waffle.jpg');

        $response = $this->actingAs($this->owner)->post('/owner/products', [
            'name' => 'Waffle with Image',
            'description' => 'Beautiful waffle',
            'price' => 12.00,
            'category_id' => $this->category->id,
            'image' => $file,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('owner.products'));

        $product = Product::where('name', 'Waffle with Image')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_owner_can_view_edit_product_form(): void
    {
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/products/' . $product->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_owner_can_update_product(): void
    {
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->owner)->put('/owner/products/' . $product->id, [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'price' => 20.00,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('owner.products'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Updated Name', $product->name);
        $this->assertEquals('20.00', $product->price);
    }

    public function test_owner_can_update_product_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);

        $file = UploadedFile::fake()->image('new-image.jpg');

        $response = $this->actingAs($this->owner)->put('/owner/products/' . $product->id, [
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category_id' => $this->category->id,
            'image' => $file,
        ]);

        $response->assertRedirect(route('owner.products'));

        $product->refresh();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_owner_can_delete_product(): void
    {
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->owner)->delete('/owner/products/' . $product->id);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_owner_can_toggle_product_status(): void
    {
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/products/' . $product->id . '/toggle');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    public function test_owner_cannot_edit_other_sellers_products(): void
    {
        $otherOwner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $otherProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $otherOwner->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/products/' . $otherProduct->id . '/edit');

        $response->assertStatus(404);
    }

    public function test_owner_cannot_delete_other_sellers_products(): void
    {
        $otherOwner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $otherProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $otherOwner->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->owner)->delete('/owner/products/' . $otherProduct->id);

        $response->assertStatus(404);
    }

    public function test_product_requires_name(): void
    {
        $response = $this->actingAs($this->owner)->post('/owner/products', [
            'description' => 'No name product',
            'price' => 10.00,
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_requires_price(): void
    {
        $response = $this->actingAs($this->owner)->post('/owner/products', [
            'name' => 'No price product',
            'description' => 'Description',
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_product_price_must_be_positive(): void
    {
        $response = $this->actingAs($this->owner)->post('/owner/products', [
            'name' => 'Negative price product',
            'description' => 'Description',
            'price' => -10.00,
            'category_id' => $this->category->id,
        ]);

        $response->assertSessionHasErrors('price');
    }
}
