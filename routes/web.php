<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Api\CustomerController as ApiCustomerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductController as AdminProductController; // 引入后台控制器并使用别名
// 在 routes/web.php 的 admin 路由组内
use App\Http\Controllers\Api\ChatController; // 在文件顶部添加这个 use 语句
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// In routes/web.php
use App\Models\Conversation;

// Chat routes - now uses queue system
Route::post('/chat/start', [App\Http\Controllers\Api\ChatController::class, 'startChat'])->name('chat.start')->middleware('auth');
Route::get('/chat/queue-status/{conversationId}', [App\Http\Controllers\Api\ChatController::class, 'getQueueStatus'])->name('chat.queue-status')->middleware('auth');
Route::post('/chat/leave-queue/{conversationId}', [App\Http\Controllers\Api\ChatController::class, 'leaveQueue'])->name('chat.leave-queue')->middleware('auth');

// Chat routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('chat/conversations', [App\Http\Controllers\Api\ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('chat/conversations/{conversation}/messages', [App\Http\Controllers\Api\ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('chat/messages', [App\Http\Controllers\Api\ChatController::class, 'sendMessage'])->name('chat.send');
    
    // Ticket routes
    Route::resource('tickets', App\Http\Controllers\TicketController::class)->except(['edit', 'update', 'destroy']);
    Route::post('tickets/{ticket}/reply', [App\Http\Controllers\TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/rate', [App\Http\Controllers\TicketController::class, 'rate'])->name('tickets.rate');
    Route::patch('tickets/{ticket}/close', [App\Http\Controllers\TicketController::class, 'close'])->name('tickets.close');
    Route::patch('tickets/{ticket}/reopen', [App\Http\Controllers\TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::get('tickets/download/attachment', [App\Http\Controllers\TicketController::class, 'downloadAttachment'])->name('tickets.download');
});

// Admin chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/chat/conversations/{id}/messages', [ChatController::class, 'messages']);
    Route::post('/admin/chat/messages', [ChatController::class, 'store']);
});

// Comment out Laravel's default broadcast routes - they're not working properly
// Broadcast::routes(['middleware' => ['web', 'auth']]);

