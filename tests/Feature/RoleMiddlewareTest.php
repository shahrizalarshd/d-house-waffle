<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apartment = Apartment::factory()->create();
    }

    public function test_customer_can_access_buyer_routes(): void
    {
        $customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($customer)->get('/home')->assertStatus(200);
        $this->actingAs($customer)->get('/products')->assertStatus(200);
        $this->actingAs($customer)->get('/cart')->assertStatus(200);
        $this->actingAs($customer)->get('/orders')->assertStatus(200);
    }

    public function test_customer_cannot_access_staff_routes(): void
    {
        $customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($customer)->get('/staff/dashboard')->assertStatus(403);
        $this->actingAs($customer)->get('/staff/orders')->assertStatus(403);
    }

    public function test_customer_cannot_access_owner_routes(): void
    {
        $customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($customer)->get('/owner/dashboard')->assertStatus(403);
        $this->actingAs($customer)->get('/owner/products')->assertStatus(403);
        $this->actingAs($customer)->get('/owner/settings')->assertStatus(403);
    }

    public function test_staff_can_access_staff_routes(): void
    {
        $staff = User::factory()->staff()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($staff)->get('/staff/dashboard')->assertStatus(200);
        $this->actingAs($staff)->get('/staff/orders')->assertStatus(200);
    }

    public function test_staff_cannot_access_owner_routes(): void
    {
        $staff = User::factory()->staff()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($staff)->get('/owner/dashboard')->assertStatus(403);
        $this->actingAs($staff)->get('/owner/products')->assertStatus(403);
        $this->actingAs($staff)->get('/owner/settings')->assertStatus(403);
    }

    public function test_owner_can_access_owner_routes(): void
    {
        $owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($owner)->get('/owner/dashboard')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/products')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/orders')->assertStatus(200);
        $this->actingAs($owner)->get('/owner/settings')->assertStatus(200);
    }

    public function test_super_admin_can_access_super_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $this->actingAs($superAdmin)->get('/super/dashboard')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/super/settings')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/super/apartments')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/super/users')->assertStatus(200);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $this->get('/home')->assertRedirect('/login');
        $this->get('/staff/dashboard')->assertRedirect('/login');
        $this->get('/owner/dashboard')->assertRedirect('/login');
        $this->get('/super/dashboard')->assertRedirect('/login');
    }
}
