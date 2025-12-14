<?php

namespace Tests\Unit\Models;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_belongs_to_apartment(): void
    {
        $apartment = Apartment::factory()->create();
        $user = User::factory()->create(['apartment_id' => $apartment->id]);

        $this->assertInstanceOf(Apartment::class, $user->apartment);
        $this->assertEquals($apartment->id, $user->apartment->id);
    }

    public function test_user_has_many_products_as_seller(): void
    {
        $seller = User::factory()->owner()->create();
        $product = Product::factory()->create([
            'seller_id' => $seller->id,
            'apartment_id' => $seller->apartment_id,
        ]);

        $this->assertTrue($seller->products->contains($product));
    }

    public function test_user_has_many_orders_as_buyer(): void
    {
        $buyer = User::factory()->customer()->create();
        $seller = User::factory()->owner()->create(['apartment_id' => $buyer->apartment_id]);

        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'apartment_id' => $buyer->apartment_id,
        ]);

        $this->assertTrue($buyer->buyerOrders->contains($order));
        $this->assertTrue($buyer->orders->contains($order));
    }

    public function test_user_has_many_orders_as_seller(): void
    {
        $buyer = User::factory()->customer()->create();
        $seller = User::factory()->owner()->create(['apartment_id' => $buyer->apartment_id]);

        $order = Order::factory()->create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'apartment_id' => $buyer->apartment_id,
        ]);

        $this->assertTrue($seller->sellerOrders->contains($order));
    }

    public function test_is_customer_returns_true_for_customer_role(): void
    {
        $user = User::factory()->customer()->create();

        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isStaff());
        $this->assertFalse($user->isOwner());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_is_staff_returns_true_for_staff_role(): void
    {
        $user = User::factory()->staff()->create();

        $this->assertFalse($user->isCustomer());
        $this->assertTrue($user->isStaff());
        $this->assertFalse($user->isOwner());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_is_owner_returns_true_for_owner_role(): void
    {
        $user = User::factory()->owner()->create();

        $this->assertFalse($user->isCustomer());
        $this->assertFalse($user->isStaff());
        $this->assertTrue($user->isOwner());
        $this->assertFalse($user->isSuperAdmin());
    }

    public function test_is_super_admin_returns_true_for_super_admin_role(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->assertFalse($user->isCustomer());
        $this->assertFalse($user->isStaff());
        $this->assertFalse($user->isOwner());
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_can_manage_business_returns_true_for_staff_and_owner(): void
    {
        $customer = User::factory()->customer()->create();
        $staff = User::factory()->staff()->create();
        $owner = User::factory()->owner()->create();

        $this->assertFalse($customer->canManageBusiness());
        $this->assertTrue($staff->canManageBusiness());
        $this->assertTrue($owner->canManageBusiness());
    }

    public function test_can_access_business_settings_returns_true_only_for_owner(): void
    {
        $customer = User::factory()->customer()->create();
        $staff = User::factory()->staff()->create();
        $owner = User::factory()->owner()->create();

        $this->assertFalse($customer->canAccessBusinessSettings());
        $this->assertFalse($staff->canAccessBusinessSettings());
        $this->assertTrue($owner->canAccessBusinessSettings());
    }

    public function test_has_qr_code_returns_correct_value(): void
    {
        $userWithoutQR = User::factory()->create(['qr_code_image' => null]);
        $userWithQR = User::factory()->create(['qr_code_image' => 'qr-codes/test.png']);

        $this->assertFalse($userWithoutQR->hasQRCode());
        $this->assertTrue($userWithQR->hasQRCode());
    }

    public function test_get_qr_code_url_returns_correct_url(): void
    {
        $userWithoutQR = User::factory()->create(['qr_code_image' => null]);
        $userWithQR = User::factory()->create(['qr_code_image' => 'qr-codes/test.png']);

        $this->assertNull($userWithoutQR->getQRCodeUrl());
        $this->assertStringContainsString('qr-codes/test.png', $userWithQR->getQRCodeUrl());
    }
}
