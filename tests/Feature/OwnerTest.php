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

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $owner;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apartment = Apartment::factory()->create();
        $this->owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
    }

    public function test_owner_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/dashboard');

        $response->assertStatus(200);
    }

    public function test_owner_dashboard_shows_statistics(): void
    {
        // Create some orders
        $order = Order::factory()->paid()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'seller_amount' => 50.00,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/dashboard');

        $response->assertStatus(200);
        $response->assertSee('50'); // Total earnings
    }

    public function test_owner_can_view_orders(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/orders');

        $response->assertStatus(200);
        $response->assertSee($order->order_no);
    }

    public function test_owner_can_update_order_status(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/orders/' . $order->id . '/status', [
            'status' => 'preparing',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('preparing', $order->status);
    }

    public function test_owner_can_mark_cash_order_as_paid(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/orders/' . $order->id . '/mark-paid');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->status);
    }

    public function test_owner_cannot_mark_online_order_as_paid_manually(): void
    {
        $order = Order::factory()->onlinePayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/orders/' . $order->id . '/mark-paid');

        $response->assertSessionHas('error');
    }

    public function test_owner_can_verify_qr_payment(): void
    {
        $order = Order::factory()->qrPayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/test.png',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/orders/' . $order->id . '/verify-qr', [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->status);
    }

    public function test_owner_can_reject_qr_payment(): void
    {
        $order = Order::factory()->qrPayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/test.png',
        ]);

        $response = $this->actingAs($this->owner)->post('/owner/orders/' . $order->id . '/verify-qr', [
            'action' => 'reject',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('failed', $order->payment_status);
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_owner_can_view_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'apartment_id' => $this->apartment->id,
            'seller_id' => $this->owner->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/products');

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_owner_can_access_settings(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/settings');

        $response->assertStatus(200);
    }

    public function test_owner_can_view_sales_report(): void
    {
        $response = $this->actingAs($this->owner)->get('/owner/sales-report');

        $response->assertStatus(200);
    }

    public function test_owner_can_filter_sales_report_by_date(): void
    {
        Order::factory()->paid()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->owner->id,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->owner)->get('/owner/sales-report', [
            'date_from' => now()->subDays(7)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
    }

    public function test_owner_can_update_profile_with_qr_code(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('qr-code.png');

        $response = $this->actingAs($this->owner)->put('/owner/profile', [
            'qr_code_image' => $file,
            'qr_code_type' => 'DuitNow',
            'qr_code_instructions' => 'Scan this QR to pay',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->owner->refresh();
        $this->assertNotNull($this->owner->qr_code_image);
        $this->assertEquals('DuitNow', $this->owner->qr_code_type);
    }

    public function test_customer_cannot_access_owner_routes(): void
    {
        $this->actingAs($this->customer)->get('/owner/dashboard')->assertStatus(403);
        $this->actingAs($this->customer)->get('/owner/orders')->assertStatus(403);
        $this->actingAs($this->customer)->get('/owner/products')->assertStatus(403);
        $this->actingAs($this->customer)->get('/owner/settings')->assertStatus(403);
    }
}
