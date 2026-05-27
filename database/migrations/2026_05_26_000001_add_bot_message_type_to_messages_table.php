<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('messages', 'message_type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('user', 'system', 'bot') NOT NULL DEFAULT 'user'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('messages', 'message_type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('user', 'system') NOT NULL DEFAULT 'user'");
        }
    }
};
