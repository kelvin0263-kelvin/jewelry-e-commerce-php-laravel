<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ApiConsumptionController extends Controller
{
    /**
     * Demo: consume another module's API (Inventory) and render in a Product view.
     * Add `?use_api=1` to force HTTP; otherwise tries internal fallback when possible.
     */
    public function inventory(Request $request)
    {
        $error = null;
        $items = [];

        try {
            // Always use API for this demo to respect IFA (can toggle with query flag)
            $useApi = (bool) $request->boolean('use_api', true);

            if ($useApi) {
                // External API consumption (module boundary via HTTP)
                $endpoint = url('/api/inventory');
                $response = Http::timeout(10)->get($endpoint);

                if ($response->failed()) {
                    throw new \RuntimeException('Failed to fetch inventory via API');
                }

                $items = $response->json() ?? [];
            } else {
                // Internal consumption example (direct model access) — faster for local testing
                // NOTE: This bypasses the API boundary; only suitable for in-process scenarios.
                $items = \App\Modules\Inventory\Models\Inventory::with('variations')
                    ->latest()
                    ->get()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('product::products.api-consume', [
            'items' => $items,
            'error' => $error,
            'used_http' => $useApi ?? true,
        ]);
    }

    /**
     * Demo: consume Support module chat API with Sanctum auth.
     * - GET /products/api-demo/support-chat
     * Query params:
     *   - use_api=1|0 (default 1) to use HTTP or internal
     *   - token=... optional Bearer token (if not logged in)
     *   - action=start to start a new chat via HTTP API
     *   - conversation_id=ID to fetch messages for a conversation
     */
    public function supportChat(Request $request)
    {
        $error = null;
        $usedHttp = (bool) $request->boolean('use_api', true);
        $conversations = [];
        $messages = [];
        $action = $request->query('action');
        $conversationId = $request->query('conversation_id');
        $tokenString = null;
        $createdToken = null; // NewAccessToken instance for cleanup

        try {
            if ($usedHttp) {
                // Resolve token: query param > logged-in user token > config
                if ($request->filled('token')) {
                    $tokenString = (string) $request->query('token');
                } elseif (Auth::check()) {
                    $createdToken = Auth::user()->createToken('product-support-demo');
                    $tokenString = $createdToken->plainTextToken;
                } elseif (config('services.support.api_token')) {
                    $tokenString = (string) config('services.support.api_token');
                } else {
                    throw new \RuntimeException('Support API requires authentication. Login first or pass ?token=YOUR_TOKEN');
                }

                $base = rtrim((string) (config('services.support.base_url') ?: url('/')), '/');

                // Optional: start a new chat
                if ($action === 'start') {
                    $payload = [
                        'initial_message' => 'Hello from Product module demo',
                        'priority' => 'normal',
                    ];
                    $start = Http::timeout(10)
                        ->withToken($tokenString)
                        ->post($base . '/api/support/chat/start', $payload);
                    if ($start->failed()) {
                        throw new \RuntimeException('Failed to start chat: ' . $start->body());
                    }
                }

                // Fetch conversations
                $conv = Http::timeout(10)
                    ->withToken($tokenString)
                    ->get($base . '/api/support/chat/conversations');
                if ($conv->failed()) {
                    throw new \RuntimeException('Failed to fetch conversations (' . $conv->status() . ')');
                }
                $conversations = $conv->json() ?? [];

                // Fetch messages for selected or first conversation
                $targetId = $conversationId ?: ($conversations[0]['id'] ?? null);
                if ($targetId) {
                    $msg = Http::timeout(10)
                        ->withToken($tokenString)
                        ->get($base . '/api/support/chat/conversations/' . $targetId . '/messages');
                    if ($msg->ok()) {
                        $messages = $msg->json() ?? [];
                    }
                }
            } else {
                // Internal access (no HTTP) for local demo
                $conversations = \App\Modules\Support\Models\Conversation::with(['user', 'agent'])
                    ->select(['id','user_id','assigned_agent_id','status','started_at','ended_at','end_reason','created_at','updated_at'])
                    ->latest()
                    ->get()
                    ->toArray();

                $targetId = $conversationId ?: ($conversations[0]['id'] ?? null);
                if ($targetId) {
                    $messages = \App\Modules\Support\Models\Message::where('conversation_id', $targetId)
                        ->with('user')
                        ->orderBy('created_at', 'asc')
                        ->get()
                        ->toArray();
                }
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        } finally {
            // Clean up temporary token
            if ($createdToken && isset($createdToken->accessToken)) {
                try { $createdToken->accessToken->delete(); } catch (\Throwable $e) { /* ignore */ }
            }
        }

        return view('product::products.support-chat-demo', [
            'conversations' => $conversations,
            'messages' => $messages,
            'error' => $error,
            'used_http' => $usedHttp,
            'conversation_id' => $conversationId,
        ]);
    }
}
