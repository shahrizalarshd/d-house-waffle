<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected $apartment;
    protected $owner;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create apartment using factory
        $this->apartment = Apartment::factory()->create();

        // Create owner using factory
        $this->owner = User::factory()->create([
            'apartment_id' => $this->apartment->id,
            'role' => 'owner',
        ]);

        // Create category
        $category = Category::factory()->create();

        // Create product using factory
        $this->product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'name' => 'Test Waffle',
            'price' => 10.00,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function guest_can_view_menu_without_login()
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('Test Waffle');
    }

    #[Test]
    public function guest_can_view_cart_without_login()
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
    }

    #[Test]
    public function guest_can_view_checkout_without_login()
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
    }

    #[Test]
    public function guest_can_track_order_with_token()
    {
        // Create a guest order
        $order = Order::factory()->create([
            'seller_id' => $this->owner->id,
            'buyer_id' => null,
            'guest_name' => 'Jane Doe',
            'guest_phone' => '0198765432',
            'guest_block' => 'C',
            'guest_unit_no' => '3-05',
            'guest_token' => 'test-token-12345',
            'total_amount' => 20.00,
            'status' => 'pending',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        $response = $this->get('/track/test-token-12345');

        $response->assertStatus(200);
        $response->assertSee('Jane Doe');
    }

    #[Test]
    public function invalid_tracking_token_returns_404()
    {
        $response = $this->get('/track/invalid-token');

        $response->assertStatus(404);
    }
}
