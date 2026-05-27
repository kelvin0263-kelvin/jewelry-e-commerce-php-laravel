<?php

namespace App\Jobs;

use App\Modules\Support\Models\Message;
use App\Modules\Support\Services\RagChatBotService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendRagBotReply implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(private readonly int $messageId)
    {
    }

    public function handle(RagChatBotService $bot): void
    {
        $message = Message::find($this->messageId);

        if ($message) {
            $bot->sendReplyForMessage($message);
        }
    }
}
