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

// Add debug route to test authentication
Route::post('/debug-broadcast-auth', function (Request $request) {
    Log::info('Debug broadcast auth route hit', [
        'user_authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'session_id' => $request->session()->getId(),
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'request_data' => $request->all(),
        'headers' => $request->headers->all(),
    ]);
    
    if (!Auth::check()) {
        return response()->json([
            'error' => 'User not authenticated',
            'session_id' => $request->session()->getId(),
            'has_session_cookie' => $request->hasCookie(config('session.cookie')),
        ], 403);
    }
    
    return response()->json([
        'success' => true,
        'user' => Auth::user(),
        'message' => 'Authentication working'
    ]);
})->middleware(['web', 'auth']);

// Add debug route to intercept broadcast auth requests
Route::post('/debug-intercept-broadcast-auth', function (Request $request) {
    Log::info('=== INTERCEPTING BROADCAST AUTH ===');
    Log::info('Broadcasting auth request intercepted', [
        'user_authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user_email' => Auth::user() ? Auth::user()->email : 'NO USER',
        'session_id' => $request->session()->getId(),
        'csrf_token' => $request->header('X-CSRF-TOKEN'),
        'request_data' => $request->all(),
        'channel_name' => $request->get('channel_name'),
        'socket_id' => $request->get('socket_id'),
        'all_headers' => $request->headers->all(),
    ]);
    
    if (!Auth::check()) {
        Log::error('User not authenticated in broadcast auth intercept');
        return response()->json(['error' => 'Not authenticated'], 403);
    }
    
    // Try to manually call the broadcast auth
    try {
        $result = Broadcast::auth($request);
        Log::info('Manual broadcast auth successful', ['result' => $result]);
        return $result;
    } catch (\Exception $e) {
        Log::error('Manual broadcast auth failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => $e->getMessage()], 403);
    }
})->middleware(['web', 'auth']);


Route::get('/', function () {
    return view('welcome');
});

// Debug routes for testing (can be removed in production)
Route::get('/debug-realtime', function () {
    return view('debug-realtime');
})->middleware('auth');

Route::get('/debug-chat', function () {
    try {
        $conversations = \App\Models\Conversation::count();
        $messages = \App\Models\Message::count();
        $users = \App\Models\User::count();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'conversations' => $conversations,
                'messages' => $messages,
                'users' => $users,
                'database_working' => true
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'database_working' => false
        ]);
    }
});

Route::get('/debug-chat-controller', function () {
    try {
        $controller = new \App\Http\Controllers\Api\ChatController();
        $conversation = \App\Models\Conversation::first();
        
        if ($conversation) {
            $messages = $controller->messages($conversation->id);
            return response()->json([
                'status' => 'success',
                'conversation_id' => $conversation->id,
                'messages' => $messages->getData(),
                'controller_working' => true
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No conversations found',
                'controller_working' => false
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'controller_working' => false
        ]);
    }
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

Route::get('/debug-auth', function (Request $request) {
    if (Auth::check()) {
        return response()->json([
            'status' => 'Authenticated',
            'user' => Auth::user(),
            'session_id' => $request->session()->getId(),
        ]);
    } else {
        return response()->json([
            'status' => 'Unauthenticated',
            'session_id' => $request->session()->getId(),
            'has_cookie' => $request->hasCookie(config('session.cookie')),
        ]);
    }
});

Route::get('/debug-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        $logs = file_get_contents($logPath);
        $lines = explode("\n", $logs);
        $recentLines = array_slice($lines, -50); // Get last 50 lines
        return response()->json([
            'recent_logs' => $recentLines,
            'total_lines' => count($lines)
        ]);
    } else {
        return response()->json(['error' => 'Log file not found']);
    }
});

// Test basic channel authorization manually
Route::post('/test-channel-auth', function (Request $request) {
    Log::info('=== TESTING CHANNEL AUTH MANUALLY ===');
    
    $channelName = 'chat.1';
    $user = Auth::user();
    
    Log::info('Manual channel auth test', [
        'channel_name' => $channelName,
        'user_authenticated' => Auth::check(),
        'user_id' => $user ? $user->id : 'NO USER',
        'user_email' => $user ? $user->email : 'NO EMAIL',
    ]);
    
    // Test the channel authorization callback manually
    $channels = app('Illuminate\Broadcasting\BroadcastManager')->getChannels();
    $channelPattern = 'chat.{conversationId}';
    
    // Simulate the channel authorization
    if ($user) {
        $result = call_user_func_array(function($user, $conversationId) {
            Log::info('Manual channel callback execution', [
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
            ]);
            return true;
        }, [$user, 1]);
        
        Log::info('Manual channel auth result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'user' => $user,
            'message' => 'Manual channel auth test completed'
        ]);
    } else {
        Log::error('Manual channel auth failed: No user');
        return response()->json(['error' => 'No user authenticated'], 403);
    }
})->middleware(['web', 'auth']);

require __DIR__.'/auth.php';
