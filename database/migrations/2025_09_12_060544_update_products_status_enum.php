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
        // Update the status enum to include new values
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'rejected', 'issued') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft', 'pending_review', 'approved', 'published') DEFAULT 'draft'");
    }
};