<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft','pending','pending_review','approved','published','issued') DEFAULT 'draft'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('draft','pending_review','approved','published','issued') DEFAULT 'draft'");
    }
};
