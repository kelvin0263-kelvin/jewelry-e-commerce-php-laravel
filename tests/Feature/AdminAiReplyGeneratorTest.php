<?php

use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\KnowledgeChunk;
use App\Modules\Support\Models\KnowledgeDocument;
use App\Modules\Support\Models\Message;
use App\Modules\Support\Services\AiClientService;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;

function createChatUser(array $attributes = []): User
{
    return User::create(array_merge([
        'name' => 'Test User',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'is_admin' => false,
    ], $attributes));
}

test('admin can generate a draft AI reply without sending a message', function () {
    config()->set('rag.ai.provider', 'gemini');

    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'mei@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $customer->id,
        'body' => 'How long does shipping take in Malaysia?',
        'message_type' => 'user',
    ]);

    $document = KnowledgeDocument::create([
        'title' => 'Shipping Policy',
        'content' => 'Standard delivery within Malaysia usually takes 3 to 7 business days after payment is confirmed.',
        'source_type' => 'policy',
        'is_active' => true,
    ]);

    KnowledgeChunk::create([
        'knowledge_document_id' => $document->id,
        'chunk_index' => 0,
        'content' => $document->content,
        'metadata' => ['title' => $document->title],
    ]);

    $this->mock(AiClientService::class, function (MockInterface $mock) {
        $mock->shouldReceive('embedding')->andReturn(null);
        $mock->shouldReceive('chat')->once()->andReturn(
            'Hi Mei, standard delivery within Malaysia usually takes 3 to 7 business days after payment is confirmed.'
        );
    });

    $response = $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/ai-reply", [
            'tone' => 'friendly',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('reply', 'Hi Mei, standard delivery within Malaysia usually takes 3 to 7 business days after payment is confirmed.')
        ->assertJsonPath('tone', 'friendly');

    expect(Message::where('conversation_id', $conversation->id)->count())->toBe(1);
});

test('customer cannot generate admin AI replies', function () {
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'pending',
    ]);

    $this
        ->actingAs($customer)
        ->postJson("/admin/chat/conversations/{$conversation->id}/ai-reply", [
            'tone' => 'friendly',
        ])
        ->assertForbidden();
});

test('admin can polish a typed reply without sending a message', function () {
    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'polish-admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'polish-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $customer->id,
        'body' => 'Can I return this if it arrives damaged?',
        'message_type' => 'user',
    ]);

    $this->mock(AiClientService::class, function (MockInterface $mock) {
        $mock->shouldReceive('chat')->once()->andReturn(
            'Hi Mei, if the item arrives damaged, please send us your order number, clear photos, and a short description so we can review it for you.'
        );
    });

    $response = $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/polish-reply", [
            'text' => 'if damage send order no and photo we check',
            'mode' => 'polish',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('reply', 'Hi Mei, if the item arrives damaged, please send us your order number, clear photos, and a short description so we can review it for you.')
        ->assertJsonPath('mode', 'polish');

    expect(Message::where('conversation_id', $conversation->id)->count())->toBe(1);
});

test('polish reply requires typed text', function () {
    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'empty-polish-admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'empty-polish-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
    ]);

    $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/polish-reply", [
            'text' => '',
            'mode' => 'polish',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('text');
});

test('admin draft generator rejects incomplete ai replies and uses fallback', function () {
    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'incomplete-admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'incomplete-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
    ]);

    Message::create([
        'conversation_id' => $conversation->id,
        'user_id' => $customer->id,
        'body' => 'Hello',
        'message_type' => 'user',
    ]);

    $this->mock(AiClientService::class, function (MockInterface $mock) {
        $mock->shouldReceive('embedding')->andReturn(null);
        $mock->shouldReceive('chat')->twice()->andReturn('Dear Customer, Thank you for');
    });

    $response = $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/ai-reply", [
            'tone' => 'friendly',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('source', 'fallback')
        ->assertJsonPath('fallback_reason', 'ai_incomplete')
        ->assertJsonPath('reply', 'Hi Mei Customer, thanks for the details. Could you share your order number or any relevant photos/details so I can check this accurately?');
});

test('polish rejects incomplete ai replies and keeps a complete fallback', function () {
    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'incomplete-polish-admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'incomplete-polish-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
    ]);

    $this->mock(AiClientService::class, function (MockInterface $mock) {
        $mock->shouldReceive('chat')->twice()->andReturn('Dear Customer, Thank you for');
    });

    $response = $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/polish-reply", [
            'text' => 'thank you for asking about return',
            'mode' => 'polish',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('source', 'fallback')
        ->assertJsonPath('fallback_reason', 'ai_incomplete')
        ->assertJsonPath('reply', 'Thank you for asking about return.');
});

test('polish retries once when ai returns incomplete text', function () {
    $admin = createChatUser([
        'name' => 'Admin User',
        'email' => 'retry-polish-admin@example.test',
        'is_admin' => true,
    ]);
    $customer = createChatUser([
        'name' => 'Mei Customer',
        'email' => 'retry-polish-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'assigned_agent_id' => $admin->id,
        'status' => 'active',
    ]);

    $this->mock(AiClientService::class, function (MockInterface $mock) {
        $mock->shouldReceive('chat')
            ->twice()
            ->andReturn(
                'Thank you for reaching out. To help us',
                'Thank you for reaching out. To help us review this properly, please share your order number and a short description of the issue.'
            );
    });

    $response = $this
        ->actingAs($admin)
        ->postJson("/admin/chat/conversations/{$conversation->id}/polish-reply", [
            'text' => 'thank you for reaching out to help us check send order number',
            'mode' => 'polish',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('source', 'ai')
        ->assertJsonPath('fallback_reason', null)
        ->assertJsonPath('reply', 'Thank you for reaching out. To help us review this properly, please share your order number and a short description of the issue.');
});
