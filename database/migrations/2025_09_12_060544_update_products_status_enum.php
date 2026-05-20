<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft','pending','published','rejected','issued') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft','pending_review','approved','published') DEFAULT 'draft'");
    }
};
