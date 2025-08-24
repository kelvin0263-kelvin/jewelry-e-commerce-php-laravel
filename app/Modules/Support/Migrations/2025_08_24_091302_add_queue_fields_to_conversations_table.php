<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'completed', 'abandoned'])->default('pending')->after('id');
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null')->after('user_id');
            $table->timestamp('started_at')->nullable()->after('assigned_agent_id');
            $table->timestamp('ended_at')->nullable()->after('started_at');
            $table->integer('queue_wait_time')->nullable()->after('ended_at'); // seconds
            $table->enum('end_reason', ['resolved', 'transferred', 'abandoned', 'timeout'])->nullable()->after('queue_wait_time');
            $table->decimal('customer_rating', 3, 2)->nullable()->after('end_reason');
            $table->text('customer_feedback')->nullable()->after('customer_rating');
            
            $table->index(['status']);
            $table->index(['assigned_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['assigned_agent_id']);
            $table->dropColumn([
                'status',
                'assigned_agent_id', 
                'started_at',
                'ended_at',
                'queue_wait_time',
                'end_reason',
                'customer_rating',
                'customer_feedback'
            ]);
        });
    }
};