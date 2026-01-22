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
    // public function up(): void
    // {
    //     // Update the status enum to include new values
    //     DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'rejected', 'issued') DEFAULT 'draft'");
    // }

    // /**
    //  * Reverse the migrations.
    //  */
    // public function down(): void
    // {
    //     // Revert to original enum values
    //     DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft', 'pending_review', 'approved', 'published') DEFAULT 'draft'");
    // }

      public function up(): void
    {
        // 1) status 变成 varchar，并设置默认值
        DB::statement("ALTER TABLE products ALTER COLUMN status TYPE varchar(255)");
        DB::statement("ALTER TABLE products ALTER COLUMN status SET DEFAULT 'draft'");

        // 2) 先删旧约束，再加新约束（等价 enum）
        DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_status_check");
        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT products_status_check
            CHECK (status IN ('draft', 'pending', 'published', 'rejected', 'issued'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products ALTER COLUMN status TYPE varchar(255)");
        DB::statement("ALTER TABLE products ALTER COLUMN status SET DEFAULT 'draft'");

        DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_status_check");
        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT products_status_check
            CHECK (status IN ('draft', 'pending_review', 'approved', 'published'))
        ");
    }
};