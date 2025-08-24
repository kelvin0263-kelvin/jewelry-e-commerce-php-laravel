<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['waiting', 'assigned', 'completed', 'abandoned'])->default('waiting');
            $table->integer('position')->nullable(); // Position in queue (FIFO)
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('queued_at');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('wait_time_seconds')->nullable(); // Time waited before assignment
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->json('escalation_context')->nullable(); // Context from self-service
            $table->text('initial_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'position']);
            $table->index(['queued_at']);
            $table->index(['assigned_agent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_queue');
    }
};