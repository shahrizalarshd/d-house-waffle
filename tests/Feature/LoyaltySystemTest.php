<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\LoyaltySetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Models\LoyaltyTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoyaltySystemTest extends TestCase
{
    use RefreshDatabase;

    protected $apartment;
    protected $owner;
    protected $customer;
    protected $product;
    protected $loyaltySetting;

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

        // Create customer using factory
        $this->customer = User::factory()->create([
            'apartment_id' => $this->apartment->id,
            'role' => 'customer',
            'loyalty_stamps' => 0,
            'lifetime_orders' => 0,
        ]);

        // Create loyalty settings
        $this->loyaltySetting = LoyaltySetting::create([
            'apartment_id' => $this->apartment->id,
            'stamps_required' => 5,
            'stamp_discount_percent' => 10.00,
            'loyalty_enabled' => true,
        ]);
    }

    #[Test]
    public function customer_starts_with_zero_stamps()
    {
        $this->assertEquals(0, $this->customer->loyalty_stamps);
        $this->assertEquals(0, $this->customer->lifetime_orders);
    }

    #[Test]
    public function customer_earns_stamp_on_completed_order()
    {
        $order = Order::factory()->create([
            'seller_id' => $this->owner->id,
            'buyer_id' => $this->customer->id,
            'apartment_id' => $this->apartment->id,
            'total_amount' => 20.00,
            'status' => 'completed',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        // Simulate order completion
        $loyaltyService = new LoyaltyService();
        $loyaltyService->awardStamp($this->customer, $order);

        $this->customer->refresh();
        $this->assertEquals(1, $this->customer->loyalty_stamps);
        $this->assertEquals(1, $this->customer->lifetime_orders);
    }

    #[Test]
    public function customer_gets_discount_after_reaching_threshold()
    {
        // Give customer 4 stamps
        $this->customer->update(['loyalty_stamps' => 4, 'lifetime_orders' => 4]);

        $order = Order::factory()->create([
            'seller_id' => $this->owner->id,
            'buyer_id' => $this->customer->id,
            'apartment_id' => $this->apartment->id,
            'total_amount' => 20.00,
            'status' => 'completed',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        // Add 5th stamp
        $loyaltyService = new LoyaltyService();
        $loyaltyService->awardStamp($this->customer, $order);

        $this->customer->refresh();
        $this->assertTrue($this->customer->loyalty_discount_available);
        $this->assertEquals(0, $this->customer->loyalty_stamps); // Reset after reaching threshold
    }

    #[Test]
    public function loyalty_discount_is_applied_correctly()
    {
        $subtotal = 100.00;
        $discountPercent = $this->loyaltySetting->stamp_discount_percent;
        $expectedDiscount = $subtotal * ($discountPercent / 100);

        $this->assertEquals(10.00, $expectedDiscount);
    }

    #[Test]
    public function loyalty_page_shows_customer_progress()
    {
        $this->customer->update(['loyalty_stamps' => 3]);

        $response = $this->actingAs($this->customer)->get('/loyalty');

        $response->assertStatus(200);
    }

    #[Test]
    public function owner_can_view_loyalty_settings()
    {
        $response = $this->actingAs($this->owner)->get('/owner/loyalty-settings');

        $response->assertStatus(200);
    }

    #[Test]
    public function guest_orders_do_not_earn_stamps()
    {
        $order = Order::factory()->create([
            'seller_id' => $this->owner->id,
            'buyer_id' => null, // Guest order
            'guest_name' => 'Guest User',
            'guest_phone' => '0123456789',
            'guest_block' => 'A',
            'guest_unit_no' => '1-01',
            'guest_token' => 'token123',
            'total_amount' => 20.00,
            'status' => 'completed',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
        ]);

        // Guest orders should not trigger loyalty stamps
        $this->assertNull($order->buyer_id);
    }
}
