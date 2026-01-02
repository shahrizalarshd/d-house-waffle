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
        Schema::table('users', function (Blueprint $table) {
            // Loyalty stamp tracking
            $table->unsignedInteger('loyalty_stamps')->default(0)->after('status');
            $table->unsignedInteger('lifetime_orders')->default(0)->after('loyalty_stamps');
            $table->decimal('lifetime_spent', 10, 2)->default(0)->after('lifetime_orders');
            
            // Tier system
            $table->string('loyalty_tier')->default('bronze')->after('lifetime_spent');
            
            // Discount availability
            $table->boolean('loyalty_discount_available')->default(false)->after('loyalty_tier');
            $table->timestamp('loyalty_discount_expires_at')->nullable()->after('loyalty_discount_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'loyalty_stamps',
                'lifetime_orders',
                'lifetime_spent',
                'loyalty_tier',
                'loyalty_discount_available',
                'loyalty_discount_expires_at',
            ]);
        });
    }
};
