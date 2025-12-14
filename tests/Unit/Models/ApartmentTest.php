<?php

namespace Tests\Unit\Models;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_apartment_has_many_users(): void
    {
        $apartment = Apartment::factory()->create();
        $user = User::factory()->create(['apartment_id' => $apartment->id]);

        $this->assertTrue($apartment->users->contains($user));
    }

    public function test_apartment_has_many_products(): void
    {
        $apartment = Apartment::factory()->create();
        $seller = User::factory()->owner()->create(['apartment_id' => $apartment->id]);
        $product = Product::factory()->create([
            'apartment_id' => $apartment->id,
            'seller_id' => $seller->id,
        ]);

        $this->assertTrue($apartment->products->contains($product));
    }

    public function test_apartment_has_many_orders(): void
    {
        $apartment = Apartment::factory()->create();
        $order = Order::factory()->create(['apartment_id' => $apartment->id]);

        $this->assertTrue($apartment->orders->contains($order));
    }

    public function test_service_fee_percent_is_cast_to_decimal(): void
    {
        $apartment = Apartment::factory()->create(['service_fee_percent' => 5.50]);

        $this->assertEquals('5.50', $apartment->service_fee_percent);
    }

    public function test_payment_settings_are_cast_to_boolean(): void
    {
        $apartment = Apartment::factory()->create([
            'payment_online_enabled' => 1,
            'payment_qr_enabled' => 0,
            'payment_cash_enabled' => 1,
        ]);

        $this->assertTrue($apartment->payment_online_enabled);
        $this->assertFalse($apartment->payment_qr_enabled);
        $this->assertTrue($apartment->payment_cash_enabled);
    }
}
