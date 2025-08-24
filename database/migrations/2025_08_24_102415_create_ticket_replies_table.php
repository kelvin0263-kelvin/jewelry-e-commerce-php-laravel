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
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who sent the reply
            
            $table->text('message');
            $table->enum('reply_type', ['customer', 'agent', 'system'])->default('customer');
            
            // File attachments
            $table->json('attachments')->nullable(); // Array of file paths/URLs
            
            // Internal notes (only visible to agents)
            $table->boolean('is_internal')->default(false);
            
            // Tracking
            $table->boolean('is_first_response')->default(false); // Mark first agent response for SLA
            $table->timestamp('read_at')->nullable(); // When customer/agent read the reply
            
            $table->timestamps();
            
            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['user_id', 'reply_type']);
            $table->index(['is_internal', 'reply_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
    }
};