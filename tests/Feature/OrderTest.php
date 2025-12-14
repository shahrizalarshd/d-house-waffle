<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $customer;
    protected User $owner;
    protected Product $product;

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

        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
            'price' => 10.00,
            'is_active' => true,
        ]);
    }

    public function test_checkout_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->customer)->get('/checkout');

        $response->assertStatus(200);
    }

    public function test_order_can_be_placed_with_cash_payment(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);
    }

    public function test_order_can_be_placed_with_online_payment(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
            'payment_method' => 'online',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->customer->id,
            'payment_method' => 'online',
        ]);

        $this->assertDatabaseHas('payments', [
            'gateway' => 'billplz',
            'status' => 'pending',
        ]);
    }

    public function test_order_can_be_placed_with_qr_payment_when_seller_has_qr(): void
    {
        // Setup owner with QR code
        $this->owner->update(['qr_code_image' => 'qr-codes/test.png']);

        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
            'payment_method' => 'qr',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $this->customer->id,
            'payment_method' => 'qr',
        ]);
    }

    public function test_order_cannot_be_placed_with_qr_payment_when_seller_has_no_qr(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
            'payment_method' => 'qr',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_order_calculates_platform_fee_correctly(): void
    {
        // Set 5% service fee
        $this->apartment->update(['service_fee_percent' => 5.00]);

        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2, // 2 x RM10 = RM20
                ],
            ],
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(200);

        $order = Order::where('buyer_id', $this->customer->id)->first();
        $this->assertEquals('20.00', $order->total_amount);
        $this->assertEquals('1.00', $order->platform_fee); // 5% of 20
        $this->assertEquals('19.00', $order->seller_amount); // 20 - 1
    }

    public function test_order_creates_order_items(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                ],
            ],
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(200);

        $order = Order::where('buyer_id', $this->customer->id)->first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 3,
        ]);
    }

    public function test_payment_page_can_be_viewed_for_own_order(): void
    {
        $order = Order::factory()->onlinePayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->customer)->get('/payment/' . $order->id);

        $response->assertStatus(200);
    }

    public function test_qr_payment_page_can_be_viewed(): void
    {
        $this->owner->update(['qr_code_image' => 'qr-codes/test.png']);

        $order = Order::factory()->qrPayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->customer)->get('/orders/' . $order->id . '/qr-payment');

        $response->assertStatus(200);
    }

    public function test_payment_proof_can_be_uploaded(): void
    {
        Storage::fake('public');

        $order = Order::factory()->qrPayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);

        $file = UploadedFile::fake()->image('payment-proof.jpg');

        $response = $this->actingAs($this->customer)->post('/orders/' . $order->id . '/upload-proof', [
            'payment_proof' => $file,
            'payment_notes' => 'Paid via DuitNow',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertNotNull($order->payment_proof);
        $this->assertEquals('Paid via DuitNow', $order->payment_notes);
    }

    public function test_payment_proof_cannot_be_uploaded_for_non_qr_orders(): void
    {
        Storage::fake('public');

        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_method' => 'cash',
        ]);

        $file = UploadedFile::fake()->image('payment-proof.jpg');

        $response = $this->actingAs($this->customer)->post('/orders/' . $order->id . '/upload-proof', [
            'payment_proof' => $file,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_order_requires_valid_products(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => 9999, // Non-existent
                    'quantity' => 1,
                ],
            ],
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(422);
    }

    public function test_order_requires_valid_payment_method(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/orders/place', [
            'cart' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ],
            ],
            'payment_method' => 'invalid-method',
        ]);

        $response->assertStatus(422);
    }
}
