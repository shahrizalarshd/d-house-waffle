<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $customer;
    protected User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apartment = Apartment::factory()->create();
        $this->customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
    }

    public function test_home_page_displays_active_products(): void
    {
        $category = Category::factory()->create();
        $activeProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        $inactiveProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->customer)->get('/home');

        $response->assertStatus(200);
        $response->assertSee($activeProduct->name);
        $response->assertDontSee($inactiveProduct->name);
    }

    public function test_home_page_can_search_products(): void
    {
        $category = Category::factory()->create();
        $chocolateProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'name' => 'Chocolate Waffle',
            'is_active' => true,
        ]);
        $strawberryProduct = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'name' => 'Strawberry Waffle',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->customer)->get('/home?search=chocolate');

        $response->assertStatus(200);
        $response->assertSee('Chocolate Waffle');
        $response->assertDontSee('Strawberry Waffle');
    }

    public function test_home_page_can_filter_by_category(): void
    {
        $category1 = Category::factory()->create(['name' => 'Classic']);
        $category2 = Category::factory()->create(['name' => 'Premium']);

        $product1 = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category1->id,
            'name' => 'Classic Waffle',
            'is_active' => true,
        ]);
        $product2 = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category2->id,
            'name' => 'Premium Waffle',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->customer)->get('/home?category=' . $category1->id);

        $response->assertStatus(200);
        $response->assertSee('Classic Waffle');
        $response->assertDontSee('Premium Waffle');
    }

    public function test_products_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->customer)->get('/products');

        $response->assertStatus(200);
    }

    public function test_cart_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->customer)->get('/cart');

        $response->assertStatus(200);
    }

    public function test_orders_page_displays_user_orders(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->customer)->get('/orders');

        $response->assertStatus(200);
        $response->assertSee($order->order_no);
    }

    public function test_orders_page_does_not_show_other_users_orders(): void
    {
        $otherCustomer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $otherOrder = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $otherCustomer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->customer)->get('/orders');

        $response->assertStatus(200);
        $response->assertDontSee($otherOrder->order_no);
    }

    public function test_order_detail_page_can_be_viewed(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);
        OrderItem::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($this->customer)->get('/orders/' . $order->id);

        $response->assertStatus(200);
        $response->assertSee($order->order_no);
    }

    public function test_cannot_view_other_users_order_detail(): void
    {
        $otherCustomer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $otherOrder = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $otherCustomer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->customer)->get('/orders/' . $otherOrder->id);

        $response->assertStatus(404);
    }

    public function test_profile_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->customer)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($this->customer->name);
    }

    public function test_profile_can_be_updated(): void
    {
        $response = $this->actingAs($this->customer)->put('/profile', [
            'name' => 'Updated Name',
            'email' => $this->customer->email,
            'phone' => '0987654321',
            'block' => 'B',
            'unit_no' => '15-10',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertEquals('Updated Name', $this->customer->name);
        $this->assertEquals('0987654321', $this->customer->phone);
    }

    public function test_profile_password_can_be_changed(): void
    {
        $response = $this->actingAs($this->customer)->put('/profile', [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_profile_password_change_requires_correct_current_password(): void
    {
        $response = $this->actingAs($this->customer)->put('/profile', [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_guest_cannot_access_buyer_pages(): void
    {
        $this->get('/home')->assertRedirect('/login');
        $this->get('/products')->assertRedirect('/login');
        $this->get('/cart')->assertRedirect('/login');
        $this->get('/orders')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }
}
