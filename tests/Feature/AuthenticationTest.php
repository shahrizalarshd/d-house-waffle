<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected Apartment $apartment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apartment = Apartment::factory()->create();
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_register_page_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_users_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/home');
    }

    public function test_users_cannot_login_with_incorrect_password(): void
    {
        $user = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_customer_is_redirected_to_home_after_login(): void
    {
        $user = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/home');
    }

    public function test_staff_is_redirected_to_staff_dashboard_after_login(): void
    {
        $user = User::factory()->staff()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/staff/dashboard');
    }

    public function test_owner_is_redirected_to_owner_dashboard_after_login(): void
    {
        $user = User::factory()->owner()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/owner/dashboard');
    }

    public function test_super_admin_is_redirected_to_super_dashboard_after_login(): void
    {
        $user = User::factory()->superAdmin()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/super/dashboard');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0123456789',
            'unit_no' => '10-05',
            'block' => 'A',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/home');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0123456789',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'phone' => '0123456789',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'phone' => '0123456789',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_authenticated_users_are_redirected_from_welcome_page(): void
    {
        $user = User::factory()->customer()->create([
            'apartment_id' => $this->apartment->id,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/home');
    }

    public function test_guests_can_see_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