// Custom broadcasting authentication route - override Laravel's default
Route::post('/broadcasting/auth', function (Request $request) {
    Log::info('=== CUSTOM BROADCAST AUTH ROUTE ===');
    
    // Get app key and secret from config
    $appKey = config('broadcasting.connections.reverb.key', 'reverb-key');
    $appSecret = config('broadcasting.connections.reverb.secret', 'reverb-secret');
    
    Log::info('Custom broadcast auth request', [
        'user_authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user_email' => Auth::user() ? Auth::user()->email : 'NO USER',
        'session_id' => $request->session()->getId(),
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'channel_name' => $request->get('channel_name'),
        'socket_id' => $request->get('socket_id'),
        'app_key' => $appKey,
    ]);

    // Ensure user is authenticated
    if (!Auth::check()) {
        Log::error('Custom broadcast auth failed: User not authenticated');
        return response()->json(['error' => 'Unauthenticated'], 403);
    }

    $user = Auth::user();
    $channelName = $request->get('channel_name');
    $socketId = $request->get('socket_id');

    Log::info('Attempting to authorize channel', [
        'channel_name' => $channelName,
        'user_id' => $user->id,
        'socket_id' => $socketId,
    ]);

    // Handle private channel authorization
    if (strpos($channelName, 'private-') === 0) {
        $channelNameShort = substr($channelName, 8); // Remove 'private-' prefix
        Log::info('Processing private channel: ' . $channelNameShort);

        // Check if this is a chat channel
        if (preg_match('/^chat\.(\d+)$/', $channelNameShort, $matches)) {
            $conversationId = $matches[1];
            Log::info('Authorizing chat channel', [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
            ]);

            // For testing: allow all authenticated users
            $authorized = true;

            if ($authorized) {
                Log::info('Channel authorization successful');
                
                // Generate the auth signature for Reverb (same format as Pusher)
                $authString = $socketId . ':' . $channelName;
                
                Log::info('Generating auth signature', [
                    'auth_string' => $authString,
                    'app_key' => $appKey,
                    'app_secret' => $appSecret ? 'SET' : 'NOT SET',
                    'original_channel' => $channelName,
                    'socket_id' => $socketId,
                ]);
                
                $authSignature = hash_hmac('sha256', $authString, $appSecret);
                $authResult = $appKey . ':' . $authSignature;
                
                Log::info('Auth signature generated', [
                    'auth_result' => $authResult,
                    'signature' => $authSignature,
                ]);
                
                return response()->json([
                    'auth' => $authResult
                ]);
            } else {
                Log::error('Channel authorization failed');
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
    }

    Log::error('Unknown channel type: ' . $channelName);
    return response()->json(['error' => 'Unknown channel'], 403);
})->middleware(['web', 'auth']);




Route::get('/', function () {
    return view('welcome');
});





Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// need to login first(auth) and then check if the user is admin (is_admin)
// ->prefix('admin') mean add a prefix to the route example /producsts become /admin/products
// ->name('admin.'): 这个方法用于为这个路由组内的所有命名路由添加名称前缀 so when use Route::resource which create products.index it will become admin.products.index
// Admin routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Chat Queue Management Routes
    Route::prefix('chat-queue')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ChatQueueController::class, 'index'])->name('admin.chat-queue.index');
        Route::get('/data', [App\Http\Controllers\Admin\ChatQueueController::class, 'getData'])->name('admin.chat-queue.data');
        Route::post('/{queueId}/accept', [App\Http\Controllers\Admin\ChatQueueController::class, 'acceptChat'])->name('admin.chat-queue.accept');
        Route::post('/{queueId}/assign', [App\Http\Controllers\Admin\ChatQueueController::class, 'assignChat'])->name('admin.chat-queue.assign');
        Route::post('/{queueId}/abandon', [App\Http\Controllers\Admin\ChatQueueController::class, 'abandonChat'])->name('admin.chat-queue.abandon');
        Route::post('/transfer/{conversationId}', [App\Http\Controllers\Admin\ChatQueueController::class, 'transferChat'])->name('admin.chat-queue.transfer');
        Route::post('/complete/{conversationId}', [App\Http\Controllers\Admin\ChatQueueController::class, 'completeChat'])->name('admin.chat-queue.complete');
        Route::get('/status/{conversationId}', [App\Http\Controllers\Admin\ChatQueueController::class, 'getQueueStatus'])->name('admin.chat-queue.status');
        Route::get('/my-chats', [App\Http\Controllers\Admin\ChatQueueController::class, 'getMyChats'])->name('admin.chat-queue.my-chats');
        Route::get('/stats', [App\Http\Controllers\Admin\ChatQueueController::class, 'getStats'])->name('admin.chat-queue.stats');
        Route::post('/agent-status', [App\Http\Controllers\Admin\ChatQueueController::class, 'updateAgentStatus'])->name('admin.chat-queue.agent-status');
    });
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::get('/customers/segmentation', [ApiCustomerController::class, 'segmentation'])->name('admin.customers.segmentation');
    
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    
    Route::get('/reports/product-performance', [ReportController::class, 'productPerformance'])->name('admin.reports.product-performance');
    
    // Chat management routes
    Route::get('/chat', function () {
        return view('admin.chat.index');
    })->name('admin.chat.index');
    Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('admin.chat.conversations');
    
    // Admin ticket management routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/assign', [App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('admin.tickets.assign');
        Route::post('/{ticket}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::patch('/{ticket}/status', [App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('admin.tickets.status');
        Route::patch('/{ticket}/priority', [App\Http\Controllers\Admin\TicketController::class, 'updatePriority'])->name('admin.tickets.priority');
        Route::post('/{ticket}/escalate', [App\Http\Controllers\Admin\TicketController::class, 'escalate'])->name('admin.tickets.escalate');
        Route::get('/stats/data', [App\Http\Controllers\Admin\TicketController::class, 'getStats'])->name('admin.tickets.stats');
        Route::post('/bulk-action', [App\Http\Controllers\Admin\TicketController::class, 'bulkAction'])->name('admin.tickets.bulk');
    });
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Public-facing Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// FAQ Route
Route::get('/faq', [App\Http\Controllers\FaqController::class, 'index'])->name('faq.index');

// Self-Service Routes
Route::prefix('self-service')->group(function () {
    Route::get('/', [App\Http\Controllers\SelfServiceController::class, 'index'])->name('self-service.index');
    Route::get('/{category}', [App\Http\Controllers\SelfServiceController::class, 'category'])->name('self-service.category');
    Route::post('/help', [App\Http\Controllers\SelfServiceController::class, 'help'])->name('self-service.help');
    Route::post('/escalate', [App\Http\Controllers\SelfServiceController::class, 'escalate'])->name('self-service.escalate');
});

require __DIR__.'/auth.php';
