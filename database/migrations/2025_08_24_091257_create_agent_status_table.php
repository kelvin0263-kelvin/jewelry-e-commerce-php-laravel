<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['online', 'busy', 'away', 'offline'])->default('offline');
            $table->integer('max_concurrent_chats')->default(3);
            $table->integer('current_active_chats')->default(0);
            $table->boolean('accepting_chats')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->text('status_message')->nullable(); // Custom status message
            $table->json('skills')->nullable(); // Agent skills/specializations
            $table->integer('total_chats_handled')->default(0);
            $table->decimal('average_response_time', 8, 2)->nullable(); // seconds
            $table->decimal('customer_satisfaction_rating', 3, 2)->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
            $table->index(['status', 'accepting_chats']);
            $table->index(['last_activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_status');
    }
};