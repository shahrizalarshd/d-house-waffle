<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->string('title'); // For alt text and admin reference
            $table->string('subtitle')->nullable(); // Optional subtitle text
            $table->string('image_path'); // Stored image path
            $table->string('link_url')->nullable(); // Optional click-through URL
            $table->string('link_type')->default('none'); // none, product, category, external
            $table->integer('display_order')->default(0); // Order of display
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable(); // Optional start date
            $table->timestamp('expires_at')->nullable(); // Optional expiry date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
