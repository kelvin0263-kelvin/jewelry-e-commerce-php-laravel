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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Which conversation does this message belong to?
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            // Who sent the message? (Could be a customer or an admin)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // The actual message text
            $table->text('body');
            // To track if the message has been read (optional but useful)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
