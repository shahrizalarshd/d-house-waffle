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
        // Add QR code fields to users table (for sellers)
        Schema::table('users', function (Blueprint $table) {
            $table->string('qr_code_image')->nullable()->after('phone');
            $table->string('qr_code_type')->nullable()->after('qr_code_image');
            $table->text('qr_code_instructions')->nullable()->after('qr_code_type');
        });

        // Add payment proof fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('payment_ref');
            $table->text('payment_notes')->nullable()->after('payment_proof');
        });

        // Update payment_method enum to include 'qr'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('online', 'cash', 'qr') DEFAULT 'online'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['qr_code_image', 'qr_code_type', 'qr_code_instructions']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof', 'payment_notes']);
        });

        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('online', 'cash') DEFAULT 'online'");
    }
};

