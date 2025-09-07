<?php
// app/Modules/Support/Routes/api.php

use Illuminate\Support\Facades\Route;
use App\Modules\Support\Controllers\Api\TicketController;
use App\Modules\Support\Controllers\ChatController;
use App\Modules\Support\Controllers\ChatQueueController;
use App\Modules\Support\Controllers\AdminTicketController;

// User support routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // Ticket management for users
    Route::prefix('support/tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);           // GET /api/support/tickets - user's tickets
        Route::post('/', [TicketController::class, 'store']);          // POST /api/support/tickets - create ticket
        Route::get('/{ticket}', [TicketController::class, 'show']);    // GET /api/support/tickets/{id}
        Route::put('/{ticket}', [TicketController::class, 'update']);  // PUT /api/support/tickets/{id}
        Route::post('/{ticket}/reply', [TicketController::class, 'reply']); // POST /api/support/tickets/{id}/reply
        Route::post('/{ticket}/close', [TicketController::class, 'close']); // POST /api/support/tickets/{id}/close
    });
    
    // Chat functionality for users
    Route::prefix('support/chat')->group(function () {
        // Start chat (queue)
        Route::post('/start', [ChatController::class, 'startChat']);
        // Queue status
        Route::get('/queue/{conversationId}', [ChatController::class, 'getQueueStatus']);
        // Leave queue
        Route::post('/{conversationId}/leave', [ChatController::class, 'leaveQueue']);
        // Terminate conversation
        Route::post('/{conversationId}/terminate', [ChatController::class, 'terminateConversation']);
        
        Route::get('/conversations', [ChatController::class, 'userConversations']); // GET /api/support/chat/conversations
        Route::post('/conversations', [ChatController::class, 'createConversation']); // POST /api/support/chat/conversations
        Route::get('/conversations/{conversation}', [ChatController::class, 'show']); // GET /api/support/chat/conversations/{id}
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']); // GET /api/support/chat/conversations/{id}/messages
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']); // POST /api/support/chat/conversations/{id}/messages
        // Alternative message endpoint without conversation in path
        Route::post('/messages', [ChatController::class, 'sendMessage']); // POST /api/support/chat/messages
    });
    
    // FAQ and self-service
    Route::prefix('support')->group(function () {
        Route::get('/faq', [TicketController::class, 'faq']); // GET /api/support/faq
        Route::get('/categories', [TicketController::class, 'categories']); // GET /api/support/categories
    });
    
});

// Admin support routes - require admin authentication
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // Admin ticket management
    Route::prefix('admin/support/tickets')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index']);          // GET /api/admin/support/tickets - all tickets
        Route::get('/{ticket}', [AdminTicketController::class, 'show']);   // GET /api/admin/support/tickets/{id}
        Route::put('/{ticket}', [AdminTicketController::class, 'update']); // PUT /api/admin/support/tickets/{id}
        Route::post('/{ticket}/assign', [AdminTicketController::class, 'assign']); // POST /api/admin/support/tickets/{id}/assign
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply']); // POST /api/admin/support/tickets/{id}/reply
        Route::post('/{ticket}/close', [AdminTicketController::class, 'close']); // POST /api/admin/support/tickets/{id}/close
        Route::get('/{ticket}/history', [AdminTicketController::class, 'history']); // GET /api/admin/support/tickets/{id}/history
    });
    
    // Admin chat management
    Route::prefix('admin/support/chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'adminConversations']); // GET /api/admin/support/chat/conversations
        Route::get('/conversations/{conversation}', [ChatController::class, 'show']); // GET /api/admin/support/chat/conversations/{id}
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']); // GET /api/admin/support/chat/conversations/{id}/messages
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']); // POST /api/admin/support/chat/conversations/{id}/messages
        Route::post('/conversations/{conversation}/transfer', [ChatController::class, 'transfer']); // POST /api/admin/support/chat/conversations/{id}/transfer
    });
    
    // Chat queue management
    Route::prefix('admin/support/queue')->group(function () {
        Route::get('/', [ChatQueueController::class, 'index']);         // GET /api/admin/support/queue
        Route::post('/take', [ChatQueueController::class, 'takeNext']); // POST /api/admin/support/queue/take
        Route::post('/{queue}/assign', [ChatQueueController::class, 'assign']); // POST /api/admin/support/queue/{id}/assign
    });
    
});