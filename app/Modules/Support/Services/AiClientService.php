<?php

namespace App\Modules\Support\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiClientService
{
    public function isConfigured(): bool
    {
        return match ($this->chatProvider()) {
            'gemini' => filled(config('services.gemini.key')),
            'openai' => filled(config('services.openai.key')),
            'openrouter' => filled(config('services.openrouter.key')),
            'nvidia' => filled(config('services.nvidia.key')),
            default => false,
        };
    }

    public function embedding(string $input): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return match ($this->embeddingProvider()) {
            'gemini' => $this->geminiEmbedding($input),
            'openai' => $this->openAiEmbedding($input),
            'openrouter' => null,
            'nvidia' => null,
            default => null,
        };
    }

    public function chat(array $messages): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return match ($this->chatProvider()) {
            'gemini' => $this->geminiChat($messages),
            'openai' => $this->openAiChat($messages),
            'openrouter' => $this->openRouterChat($messages),
            'nvidia' => $this->nvidiaChat($messages),
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

    private function chatProvider(): string
    {
        return strtolower((string) config('rag.ai.chat_provider', $this->provider()));
    }

    private function embeddingProvider(): string
    {
        return strtolower((string) config('rag.ai.embedding_provider', $this->provider()));
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

            if (config('rag.ai.gemini_thinking_budget') !== null) {
                $payload['generationConfig']['thinkingConfig'] = [
                    'thinkingBudget' => (int) config('rag.ai.gemini_thinking_budget'),
                ];
            }

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

            Log::info('Gemini chat response received', [
                'model' => $model,
                'finish_reason' => $response->json('candidates.0.finishReason'),
                'prompt_tokens' => $response->json('usageMetadata.promptTokenCount'),
                'candidate_tokens' => $response->json('usageMetadata.candidatesTokenCount'),
                'total_tokens' => $response->json('usageMetadata.totalTokenCount'),
                'response_preview' => \Illuminate\Support\Str::limit((string) $response->json('candidates.0.content.parts.0.text'), 180),
            ]);

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

    private function openRouterChat(array $messages): ?string
    {
        try {
            $model = $this->chatModel();
            $response = $this->openRouterClient()
                ->timeout(30)
                ->post('/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => config('rag.ai.temperature'),
                    'max_tokens' => config('rag.ai.max_tokens'),
                ]);

            if (! $response->successful()) {
                $this->logFailure('OpenRouter chat request failed', $response->status(), $response->body());

                return null;
            }

            Log::info('OpenRouter chat response received', [
                'model' => $model,
                'finish_reason' => $response->json('choices.0.finish_reason'),
                'native_finish_reason' => $response->json('choices.0.native_finish_reason'),
                'prompt_tokens' => $response->json('usage.prompt_tokens'),
                'completion_tokens' => $response->json('usage.completion_tokens'),
                'total_tokens' => $response->json('usage.total_tokens'),
                'response_preview' => \Illuminate\Support\Str::limit((string) $response->json('choices.0.message.content'), 180),
            ]);

            return trim((string) $response->json('choices.0.message.content'));
        } catch (\Throwable $e) {
            Log::warning('OpenRouter chat request error', ['error' => $e->getMessage()]);

            return null;
        }
    }


//     [
//     [
//         'role' => 'system',
//         'content' => 'You are a helpful assistant.',
//     ],
//     [
//         'role' => 'user',
//         'content' => 'Help me polish this sentence.',
//     ],
// ]
    private function nvidiaChat(array $messages): ?string
    {
        try {
            $model = $this->chatModel(); // get the current chat model from .env like meta/llama-3.3-70b-instruct
            $response = $this->nvidiaClient() //https://integrate.api.nvidia.com/v1/chat/completions
                ->timeout(30)
                ->post('/chat/completions', [ // body
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => config('rag.ai.temperature'), //0.1 - 0.3 = 比较稳定，适合客服 / RAG    0.7 - 1.0 = 比较 creative
                    'max_tokens' => config('rag.ai.max_tokens'), // 控制 AI 最多输出多少 token。
                ]);

            if (! $response->successful()) {
                $this->logFailure('NVIDIA chat request failed', $response->status(), $response->body());

                return null;
            }

            Log::info('NVIDIA chat response received', [
                'model' => $model,
                'finish_reason' => $response->json('choices.0.finish_reason'),
                'prompt_tokens' => $response->json('usage.prompt_tokens'),
                'completion_tokens' => $response->json('usage.completion_tokens'),
                'total_tokens' => $response->json('usage.total_tokens'),
                'response_preview' => \Illuminate\Support\Str::limit((string) $response->json('choices.0.message.content'), 180),
            ]);

            return trim((string) $response->json('choices.0.message.content'));
            //             {
            // "choices": [
            //     {
            //     "message": {
            //         "content": "Sure, here is the polished sentence..."
            //     },
            //     "finish_reason": "stop"
            //     }
            // ]
            // }
            // so choices.0.message.content
        } catch (\Throwable $e) {
            Log::warning('NVIDIA chat request error', ['error' => $e->getMessage()]);

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

        if ($this->chatProvider() === 'gemini' && str_starts_with($model, 'gpt-')) {
            return (string) env('GEMINI_CHAT_MODEL', 'gemini-2.5-flash');
        }

        if ($this->chatProvider() === 'openai' && str_starts_with($model, 'gemini-')) {
            return (string) env('OPENAI_CHAT_MODEL', 'gpt-4o-mini');
        }

        if ($this->chatProvider() === 'openrouter' && ! str_contains($model, '/')) {
            return (string) env('OPENROUTER_CHAT_MODEL', 'google/gemini-2.5-flash');
        }

        if ($this->chatProvider() === 'nvidia' && ! str_contains($model, '/')) {
            return (string) env('NVIDIA_CHAT_MODEL', 'meta/llama-3.3-70b-instruct');
        }

        return $model;
    }

    private function embeddingModel(): string
    {
        $model = (string) config('rag.ai.embedding_model');

        if ($this->embeddingProvider() === 'gemini' && str_starts_with($model, 'text-embedding-')) {
            return (string) env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-001');
        }

        if ($this->embeddingProvider() === 'openai' && str_starts_with($model, 'gemini-')) {
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

    private function openRouterClient()
    {
        return Http::withToken(config('services.openrouter.key'))
            ->withHeaders([
                'HTTP-Referer' => config('services.openrouter.referer'),
                'X-OpenRouter-Title' => config('services.openrouter.title'),
            ])
            ->baseUrl(rtrim((string) config('services.openrouter.base_url'), '/'))
            ->acceptJson()
            ->asJson();
    }

    private function nvidiaClient()
    {
        return Http::withToken(config('services.nvidia.key'))
            ->baseUrl(rtrim((string) config('services.nvidia.base_url'), '/'))
            ->acceptJson()
            ->asJson();
    }

    private function logFailure(string $message, int $status, string $body): void
    {
        Log::warning($message, [
            'provider' => $this->provider(),
            'chat_provider' => $this->chatProvider(),
            'embedding_provider' => $this->embeddingProvider(),
            'status' => $status,
            'body' => $body,
        ]);
    }
}
