<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Expand enum to include both old and new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('buyer', 'seller', 'apartment_admin', 'super_admin', 'customer', 'staff', 'owner') DEFAULT 'buyer'");
        
        // Step 2: Update existing roles to new structure
        DB::table('users')->where('role', 'buyer')->update(['role' => 'customer']);
        DB::table('users')->where('role', 'seller')->update(['role' => 'owner']);
        DB::table('users')->where('role', 'apartment_admin')->delete(); // Remove apartment_admin
        
        // Step 3: Now modify the enum to only new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'staff', 'owner', 'super_admin') DEFAULT 'customer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('buyer', 'seller', 'apartment_admin', 'super_admin') DEFAULT 'buyer'");
        
        DB::table('users')->where('role', 'customer')->update(['role' => 'buyer']);
        DB::table('users')->where('role', 'owner')->update(['role' => 'seller']);
        DB::table('users')->where('role', 'staff')->update(['role' => 'seller']);
    }
};
