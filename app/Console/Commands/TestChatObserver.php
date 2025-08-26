<?php

namespace App\Console\Commands;

use App\Modules\Support\Services\ChatEventManager;
use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use Illuminate\Console\Command;

class TestChatObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:test-observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Chat Observer Pattern implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Chat Observer Pattern...');

        try {
            // Get the ChatEventManager instance
            $eventManager = app(ChatEventManager::class);

            // Display current stats
            $stats = $eventManager->getStats();
            $this->info("Current Observer Stats:");
            $this->line("- Observers attached: {$stats['observers_count']}");
            $this->line("- Events processed: {$stats['events_processed']}");

            // Test different events
            $this->info("\n🔥 Testing Message Sent Event...");
            $eventManager->notify('message_sent', [
                'message' => (object)[
                    'id' => 999,
                    'body' => 'Test message from observer command',
                    'conversation_id' => 1
                ],
                'conversation' => (object)[
                    'id' => 1,
                    'user_id' => 1
                ]
            ]);

            $this->info("✅ Message sent event emitted");

            $this->info("\n🔥 Testing Conversation Terminated Event...");
            $eventManager->notify('conversation_terminated', [
                'conversation' => (object)[
                    'id' => 1,
                    'user_id' => 1,
                    'assigned_agent_id' => 2
                ],
                'terminated_by' => 'admin',
                'reason' => 'resolved'
            ]);

            $this->info("✅ Conversation terminated event emitted");

            $this->info("\n🔥 Testing Queue Position Changed Event...");
            $eventManager->notify('queue_position_changed', [
                'conversation_id' => 1,
                'position' => 3,
                'estimated_wait' => 15
            ]);

            $this->info("✅ Queue position changed event emitted");

            // Display updated stats
            $newStats = $eventManager->getStats();
            $this->info("\n📊 Updated Observer Stats:");
            $this->line("- Observers attached: {$newStats['observers_count']}");
            $this->line("- Events processed: {$newStats['events_processed']}");

            // Display event history
            $history = $eventManager->getEventHistory();
            if (!empty($history)) {
                $this->info("\n📝 Recent Event History:");
                foreach (array_slice($history, -5) as $event) {
                    $this->line("- {$event['event']} at {$event['timestamp']} ({$event['observers_count']} observers)");
                }
            }

            $this->info("\n✨ Observer Pattern Test Completed Successfully!");

        } catch (\Exception $e) {
            $this->error("❌ Observer Pattern Test Failed:");
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
