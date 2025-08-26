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
        Schema::table('messages', function (Blueprint $table) {
            // Allow user_id to be nullable for system messages
            $table->foreignId('user_id')->nullable()->change();
            
            // Add message type field
            $table->enum('message_type', ['user', 'system'])->default('user')->after('body');
            
            // Add index for message type
            $table->index(['conversation_id', 'message_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Remove the index
            $table->dropIndex(['conversation_id', 'message_type']);
            
            // Remove the message_type column
            $table->dropColumn('message_type');
            
            // Restore user_id as not nullable (this might fail if there are system messages)
            // $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
