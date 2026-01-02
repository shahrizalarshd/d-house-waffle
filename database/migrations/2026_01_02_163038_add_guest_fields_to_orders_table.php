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
        Schema::table('orders', function (Blueprint $table) {
            // Make buyer_id nullable for guest orders
            $table->foreignId('buyer_id')->nullable()->change();
            
            // Guest information fields
            $table->string('guest_name')->nullable()->after('buyer_id');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->string('guest_block')->nullable()->after('guest_phone');
            $table->string('guest_unit_no')->nullable()->after('guest_block');
            $table->string('guest_token', 64)->nullable()->unique()->after('guest_unit_no');
            
            // Index for faster lookups
            $table->index('guest_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['guest_phone']);
            $table->dropColumn([
                'guest_name',
                'guest_phone',
                'guest_block',
                'guest_unit_no',
                'guest_token',
            ]);
        });
    }
};
