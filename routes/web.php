<?php

use App\Modules\User\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Modules\Product\Controllers\ProductController;
use App\Modules\Admin\Controllers\DashboardController;
use App\Modules\Admin\Controllers\CustomerController;
use App\Modules\Admin\Controllers\ApiCustomerController as ApiCustomerController;
use App\Modules\Admin\Controllers\ReportController;
use App\Modules\Product\Controllers\AdminProductController as AdminProductController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



// Chat routes - now uses queue system
Route::post('/chat/start', [App\Modules\Support\Controllers\ChatController::class, 'startChat'])->name('chat.start')->middleware('auth');
Route::get('/chat/queue-status/{conversationId}', [App\Modules\Support\Controllers\ChatController::class, 'getQueueStatus'])->name('chat.queue-status')->middleware('auth');
Route::post('/chat/leave-queue/{conversationId}', [App\Modules\Support\Controllers\ChatController::class, 'leaveQueue'])->name('chat.leave-queue')->middleware('auth');
Route::post('/chat/terminate/{conversationId}', [App\Modules\Support\Controllers\ChatController::class, 'terminateConversation'])->name('chat.terminate')->middleware('auth');

// Chat History routes
Route::middleware(['auth'])->prefix('chat-history')->group(function () {
    Route::get('/', [App\Modules\Support\Controllers\ChatHistoryController::class, 'index'])->name('chat-history.index');
    Route::get('/search', [App\Modules\Support\Controllers\ChatHistoryController::class, 'search'])->name('chat-history.search');
    Route::get('/{conversationId}', [App\Modules\Support\Controllers\ChatHistoryController::class, 'show'])->name('chat-history.show');
    Route::get('/{conversationId}/messages', [App\Modules\Support\Controllers\ChatHistoryController::class, 'messages'])->name('chat-history.messages');
    Route::get('/{conversationId}/download', [App\Modules\Support\Controllers\ChatHistoryController::class, 'download'])->name('chat-history.download');
});

