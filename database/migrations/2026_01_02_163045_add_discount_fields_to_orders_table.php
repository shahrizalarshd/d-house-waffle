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
            // Track discounts applied to orders
            $table->decimal('subtotal', 10, 2)->default(0)->after('seller_amount');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            $table->string('discount_type')->nullable()->after('discount_amount'); // loyalty, tier, promo
            $table->text('discount_details')->nullable()->after('discount_type'); // JSON for breakdown
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_amount',
                'discount_type',
                'discount_details',
            ]);
        });
    }
};
