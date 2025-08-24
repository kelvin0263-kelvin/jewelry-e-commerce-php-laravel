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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g., TKT-2024-001234
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Ticket details
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('category', [
                'general_inquiry',
                'product_issue',
                'order_problem',
                'billing_question',
                'account_help',
                'technical_support',
                'return_refund',
                'shipping_delivery',
                'other'
            ])->default('general_inquiry');
            
            // Status tracking
            $table->enum('status', [
                'open',
                'in_progress', 
                'waiting_customer',
                'waiting_agent',
                'resolved',
                'closed'
            ])->default('open');
            
            // Contact preferences
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('preferred_contact_method', ['email', 'phone', 'portal'])->default('email');
            
            // Timestamps and tracking
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->integer('response_time_hours')->nullable(); // SLA tracking
            $table->integer('resolution_time_hours')->nullable();
            
            // Customer feedback
            $table->tinyInteger('satisfaction_rating')->nullable(); // 1-5 stars
            $table->text('customer_feedback')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional data (browser, order info, etc.)
            $table->boolean('is_escalated')->default(false);
            $table->timestamp('escalated_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['assigned_agent_id', 'status']);
            $table->index(['category', 'priority']);
            $table->index(['status', 'created_at']);
            $table->index('ticket_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};