// Chat routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('chat/conversations', [App\Modules\Support\Controllers\ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('chat/conversations/{conversation}', [App\Modules\Support\Controllers\ChatController::class, 'show'])->name('chat.conversation.show');
    Route::get('chat/conversations/{conversation}/messages', [App\Modules\Support\Controllers\ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('chat/messages', [App\Modules\Support\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    
    // Ticket routes
    Route::resource('tickets', App\Modules\Support\Controllers\TicketController::class)->except(['edit', 'update', 'destroy']);
    Route::post('tickets/{ticket}/reply', [App\Modules\Support\Controllers\TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/rate', [App\Modules\Support\Controllers\TicketController::class, 'rate'])->name('tickets.rate');
    Route::patch('tickets/{ticket}/close', [App\Modules\Support\Controllers\TicketController::class, 'close'])->name('tickets.close');
    Route::patch('tickets/{ticket}/reopen', [App\Modules\Support\Controllers\TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::get('tickets/download/attachment', [App\Modules\Support\Controllers\TicketController::class, 'downloadAttachment'])->name('tickets.download');
});

// Admin chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/chat/conversations/{id}', [App\Modules\Support\Controllers\ChatController::class, 'show']);
    Route::get('/admin/chat/conversations/{id}/messages', [App\Modules\Support\Controllers\ChatController::class, 'messages']);
    Route::post('/admin/chat/messages', [App\Modules\Support\Controllers\ChatController::class, 'store']);
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

        // Check if this is a conversation channel
        if (preg_match('/^conversation\.(\d+)$/', $channelNameShort, $matches)) {
            $conversationId = $matches[1];
            Log::info('Authorizing conversation channel', [
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
        Route::get('/', [App\Modules\Support\Controllers\ChatQueueController::class, 'index'])->name('admin.chat-queue.index');
        Route::get('/data', [App\Modules\Support\Controllers\ChatQueueController::class, 'getData'])->name('admin.chat-queue.data');
        Route::post('/{queueId}/accept', [App\Modules\Support\Controllers\ChatQueueController::class, 'acceptChat'])->name('admin.chat-queue.accept');
        Route::post('/{queueId}/assign', [App\Modules\Support\Controllers\ChatQueueController::class, 'assignChat'])->name('admin.chat-queue.assign');
        Route::post('/{queueId}/abandon', [App\Modules\Support\Controllers\ChatQueueController::class, 'abandonChat'])->name('admin.chat-queue.abandon');
        Route::post('/transfer/{conversationId}', [App\Modules\Support\Controllers\ChatQueueController::class, 'transferChat'])->name('admin.chat-queue.transfer');
        Route::post('/complete/{conversationId}', [App\Modules\Support\Controllers\ChatQueueController::class, 'completeChat'])->name('admin.chat-queue.complete');
        Route::get('/status/{conversationId}', [App\Modules\Support\Controllers\ChatQueueController::class, 'getQueueStatus'])->name('admin.chat-queue.status');
        Route::get('/my-chats', [App\Modules\Support\Controllers\ChatQueueController::class, 'getMyChats'])->name('admin.chat-queue.my-chats');
        Route::get('/stats', [App\Modules\Support\Controllers\ChatQueueController::class, 'getStats'])->name('admin.chat-queue.stats');
        Route::post('/agent-status', [App\Modules\Support\Controllers\ChatQueueController::class, 'updateAgentStatus'])->name('admin.chat-queue.agent-status');
    });
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::get('/customers/segmentation', [ApiCustomerController::class, 'segmentation'])->name('admin.customers.segmentation');
    
    // Inventory Management (Basic product creation - internal only)
    Route::get('/inventory', [App\Modules\Inventory\Controllers\InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::get('/inventory/create', [App\Modules\Inventory\Controllers\InventoryController::class, 'create'])->name('admin.inventory.create');
    Route::post('/inventory', [App\Modules\Inventory\Controllers\InventoryController::class, 'store'])->name('admin.inventory.store');
    Route::get('/inventory/{product}/edit', [App\Modules\Inventory\Controllers\InventoryController::class, 'edit'])->name('admin.inventory.edit');
    Route::put('/inventory/{product}', [App\Modules\Inventory\Controllers\InventoryController::class, 'update'])->name('admin.inventory.update');
    Route::delete('/inventory/{product}', [App\Modules\Inventory\Controllers\InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
    
    // Product Management (Enhancement and publishing)
    Route::prefix('product-management')->group(function () {
        Route::get('/', [App\Modules\Product\Controllers\ProductManagementController::class, 'index'])->name('admin.product-management.index');
        Route::get('/{product}/enhance', [App\Modules\Product\Controllers\ProductManagementController::class, 'enhance'])->name('admin.product-management.enhance');
        Route::post('/{product}/enhance', [App\Modules\Product\Controllers\ProductManagementController::class, 'storeEnhancement'])->name('admin.product-management.store-enhancement');
        Route::post('/{product}/approve', [App\Modules\Product\Controllers\ProductManagementController::class, 'approve'])->name('admin.product-management.approve');
        Route::post('/{product}/publish', [App\Modules\Product\Controllers\ProductManagementController::class, 'publish'])->name('admin.product-management.publish');
        Route::post('/{product}/unpublish', [App\Modules\Product\Controllers\ProductManagementController::class, 'unpublish'])->name('admin.product-management.unpublish');
        Route::get('/{product}/edit', [App\Modules\Product\Controllers\ProductManagementController::class, 'edit'])->name('admin.product-management.edit');
        Route::put('/{product}', [App\Modules\Product\Controllers\ProductManagementController::class, 'update'])->name('admin.product-management.update');
    });
    
    Route::get('/reports/product-performance', [ReportController::class, 'productPerformance'])->name('admin.reports.product-performance');
    
    // Chat management routes
    Route::get('/chat', function () {
        return view('support::admin.chat.index');
    })->name('admin.chat.index');
    Route::get('/chat/conversations', [App\Modules\Support\Controllers\ChatController::class, 'conversations'])->name('admin.chat.conversations');
    
    // Admin ticket management routes
    Route::prefix('tickets')->group(function () {
        Route::get('/', [App\Modules\Support\Controllers\AdminTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/{ticket}', [App\Modules\Support\Controllers\AdminTicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/assign', [App\Modules\Support\Controllers\AdminTicketController::class, 'assign'])->name('admin.tickets.assign');
        Route::post('/{ticket}/reply', [App\Modules\Support\Controllers\AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::patch('/{ticket}/status', [App\Modules\Support\Controllers\AdminTicketController::class, 'updateStatus'])->name('admin.tickets.status');
        Route::patch('/{ticket}/priority', [App\Modules\Support\Controllers\AdminTicketController::class, 'updatePriority'])->name('admin.tickets.priority');
        Route::post('/{ticket}/escalate', [App\Modules\Support\Controllers\AdminTicketController::class, 'escalate'])->name('admin.tickets.escalate');
        Route::get('/stats/data', [App\Modules\Support\Controllers\AdminTicketController::class, 'getStats'])->name('admin.tickets.stats');
        Route::post('/bulk-action', [App\Modules\Support\Controllers\AdminTicketController::class, 'bulkAction'])->name('admin.tickets.bulk');
    });
});

// Public-facing Product Routes (Customer views - read-only from inventory)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// FAQ Route
Route::get('/faq', [App\Modules\Support\Controllers\FaqController::class, 'index'])->name('faq.index');

// Self-Service Routes
Route::prefix('self-service')->group(function () {
    Route::get('/', [App\Modules\Support\Controllers\SelfServiceController::class, 'index'])->name('self-service.index');
    Route::get('/{category}', [App\Modules\Support\Controllers\SelfServiceController::class, 'category'])->name('self-service.category');
    Route::post('/help', [App\Modules\Support\Controllers\SelfServiceController::class, 'help'])->name('self-service.help');
    Route::post('/escalate', [App\Modules\Support\Controllers\SelfServiceController::class, 'escalate'])->name('self-service.escalate');
});

require __DIR__.'/auth.php';
