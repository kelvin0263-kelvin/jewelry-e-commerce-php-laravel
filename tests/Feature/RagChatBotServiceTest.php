<?php

use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\KnowledgeChunk;
use App\Modules\Support\Models\KnowledgeDocument;
use App\Modules\Support\Models\Message;
use App\Modules\Support\Services\AiClientService;
use App\Modules\Support\Services\ChatEventManager;
use App\Modules\Support\Services\RagChatBotService;
use App\Modules\Support\Services\RagSearchService;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

if (! function_exists('createRagBotUser')) {
    function createRagBotUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'RAG Bot User',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ], $attributes));
    }
}

test('bot asks for clarification instead of using knowledge fallback for unclear messages', function () {
    config()->set('rag.bot.enabled', true);
    config()->set('rag.bot.max_replies_per_conversation', 20);
    DB::statement('PRAGMA ignore_check_constraints = ON');

    $customer = createRagBotUser();
    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'active',
    ]);

    $customerMessage = Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $customer->id,
        'body' => '111',
        'message_type' => 'user',
    ]);

    $document = KnowledgeDocument::create([
        'title' => 'Store Contact and Live Chat',
        'content' => 'Customers can continue sending messages while waiting in the live chat queue. An admin will join when available.',
        'source_type' => 'policy',
        'is_active' => true,
    ]);

    $chunk = KnowledgeChunk::create([
        'knowledge_document_id' => $document->id,
        'chunk_index' => 0,
        'content' => $document->content,
        'embedding' => null,
    ]);

    $chunk->load('document');

    $search = Mockery::mock(RagSearchService::class);
    $search->shouldNotReceive('search');

    $ai = Mockery::mock(AiClientService::class);
    $ai->shouldNotReceive('chat');

    $events = Mockery::mock(ChatEventManager::class);
    $events->shouldReceive('emitMessageSent')->once();

    $botMessage = (new RagChatBotService($search, $ai, $events))
        ->sendReplyForMessage($customerMessage);

    expect($botMessage)->not->toBeNull()
        ->and($botMessage->message_type)->toBe('bot')
        ->and($botMessage->body)->toContain("I'm not sure what you mean")
        ->and($botMessage->body)->not->toContain("Here's what I found");
});

test('bot still searches for short support keywords', function () {
    config()->set('rag.bot.enabled', true);
    config()->set('rag.bot.max_replies_per_conversation', 20);
    DB::statement('PRAGMA ignore_check_constraints = ON');

    $customer = createRagBotUser();
    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'active',
    ]);

    $customerMessage = Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $customer->id,
        'body' => 'refund',
        'message_type' => 'user',
    ]);

    $search = Mockery::mock(RagSearchService::class);
    $search->shouldReceive('search')
        ->once()
        ->with('refund')
        ->andReturn([]);

    $ai = Mockery::mock(AiClientService::class);
    $ai->shouldNotReceive('chat');

    $events = Mockery::mock(ChatEventManager::class);
    $events->shouldReceive('emitMessageSent')->once();

    $botMessage = (new RagChatBotService($search, $ai, $events))
        ->sendReplyForMessage($customerMessage);

    expect($botMessage)->not->toBeNull()
        ->and($botMessage->body)->not->toContain("I'm not sure what you mean");
});
