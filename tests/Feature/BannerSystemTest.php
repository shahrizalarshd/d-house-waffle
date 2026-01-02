<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BannerSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $apartment;
    protected $owner;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');

        // Create apartment using factory
        $this->apartment = Apartment::factory()->create();

        // Create owner using factory
        $this->owner = User::factory()->create([
            'apartment_id' => $this->apartment->id,
            'role' => 'owner',
        ]);
    }

    #[Test]
    public function owner_can_view_banner_management_page()
    {
        $response = $this->actingAs($this->owner)->get('/owner/banners');

        $response->assertStatus(200);
        $response->assertSee('Banner');
    }

    #[Test]
    public function owner_can_create_banner()
    {
        $image = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($this->owner)->post('/owner/banners', [
            'title' => 'Test Banner',
            'subtitle' => 'Test Subtitle',
            'image' => $image,
            'link_type' => 'none',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('banners', [
            'title' => 'Test Banner',
            'subtitle' => 'Test Subtitle',
            'apartment_id' => $this->apartment->id,
            'is_active' => true,
        ]);
    }

    #[Test]
    public function banner_requires_title_and_image()
    {
        $response = $this->actingAs($this->owner)->post('/owner/banners', [
            'link_type' => 'none',
        ]);

        $response->assertSessionHasErrors(['title', 'image']);
    }

    #[Test]
    public function maximum_three_banners_allowed()
    {
        // Create 3 banners
        for ($i = 1; $i <= 3; $i++) {
            Banner::create([
                'apartment_id' => $this->apartment->id,
                'title' => "Banner $i",
                'image_path' => "banners/test$i.jpg",
                'link_type' => 'none',
                'display_order' => $i,
                'is_active' => true,
            ]);
        }

        $image = UploadedFile::fake()->image('banner4.jpg', 1200, 400);

        $response = $this->actingAs($this->owner)->post('/owner/banners', [
            'title' => 'Banner 4',
            'image' => $image,
            'link_type' => 'none',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function owner_can_toggle_banner_status()
    {
        $banner = Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Test Banner',
            'image_path' => 'banners/test.jpg',
            'link_type' => 'none',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->post("/owner/banners/{$banner->id}/toggle");

        $response->assertRedirect();
        
        $banner->refresh();
        $this->assertFalse($banner->is_active);
    }

    #[Test]
    public function owner_can_delete_banner()
    {
        $banner = Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Test Banner',
            'image_path' => 'banners/test.jpg',
            'link_type' => 'none',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->delete("/owner/banners/{$banner->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
    }

    #[Test]
    public function active_banners_show_on_menu_page()
    {
        Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Active Banner',
            'image_path' => 'banners/active.jpg',
            'link_type' => 'none',
            'is_active' => true,
        ]);

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('Active Banner');
    }

    #[Test]
    public function inactive_banners_do_not_show_on_menu()
    {
        Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Inactive Banner',
            'image_path' => 'banners/inactive.jpg',
            'link_type' => 'none',
            'is_active' => false,
        ]);

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertDontSee('Inactive Banner');
    }

    #[Test]
    public function scheduled_banners_do_not_show_before_start_date()
    {
        Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Future Banner',
            'image_path' => 'banners/future.jpg',
            'link_type' => 'none',
            'is_active' => true,
            'starts_at' => now()->addDays(7),
        ]);

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertDontSee('Future Banner');
    }

    #[Test]
    public function expired_banners_do_not_show()
    {
        Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'Expired Banner',
            'image_path' => 'banners/expired.jpg',
            'link_type' => 'none',
            'is_active' => true,
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertDontSee('Expired Banner');
    }

    #[Test]
    public function banner_api_returns_active_banners()
    {
        Banner::create([
            'apartment_id' => $this->apartment->id,
            'title' => 'API Banner',
            'image_path' => 'banners/api.jpg',
            'link_type' => 'external',
            'link_url' => 'https://example.com',
            'is_active' => true,
        ]);

        $response = $this->get('/api/banners');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'API Banner',
            'link_type' => 'external',
        ]);
    }
}
