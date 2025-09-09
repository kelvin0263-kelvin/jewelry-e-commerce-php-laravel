<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Public controllers
use App\Modules\Product\Controllers\ProductController;

// Admin controllers
use App\Modules\Admin\Controllers\DashboardController;
use App\Modules\Admin\Controllers\CustomerController;
use App\Modules\Admin\Controllers\ApiCustomerController as ApiCustomerController;
use App\Modules\Admin\Controllers\ReportController;
use App\Modules\Inventory\Controllers\InventoryController;
use App\Modules\Product\Controllers\ProductManagementController;

// Support controllers (chat/tickets/self-service)
use App\Modules\Support\Controllers\ChatController;
use App\Modules\Support\Controllers\ChatHistoryController;
use App\Modules\Support\Controllers\TicketController;
use App\Modules\Support\Controllers\AdminTicketController;
use App\Modules\Support\Controllers\ChatQueueController;
use App\Modules\Support\Controllers\FaqController;
use App\Modules\Support\Controllers\SelfServiceController;
use App\Modules\User\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'home')->name('home');

// Product catalog (read-only for customers)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Product actions for customers (auth required)
Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/wishlist', [ProductController::class, 'addToWishlist'])->name('products.wishlist');
    Route::post('/products/{product}/cart', [ProductController::class, 'addToCart'])->name('products.cart');
    Route::post('/products/{product}/review', [ProductController::class, 'submitReview'])->name('products.review');
});

// Review routes (public for now)
Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Cart routes
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('checkout.index');

// Wishlist routes
Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove/{id}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');

// FAQ (auth-only as per current app)
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index')->middleware('auth');

// Self-Service knowledge base
Route::prefix('self-service')->group(function () {
    Route::get('/', [SelfServiceController::class, 'index'])->name('self-service.index');
    Route::get('/{category}', [SelfServiceController::class, 'category'])->name('self-service.category');
    Route::post('/help', [SelfServiceController::class, 'help'])->name('self-service.help');
    Route::post('/escalate', [SelfServiceController::class, 'escalate'])->name('self-service.escalate');
});

/*
|--------------------------------------------------------------------------
| Customer: Chat + Tickets (auth)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Live chat lifecycle
    Route::post('/chat/start', [ChatController::class, 'startChat'])->name('chat.start');
    Route::get('/chat/queue-status/{conversationId}', [ChatController::class, 'getQueueStatus'])->name('chat.queue-status');
    Route::post('/chat/leave-queue/{conversationId}', [ChatController::class, 'leaveQueue'])->name('chat.leave-queue');
    Route::post('/chat/terminate/{conversationId}', [ChatController::class, 'terminateConversation'])->name('chat.terminate');

    // Conversations + messages
    Route::get('chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('chat/conversations/{conversation}', [ChatController::class, 'show'])->name('chat.conversation.show');
    Route::get('chat/conversations/{conversation}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('chat/messages', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Ticketing
    Route::resource('tickets', TicketController::class)->except(['edit', 'update', 'destroy']);
    Route::post('tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::patch('tickets/{ticket}/rate', [TicketController::class, 'rate'])->name('tickets.rate');
    Route::patch('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::patch('tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::get('tickets/download/attachment', [TicketController::class, 'downloadAttachment'])->name('tickets.download');
});

// Chat history (auth)
Route::middleware(['auth'])->prefix('chat-history')->group(function () {
    Route::get('/', [ChatHistoryController::class, 'index'])->name('chat-history.index');
    Route::get('/search', [ChatHistoryController::class, 'search'])->name('chat-history.search');
    Route::get('/{conversationId}', [ChatHistoryController::class, 'show'])->name('chat-history.show');
    Route::get('/{conversationId}/messages', [ChatHistoryController::class, 'messages'])->name('chat-history.messages');
    Route::get('/{conversationId}/download', [ChatHistoryController::class, 'download'])->name('chat-history.download');
});

/*
|--------------------------------------------------------------------------
| Custom Broadcasting Auth (kept from your original)
|--------------------------------------------------------------------------
*/

// Commented out default due to custom handling in app
// Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::post('/broadcasting/auth', function (Request $request) {
    Log::info('=== CUSTOM BROADCAST AUTH ROUTE ===');

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

    if (strpos($channelName, 'private-') === 0) {
        $channelNameShort = substr($channelName, 8);
        Log::info('Processing private channel: ' . $channelNameShort);

        if (preg_match('/^conversation\.(\d+)$/', $channelNameShort, $matches)) {
            $conversationId = $matches[1];
            Log::info('Authorizing conversation channel', [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
            ]);

            // For now: allow all authenticated users
            $authorized = true;

            if ($authorized) {
                $authString = $socketId . ':' . $channelName;
                $authSignature = hash_hmac('sha256', $authString, $appSecret);
                $authResult = $appKey . ':' . $authSignature;

                Log::info('Auth signature generated', [
                    'auth_result' => $authResult,
                    'signature' => $authSignature,
                ]);

                return response()->json(['auth' => $authResult]);
            }

            Log::error('Channel authorization failed');
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    Log::error('Unknown channel type: ' . $channelName);
    return response()->json(['error' => 'Unknown channel'], 403);
})->middleware(['web', 'auth']);

