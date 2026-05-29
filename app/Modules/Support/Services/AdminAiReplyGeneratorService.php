<?php

namespace App\Modules\Support\Services;

use App\Modules\Support\Models\Conversation;
use App\Modules\Support\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminAiReplyGeneratorService
{
    public function __construct(
        private readonly RagSearchService $search,
        private readonly AiClientService $ai,
    ) {
    }

    // use for generate the ai reply when the admin click the generate button
    public function generate(Conversation $conversation, string $tone = 'friendly'): array
    {
        $tone = $this->normalizeTone($tone);
        $conversation->loadMissing('user'); // get the user conversation

        $messages = $conversation->messages() // get the latest 10 message in the conversation
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        $latestCustomerMessage = $messages // filter the message sent by the customer (not by admin or bot)
            ->filter(fn (Message $message) => (int) $message->user_id === (int) $conversation->user_id)
            ->last();

        $question = trim((string) ($latestCustomerMessage?->body ?? '')); // get the content of the latest customer message as the question for RAG
        $results = $question !== '' ? $this->search->search($question) : []; // search in the database using RAG (find relevant chucnk to the question)
        $context = $this->formatContext($results); // format the search result to the context that ai can understand 

        $aiReply = config('rag.ai.enabled')
            ? $this->ai->chat($this->prompt($conversation, $messages, $context, $tone)) // call ai to generate the reply based on the conversation, search context, and tone
            : null;
        $aiReply = $this->retryIncompleteReply( // if the ai reply looks incomplete, retry once with a prompt to fix it
            $aiReply,
            fn () => $this->ai->chat($this->generateRetryPrompt($conversation, $messages, $context, $tone, $aiReply))
        );


        $fallbackReason = $this->fallbackReason($aiReply);
        // if there is no fallback reason, use the ai reply; otherwise, use the fallback reply which is a simple 
        // reply based on the search result or a generic message asking for more details
        $reply = $fallbackReason === null 
            ? $aiReply
            : $this->fallbackReply($conversation, $question, $results);

        $this->logFallback('generate', $conversation, $fallbackReason, $aiReply);

        return [
            'reply' => $this->cleanReply($reply),
            'source' => $fallbackReason === null ? 'ai' : 'fallback',
            'fallback_reason' => $fallbackReason,
            'tone' => $tone,
            'sources' => collect($results)
                ->pluck('chunk.document.title')
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }

    // use ai for polish the  reply when the admin click the polish button
    public function polish(Conversation $conversation, string $text, string $mode = 'polish'): array
    {
        $mode = $this->normalizeMode($mode);
        $conversation->loadMissing('user');

        $aiReply = config('rag.ai.enabled')
            ? $this->ai->chat($this->polishPrompt($conversation, $text, $mode))
            : null;
        $aiReply = $this->retryIncompleteReply(
            $aiReply,
            fn () => $this->ai->chat($this->polishRetryPrompt($conversation, $text, $mode, $aiReply))
        );
        $fallbackReason = $this->fallbackReason($aiReply);
        $reply = $fallbackReason === null
            ? $aiReply
            : $this->fallbackPolish($text);

        $this->logFallback('polish', $conversation, $fallbackReason, $aiReply);

        return [
            'reply' => $this->cleanReply($reply),
            'source' => $fallbackReason === null ? 'ai' : 'fallback',
            'fallback_reason' => $fallbackReason,
            'mode' => $mode,
        ];
    }

    // prompt for generate the ai reply based on the conversation history, search context, and tone
    private function prompt(Conversation $conversation, $messages, string $context, string $tone): array
    {
        $history = $messages
            ->map(function (Message $message) use ($conversation) {
                $sender = match (true) {
                    $message->message_type === 'bot' => 'AI assistant',
                    $message->message_type === 'system' => 'System',
                    (int) $message->user_id === (int) $conversation->user_id => 'Customer',
                    default => 'Admin',
                };

                return "{$sender}: {$message->body}";
            })
            ->implode("\n");

        return [
            [
                'role' => 'system',
                'content' => 'You generate draft replies for a jewelry e-commerce admin. Write only the reply text in 1 to 3 complete sentences. Every reply must end with final punctuation. Do not send the message. Do not invent policies, discounts, refunds, stock, delivery guarantees, or order status. If details are missing, ask for the order number or relevant details.',
            ],
            [
                'role' => 'user',
                'content' => "Customer name: {$conversation->user?->name}\nTone: {$tone}\nStore knowledge:\n{$context}\n\nRecent chat:\n{$history}\n\nDraft the next admin reply.",
            ],
        ];
    }

    // prompt for polish the draft reply based on the selected mode (grammar, professional, shorten, or polish)
    private function polishPrompt(Conversation $conversation, string $text, string $mode): array
    {
        $instruction = match ($mode) {
            'grammar' => 'Correct grammar, spelling, punctuation, and sentence flow only. Preserve the original meaning and level of detail.',
            'professional' => 'Rewrite in a professional customer-support tone while preserving the original meaning.',
            'shorten' => 'Make the reply shorter and clearer while preserving the original meaning.',
            default => 'Polish the reply so it is clearer, more meaningful, and natural for customer support while preserving the original meaning.',
        };

        return [
            [
                'role' => 'system',
                'content' => 'You polish draft replies for a jewelry e-commerce admin. Return only the polished reply text in complete sentences with final punctuation. Do not add new facts, policies, refund promises, discount promises, stock claims, delivery guarantees, or order status. If the draft is unclear, keep it cautious and ask for relevant details.',
            ],
            [
                'role' => 'user',
                'content' => "Customer name: {$conversation->user?->name}\nMode: {$mode}\nInstruction: {$instruction}\n\nDraft reply:\n{$text}",
            ],
        ];
    }

    private function generateRetryPrompt(Conversation $conversation, $messages, string $context, string $tone, ?string $incompleteReply): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'Write one complete customer-support reply. Return only the final reply text. The reply must be complete and end with a period, question mark, or exclamation mark.',
            ],
            [
                'role' => 'user',
                'content' => "Customer name: {$conversation->user?->name}\nTone: {$tone}\nStore knowledge:\n{$context}\nPrevious incomplete reply:\n{$incompleteReply}\n\nWrite a complete replacement reply.",
            ],
        ];
    }

    private function polishRetryPrompt(Conversation $conversation, string $text, string $mode, ?string $incompleteReply): array
    {
        return [
            [
                'role' => 'system',
                'content' => 'Rewrite the draft as one complete customer-support reply. Preserve the original meaning. Return only the final reply text. The reply must be complete and end with a period, question mark, or exclamation mark.',
            ],
            [
                'role' => 'user',
                'content' => "Customer name: {$conversation->user?->name}\nMode: {$mode}\nOriginal draft:\n{$text}\n\nPrevious incomplete reply:\n{$incompleteReply}\n\nWrite a complete replacement reply.",
            ],
        ];
    }

    // (generate) format the search result into a readable “context” for ai, if there is no search result, return a default message
    private function formatContext(array $results): string
    {
        if ($results === []) {
            return 'No matching knowledge base entry was found.';
        }

        return collect($results)
            ->map(function (array $result, int $index) {
                $chunk = $result['chunk'];

                return 'Source '.($index + 1).': '.$chunk->document->title."\n".$chunk->content;
            })
            ->implode("\n\n---\n\n");
    }

    private function fallbackReply(Conversation $conversation, string $question, array $results): string
    {
        $name = $conversation->user?->name ?: 'there';

        if ($results !== []) {
            $chunk = $results[0]['chunk'];
            $summary = Str::limit(trim($chunk->content), 360);

            return "Hi {$name}, based on our {$chunk->document->title}, {$summary}";
        }

        if ($question === '') {
            return "Hi {$name}, thanks for reaching out. How can I help you today?";
        }

        return "Hi {$name}, thanks for the details. Could you share your order number or any relevant photos/details so I can check this accurately?";
    }

    private function normalizeTone(string $tone): string
    {
        return in_array($tone, ['friendly', 'professional', 'concise'], true) ? $tone : 'friendly';
    }

    private function normalizeMode(string $mode): string
    {
        return in_array($mode, ['grammar', 'polish', 'professional', 'shorten'], true) ? $mode : 'polish';
    }

    private function fallbackPolish(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        return ucfirst(rtrim($text, ". \t\n\r\0\x0B")).'.';
    }

    private function fallbackReason(?string $reply): ?string
    {
        if (! config('rag.ai.enabled')) {
            return 'ai_disabled';
        }

        if (! $reply || trim($reply) === '') {
            return 'ai_empty';
        }

        if ($this->looksIncomplete($reply)) {
            return 'ai_incomplete';
        }

        return null;
    }

    private function retryIncompleteReply(?string $reply, callable $retry): ?string
    {
        if ($this->fallbackReason($reply) !== 'ai_incomplete') {
            return $reply;
        }

        $retryReply = $retry();

        return $this->fallbackReason($retryReply) === null ? $retryReply : $reply;
    }

    private function logFallback(string $action, Conversation $conversation, ?string $reason, ?string $aiReply): void
    {
        if ($reason === null) {
            return;
        }

        Log::info('Admin AI reply fallback used', [
            'action' => $action,
            'conversation_id' => $conversation->id,
            'customer_id' => $conversation->user_id,
            'reason' => $reason,
            'ai_reply_preview' => Str::limit((string) $aiReply, 160),
        ]);
    }

    private function looksIncomplete(string $reply): bool
    {
        $reply = trim($reply);

        if ($reply === '') {
            return true;
        }

        if (! preg_match('/[.!?)]$/', $reply)) {
            return true;
        }

        if (str_word_count($reply) < 8) {
            return true;
        }

        $lastWord = mb_strtolower((string) preg_replace('/^.*?([\pL\pN]+)[^\pL\pN]*$/u', '$1', $reply));

        return in_array($lastWord, [
            'a',
            'an',
            'and',
            'are',
            'as',
            'at',
            'because',
            'by',
            'for',
            'from',
            'if',
            'in',
            'is',
            'of',
            'or',
            'our',
            'that',
            'the',
            'to',
            'with',
            'your',
        ], true);
    }

    private function cleanReply(string $reply): string
    {
        return trim(preg_replace('/\s+/', ' ', $reply) ?: '');
    }
}
