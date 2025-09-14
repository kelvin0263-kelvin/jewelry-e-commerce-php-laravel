<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            // Link to the customer who started the chat   
            // migration 的 onDelete('cascade') 才决定是否级联删除。
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Link to the admin who joins the chat (can be null initially)
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
