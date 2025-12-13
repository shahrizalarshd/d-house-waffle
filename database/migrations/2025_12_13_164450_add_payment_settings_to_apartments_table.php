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
        Schema::table('apartments', function (Blueprint $table) {
            $table->boolean('payment_online_enabled')->default(true)->after('pickup_end_time');
            $table->boolean('payment_qr_enabled')->default(true)->after('payment_online_enabled');
            $table->boolean('payment_cash_enabled')->default(true)->after('payment_qr_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apartments', function (Blueprint $table) {
            $table->dropColumn(['payment_online_enabled', 'payment_qr_enabled', 'payment_cash_enabled']);
        });
    }
};
