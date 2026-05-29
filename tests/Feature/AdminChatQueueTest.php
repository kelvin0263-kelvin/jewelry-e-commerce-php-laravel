<?php

use App\Modules\Support\Models\ChatQueue;
use App\Modules\Support\Models\Conversation;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

if (! function_exists('createQueueUser')) {
    function createQueueUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Queue Test User',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_admin' => false,
        ], $attributes));
    }
}

test('admin queue page exposes remove from queue action for waiting chats', function () {
    $admin = createQueueUser([
        'name' => 'Queue Admin',
        'email' => 'queue-admin@example.test',
        'is_admin' => true,
    ]);

    $customer = createQueueUser([
        'name' => 'Waiting Customer',
        'email' => 'waiting-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'active',
    ]);

    $queueItem = ChatQueue::create([
        'conversation_id' => $conversation->id,
        'customer_id' => $customer->id,
        'status' => 'waiting',
        'position' => 1,
        'queued_at' => now()->subMinutes(2),
        'priority' => 'normal',
        'initial_message' => 'I need help with my order.',
    ]);

    $this->actingAs($admin)
        ->get('/admin/chat-queue')
        ->assertOk()
        ->assertSee('Remove from Queue')
        ->assertSee("abandonChat({$queueItem->id})", false);
});

test('queue status distinguishes admin removed chats from assigned chats', function () {
    $admin = createQueueUser([
        'name' => 'Queue Admin',
        'email' => 'remove-admin@example.test',
        'is_admin' => true,
    ]);

    $customer = createQueueUser([
        'name' => 'Removed Customer',
        'email' => 'removed-customer@example.test',
    ]);

    $conversation = Conversation::create([
        'user_id' => $customer->id,
        'status' => 'active',
    ]);

    $queueItem = ChatQueue::create([
        'conversation_id' => $conversation->id,
        'customer_id' => $customer->id,
        'status' => 'waiting',
        'position' => 1,
        'queued_at' => now()->subMinutes(2),
        'priority' => 'normal',
    ]);

    $this->actingAs($admin)
        ->postJson("/admin/chat-queue/{$queueItem->id}/abandon")
        ->assertOk()
        ->assertJson(['success' => true]);

    Sanctum::actingAs($customer);

    $this->getJson("/api/support/chat/queue/{$conversation->id}")
        ->assertOk()
        ->assertJson([
            'in_queue' => false,
            'is_assigned' => false,
            'is_terminated' => true,
            'conversation_status' => 'completed',
            'end_reason' => 'abandoned',
        ]);
});

test('chat widget clears ended conversation state and hides composer before rejoining queue', function () {
    $view = file_get_contents(resource_path('views/components/chat-widget.blade.php'));

    expect($view)
        ->toContain('function resetEndedConversationState()')
        ->toContain('chatState.conversationId = null')
        ->toContain('chatState.starting = false')
        ->toContain('function hideChatComposer')
        ->toContain("chatForm.style.display = 'none'")
        ->toContain('resetEndedConversationState();');
});
