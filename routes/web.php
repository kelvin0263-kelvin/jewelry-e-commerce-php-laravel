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

Route::post('/chat/start', function() {
    // Find or create a conversation for the logged-in user
    $conversation = Conversation::firstOrCreate(
        ['user_id' => auth()->id()],
        ['admin_id' => null] // You can set this to a specific admin ID if needed
    );
    return response()->json($conversation);
})->middleware('auth');

// Chat routes for authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::get('chat/conversations/{conversation}/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('chat/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
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
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Public-facing Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');



require __DIR__.'/auth.php';
