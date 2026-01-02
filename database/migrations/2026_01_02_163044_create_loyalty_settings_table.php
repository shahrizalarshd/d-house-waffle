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
        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            
            // Guest checkout settings
            $table->boolean('guest_checkout_enabled')->default(true);
            $table->unsignedInteger('guest_pending_limit')->default(3);
            
            // Stamp card settings
            $table->boolean('loyalty_enabled')->default(true);
            $table->unsignedInteger('stamps_required')->default(5);
            $table->decimal('stamp_discount_percent', 5, 2)->default(10.00);
            $table->unsignedInteger('discount_validity_days')->default(30);
            
            // Tier settings (optional)
            $table->boolean('tiers_enabled')->default(false);
            $table->unsignedInteger('silver_threshold')->default(10);
            $table->unsignedInteger('gold_threshold')->default(25);
            $table->decimal('silver_bonus_percent', 5, 2)->default(2.00);
            $table->decimal('gold_bonus_percent', 5, 2)->default(5.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_settings');
    }
};
