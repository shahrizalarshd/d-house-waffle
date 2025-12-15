<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: Change role column from ENUM to VARCHAR to allow all role values
     */
    public function up(): void
    {
        // Change ENUM to VARCHAR(50) to accept any role value
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to ENUM if needed (optional)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'owner', 'staff', 'customer') DEFAULT 'customer'");
    }
};