/*
|--------------------------------------------------------------------------
| Admin Routes (auth + admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Customer management
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::get('/customers/segmentation', [ApiCustomerController::class, 'segmentation'])->name('admin.customers.segmentation');

    // Inventory management
    Route::prefix('inventory')
        ->name('admin.inventory.')
        ->controller(InventoryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{inventory}/edit', 'edit')->name('edit');
            Route::put('/{inventory}', 'update')->name('update');
            Route::put('/{inventory}/toggle-status', 'toggleStatus')->name('toggleStatus');
            Route::delete('/{inventory}', 'destroy')->name('destroy');
        });

    // Product publishing workflow (with rate limiting)
    Route::prefix('product-management')->middleware(['rate_limit:30,1', 'secure_error_handling', 'database_security'])->group(function () {
        Route::get('/', [ProductManagementController::class, 'index'])->name('admin.product-management.index');
        Route::get('/create', [ProductManagementController::class, 'create'])->name('admin.product-management.create');
        Route::post('/', [ProductManagementController::class, 'store'])->name('admin.product-management.store');
        Route::get('/{product}', [ProductManagementController::class, 'show'])->name('admin.product-management.show');
        Route::get('/{product}/enhance', [ProductManagementController::class, 'enhance'])->name('admin.product-management.enhance');
        Route::post('/{product}/enhance', [ProductManagementController::class, 'storeEnhancement'])->name('admin.product-management.store-enhancement');
        Route::post('/{product}/approve', [ProductManagementController::class, 'approve'])->name('admin.product-management.approve');
        Route::post('/{product}/publish', [ProductManagementController::class, 'publish'])->name('admin.product-management.publish');
        Route::post('/{product}/unpublish', [ProductManagementController::class, 'unpublish'])->name('admin.product-management.unpublish');
        Route::get('/{product}/edit', [ProductManagementController::class, 'edit'])->name('admin.product-management.edit');
        Route::put('/{product}', [ProductManagementController::class, 'update'])->name('admin.product-management.update');
        Route::delete('/{product}', [ProductManagementController::class, 'destroy'])->name('admin.product-management.destroy');
    });

    // Reviews Management
    Route::prefix('reviews')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('admin.reviews.show');
        Route::post('/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::post('/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::delete('/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });

    // Reports
    Route::get('/reports/product-performance', [ReportController::class, 'productPerformance'])->name('admin.reports.product-performance');

    // Admin chat panel
    Route::get('/chat', function () {
        return view('support::admin.chat.index');
    })->name('admin.chat.index');
    Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('admin.chat.conversations');
    Route::get('/chat/conversations/{id}', [ChatController::class, 'show'])->name('admin.chat.show');
    Route::get('/chat/conversations/{id}/messages', [ChatController::class, 'messages'])->name('admin.chat.messages');
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('admin.chat.store');

    // Admin chat queue management
    Route::prefix('chat-queue')->group(function () {
        Route::get('/', [ChatQueueController::class, 'index'])->name('admin.chat-queue.index');
        Route::get('/data', [ChatQueueController::class, 'getData'])->name('admin.chat-queue.data');
        Route::post('/{queueId}/accept', [ChatQueueController::class, 'acceptChat'])->name('admin.chat-queue.accept');
        Route::post('/{queueId}/assign', [ChatQueueController::class, 'assignChat'])->name('admin.chat-queue.assign');
        Route::post('/{queueId}/abandon', [ChatQueueController::class, 'abandonChat'])->name('admin.chat-queue.abandon');
        Route::post('/transfer/{conversationId}', [ChatQueueController::class, 'transferChat'])->name('admin.chat-queue.transfer');
        Route::post('/complete/{conversationId}', [ChatQueueController::class, 'completeChat'])->name('admin.chat-queue.complete');
        Route::get('/status/{conversationId}', [ChatQueueController::class, 'getQueueStatus'])->name('admin.chat-queue.status');
        Route::get('/my-chats', [ChatQueueController::class, 'getMyChats'])->name('admin.chat-queue.my-chats');
        Route::get('/stats', [ChatQueueController::class, 'getStats'])->name('admin.chat-queue.stats');
        Route::post('/agent-status', [ChatQueueController::class, 'updateAgentStatus'])->name('admin.chat-queue.agent-status');
    });

    // Admin tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
        Route::patch('/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('admin.tickets.assign');
        Route::post('/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::patch('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('admin.tickets.status');
        Route::patch('/{ticket}/priority', [AdminTicketController::class, 'updatePriority'])->name('admin.tickets.priority');
        Route::post('/{ticket}/escalate', [AdminTicketController::class, 'escalate'])->name('admin.tickets.escalate');
        Route::get('/stats/data', [AdminTicketController::class, 'getStats'])->name('admin.tickets.stats');
        Route::post('/bulk-action', [AdminTicketController::class, 'bulkAction'])->name('admin.tickets.bulk');
    });
});

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

// Customer dashboard (still available, but home is default after login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

