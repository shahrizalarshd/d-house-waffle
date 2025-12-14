<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $staff;
    protected User $owner;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apartment = Apartment::factory()->create();
        $this->staff = User::factory()->staff()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
    }

    public function test_staff_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->staff)->get('/staff/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_can_view_orders(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->staff->id,
        ]);

        $response = $this->actingAs($this->staff)->get('/staff/orders');

        $response->assertStatus(200);
    }

    public function test_staff_can_update_order_status(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->staff->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->staff)->post('/staff/orders/' . $order->id . '/status', [
            'status' => 'preparing',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('preparing', $order->status);
    }

    public function test_staff_can_mark_cash_order_as_paid(): void
    {
        $order = Order::factory()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->staff->id,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->staff)->post('/staff/orders/' . $order->id . '/mark-paid');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }

    public function test_staff_can_verify_qr_payment(): void
    {
        $order = Order::factory()->qrPayment()->create([
            'apartment_id' => $this->apartment->id,
            'buyer_id' => $this->customer->id,
            'seller_id' => $this->staff->id,
            'payment_status' => 'pending',
            'payment_proof' => 'proofs/test.png',
        ]);

        $response = $this->actingAs($this->staff)->post('/staff/orders/' . $order->id . '/verify-qr', [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
    }

    public function test_staff_cannot_access_owner_only_routes(): void
    {
        $this->actingAs($this->staff)->get('/owner/products')->assertStatus(403);
        $this->actingAs($this->staff)->get('/owner/settings')->assertStatus(403);
        $this->actingAs($this->staff)->get('/owner/sales-report')->assertStatus(403);
    }

    public function test_customer_cannot_access_staff_routes(): void
    {
        $this->actingAs($this->customer)->get('/staff/dashboard')->assertStatus(403);
        $this->actingAs($this->customer)->get('/staff/orders')->assertStatus(403);
    }
}
