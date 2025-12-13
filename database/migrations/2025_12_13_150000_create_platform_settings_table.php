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
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->boolean('is_sensitive')->default(false); // Hide value in UI
            $table->timestamps();
        });

        // Insert default Billplz settings
        DB::table('platform_settings')->insert([
            [
                'key' => 'billplz_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable Billplz payment gateway',
                'is_sensitive' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billplz_api_key',
                'value' => '',
                'type' => 'string',
                'description' => 'Billplz API Secret Key',
                'is_sensitive' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billplz_collection_id',
                'value' => '',
                'type' => 'string',
                'description' => 'Billplz Collection ID',
                'is_sensitive' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billplz_x_signature',
                'value' => '',
                'type' => 'string',
                'description' => 'Billplz X Signature Key (for webhook verification)',
                'is_sensitive' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'billplz_sandbox_mode',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Use Billplz sandbox/testing mode',
                'is_sensitive' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'toyyibpay_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable ToyyibPay payment gateway',
                'is_sensitive' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};

