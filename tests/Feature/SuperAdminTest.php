<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;
    protected User $superAdmin;
    protected User $owner;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apartment = Apartment::factory()->create();
        $this->superAdmin = User::factory()->superAdmin()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->owner = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);
        $this->customer = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super/dashboard');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_view_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super/settings');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_view_apartments(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super/apartments');

        $response->assertStatus(200);
        $response->assertSee($this->apartment->name);
    }

    public function test_super_admin_can_view_users(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/super/users');

        $response->assertStatus(200);
        $response->assertSee($this->owner->name);
        $response->assertSee($this->customer->name);
    }

    public function test_owner_cannot_access_super_admin_routes(): void
    {
        $this->actingAs($this->owner)->get('/super/dashboard')->assertStatus(403);
        $this->actingAs($this->owner)->get('/super/settings')->assertStatus(403);
        $this->actingAs($this->owner)->get('/super/apartments')->assertStatus(403);
        $this->actingAs($this->owner)->get('/super/users')->assertStatus(403);
    }

    public function test_customer_cannot_access_super_admin_routes(): void
    {
        $this->actingAs($this->customer)->get('/super/dashboard')->assertStatus(403);
        $this->actingAs($this->customer)->get('/super/settings')->assertStatus(403);
        $this->actingAs($this->customer)->get('/super/apartments')->assertStatus(403);
        $this->actingAs($this->customer)->get('/super/users')->assertStatus(403);
    }

    public function test_guest_cannot_access_super_admin_routes(): void
    {
        $this->get('/super/dashboard')->assertRedirect('/login');
        $this->get('/super/settings')->assertRedirect('/login');
        $this->get('/super/apartments')->assertRedirect('/login');
        $this->get('/super/users')->assertRedirect('/login');
    }
}
