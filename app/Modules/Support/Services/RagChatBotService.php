<?php

namespace App\Modules\Support\Services;

use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RagChatBotService
{
    public function __construct(
        private readonly RagSearchService $search,
        private readonly AiClientService $ai,
        private readonly ChatEventManager $events,
    ) {
    }

    public function sendReplyForMessage(Message $customerMessage): ?Message
    {
        $customerMessage = $customerMessage->fresh(['user', 'conversation']);

        if (! $customerMessage || ! $this->shouldReply($customerMessage)) {
            return null;
        }

        $conversation = $customerMessage->conversation->fresh();
        $answer = $this->fixedAnswer($customerMessage->body);

        if ($answer === null) {
            $results = $this->search->search($customerMessage->body);
            $answer = $this->answer($customerMessage->body, $results);
        }

        if (! $answer) {
            return null;
        }

        $botMessage = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => null,
            'body' => $answer,
            'message_type' => 'bot',
        ]);

        $this->events->emitMessageSent($botMessage, $conversation);

        return $botMessage;
    }

    private function shouldReply(Message $message): bool
    {
        if (! config('rag.bot.enabled')) {
            return false;
        }

        if ($message->message_type !== 'user') {
            return false;
        }

        $conversation = $message->conversation;

        if (! $conversation instanceof Conversation) {
            return false;
        }

        if ($conversation->ended_at || in_array($conversation->status, ['completed', 'abandoned'], true)) {
            return false;
        }

        if ($conversation->assigned_agent_id) {
            return false;
        }

        if ((int) $message->user_id !== (int) $conversation->user_id) {
            return false;
        }

        $botReplies = Message::where('conversation_id', $conversation->id)
            ->where('message_type', 'bot')
            ->count();

        return $botReplies < config('rag.bot.max_replies_per_conversation');
    }

    private function fixedAnswer(string $question): ?string
    {
        $text = mb_strtolower(trim($question));

        if (preg_match('/\b(what model|which model|are you ai|are you a bot|who are you|你是谁|什么模型)\b/u', $text)) {
            return "I'm Tiffany Assistant, an automated support assistant for this store. I use the store's support knowledge base to answer common questions while you wait for an admin.";
        }

        if (preg_match('/\b(what can you help|what do you do|help me with|can you help|你可以帮|你能帮)\b/u', $text)) {
            return "I can help with shipping time, refund and return policy, payment issues, damaged items, warranty questions, jewelry care tips, and what details to prepare for an admin.";
        }

        if (preg_match('/\b(warranty|保修)\b/u', $text) && preg_match('/\b(how long|duration|period|多久|多长)\b/u', $text)) {
            return "I don't have a fixed warranty duration in the current store knowledge base. For damaged or defective items, please prepare your order number, clear photos, and a short description so an admin can review the case.";
        }

        if (preg_match('/\b(jewelry care|care tips|care guide|clean jewelry|take care|保养|护理)\b/u', $text)) {
            return "Jewelry care tips: keep pieces away from perfume, lotion, sweat, and harsh chemicals; store each piece separately in a dry pouch or box; clean gently with a soft dry cloth; avoid wearing jewelry while swimming, showering, exercising, or sleeping.";
        }

        if (preg_match('/\b(admin|agent|human|real person|真人|客服)\b/u', $text)) {
            return "An admin will join as soon as possible. You can keep sending your question, order number, or issue details here while waiting.";
        }

        if (preg_match('/\b(hi|hello|hey|halo|你好)\b/u', $text)) {
            return "Hi, I'm Tiffany Assistant. I can help with shipping, returns, payment, warranty, jewelry care, and basic order questions while you wait for an admin.";
        }

        return null;
    }

    private function answer(string $question, array $results): ?string
    {
        if ($results === []) {
            return "I'm checking this for you. An admin will join the chat soon, and you can keep sending details here while waiting.";
        }

        $context = collect($results)
            ->map(function (array $result, int $index) {
                $chunk = $result['chunk'];

                return 'Source '.($index + 1).': '.$chunk->document->title."\n".$chunk->content;
            })
            ->implode("\n\n---\n\n");

        $aiAnswer = $this->ai->chat([
            [
                'role' => 'system',
                'content' => 'You are '.config('rag.bot.name').', a concise support assistant for Tiffany Replica. Answer only from the provided context. Give a complete helpful answer in 1 to 4 short sentences. If the context is not enough, say what details the customer should provide and that an admin will help soon. Do not invent policies.',
            ],
            [
                'role' => 'user',
                'content' => "Context:\n{$context}\n\nCustomer question:\n{$question}",
            ],
        ]);

        if ($aiAnswer) {
            if (! $this->isGenericEscalation($aiAnswer) && ! $this->looksIncomplete($aiAnswer)) {
                return $aiAnswer;
            }

            Log::info('RAG bot ignored weak AI answer and used knowledge fallback', [
                'question' => $question,
                'answer' => $aiAnswer,
            ]);
        }

        $bestChunk = $results[0]['chunk'];
        $summary = Str::limit(trim($bestChunk->content), 480);

        Log::info('RAG bot used non-AI fallback answer', [
            'document' => $bestChunk->document->title,
        ]);

        return "Here's what I found from {$bestChunk->document->title}: {$summary}";
    }

    private function isGenericEscalation(string $answer): bool
    {
        $answer = mb_strtolower($answer);

        return str_contains($answer, 'admin will help')
            || str_contains($answer, 'administrator will help')
            || str_contains($answer, 'support team will help')
            || trim($answer) === 'an admin will help soon.';
    }

    private function looksIncomplete(string $answer): bool
    {
        $answer = trim($answer);

        if (str_ends_with($answer, ',') || str_ends_with($answer, ':')) {
            return true;
        }

        return str_word_count($answer) < 8;
    }
}
