<?php

namespace App\Modules\Support\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiClientService
{
    public function isConfigured(): bool
    {
        return match ($this->provider()) {
            'gemini' => filled(config('services.gemini.key')),
            'openai' => filled(config('services.openai.key')),
            default => false,
        };
    }

    public function embedding(string $input): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return match ($this->provider()) {
            'gemini' => $this->geminiEmbedding($input),
            'openai' => $this->openAiEmbedding($input),
            default => null,
        };
    }

    public function chat(array $messages): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return match ($this->provider()) {
            'gemini' => $this->geminiChat($messages),
            'openai' => $this->openAiChat($messages),
            default => null,
        };
    }

    public function chatModelName(): string
    {
        return $this->chatModel();
    }

    public function embeddingModelName(): string
    {
        return $this->embeddingModel();
    }

    private function provider(): string
    {
        return strtolower((string) config('rag.ai.provider', 'gemini'));
    }

    private function geminiEmbedding(string $input): ?array
    {
        try {
            $model = $this->embeddingModel();
            $response = $this->geminiClient()
                ->timeout(20)
                ->post("/models/{$model}:embedContent", [
                    'model' => "models/{$model}",
                    'content' => [
                        'parts' => [
                            ['text' => $input],
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                $this->logFailure('Gemini embedding request failed', $response->status(), $response->body());

                return null;
            }

            return $response->json('embedding.values');
        } catch (\Throwable $e) {
            Log::warning('Gemini embedding request error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function geminiChat(array $messages): ?string
    {
        try {
            $system = collect($messages)
                ->where('role', 'system')
                ->pluck('content')
                ->filter()
                ->implode("\n\n");

            $contents = collect($messages)
                ->reject(fn (array $message) => ($message['role'] ?? '') === 'system')
                ->map(function (array $message) {
                    return [
                        'role' => ($message['role'] ?? 'user') === 'assistant' ? 'model' : 'user',
                        'parts' => [
                            ['text' => (string) ($message['content'] ?? '')],
                        ],
                    ];
                })
                ->values()
                ->all();

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => config('rag.ai.temperature'),
                    'maxOutputTokens' => config('rag.ai.max_tokens'),
                ],
            ];

            if ($system !== '') {
                $payload['systemInstruction'] = [
                    'parts' => [
                        ['text' => $system],
                    ],
                ];
            }

            $model = $this->chatModel();
            Log::info('Gemini chat request started', [
                'model' => $model,
            ]);

            $response = $this->geminiClient()
                ->timeout(30)
                ->post("/models/{$model}:generateContent", $payload);

            if (! $response->successful()) {
                $this->logFailure('Gemini chat request failed', $response->status(), $response->body());

                return null;
            }

            return trim((string) $response->json('candidates.0.content.parts.0.text'));
        } catch (\Throwable $e) {
            Log::warning('Gemini chat request error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function openAiEmbedding(string $input): ?array
    {
        try {
            $response = $this->openAiClient()
                ->timeout(20)
                ->post('/embeddings', [
                    'model' => $this->embeddingModel(),
                    'input' => $input,
                ]);

            if (! $response->successful()) {
                $this->logFailure('OpenAI embedding request failed', $response->status(), $response->body());

                return null;
            }

            return $response->json('data.0.embedding');
        } catch (\Throwable $e) {
            Log::warning('OpenAI embedding request error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function openAiChat(array $messages): ?string
    {
        try {
            $response = $this->openAiClient()
                ->timeout(30)
                ->post('/chat/completions', [
                    'model' => $this->chatModel(),
                    'messages' => $messages,
                    'temperature' => config('rag.ai.temperature'),
                    'max_tokens' => config('rag.ai.max_tokens'),
                ]);

            if (! $response->successful()) {
                $this->logFailure('OpenAI chat request failed', $response->status(), $response->body());

                return null;
            }

            return trim((string) $response->json('choices.0.message.content'));
        } catch (\Throwable $e) {
            Log::warning('OpenAI chat request error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function geminiClient()
    {
        return Http::withHeaders([
            'x-goog-api-key' => config('services.gemini.key'),
        ])
            ->baseUrl(rtrim((string) config('services.gemini.base_url'), '/'))
            ->acceptJson()
            ->asJson();
    }

    private function chatModel(): string
    {
        $model = (string) config('rag.ai.chat_model');

        if ($this->provider() === 'gemini' && str_starts_with($model, 'gpt-')) {
            return (string) env('GEMINI_CHAT_MODEL', 'gemini-2.5-flash');
        }

        if ($this->provider() === 'openai' && str_starts_with($model, 'gemini-')) {
            return (string) env('OPENAI_CHAT_MODEL', 'gpt-4o-mini');
        }

        return $model;
    }

    private function embeddingModel(): string
    {
        $model = (string) config('rag.ai.embedding_model');

        if ($this->provider() === 'gemini' && str_starts_with($model, 'text-embedding-')) {
            return (string) env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-001');
        }

        if ($this->provider() === 'openai' && str_starts_with($model, 'gemini-')) {
            return (string) env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small');
        }

        return $model;
    }

    private function openAiClient()
    {
        return Http::withToken(config('services.openai.key'))
            ->baseUrl(rtrim((string) config('services.openai.base_url'), '/'))
            ->acceptJson()
            ->asJson();
    }

    private function logFailure(string $message, int $status, string $body): void
    {
        Log::warning($message, [
            'provider' => $this->provider(),
            'status' => $status,
            'body' => $body,
        ]);
    }
}
