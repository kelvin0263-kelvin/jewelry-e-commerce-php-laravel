<?php

use App\Modules\Support\Services\AiClientService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;

test('gemini chat logs finish reason and token usage', function () {
    config()->set('rag.ai.provider', 'gemini');
    config()->set('rag.ai.chat_provider', 'gemini');
    config()->set('rag.ai.chat_model', 'gemini-2.5-flash');
    config()->set('rag.ai.max_tokens', 400);
    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

    Http::fake([
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent' => Http::response([
            'candidates' => [
                [
                    'finishReason' => 'MAX_TOKENS',
                    'content' => [
                        'parts' => [
                            ['text' => 'Thank you for reaching out. To help us'],
                        ],
                    ],
                ],
            ],
            'usageMetadata' => [
                'promptTokenCount' => 25,
                'candidatesTokenCount' => 40,
                'totalTokenCount' => 65,
            ],
        ], 200),
    ]);

    Log::spy();

    $reply = app(AiClientService::class)->chat([
        ['role' => 'user', 'content' => 'Polish this reply.'],
    ]);

    expect($reply)->toBe('Thank you for reaching out. To help us');

    Log::shouldHaveReceived('info')
        ->with('Gemini chat response received', Mockery::on(function (array $context) {
            return $context['model'] === 'gemini-2.5-flash'
                && $context['finish_reason'] === 'MAX_TOKENS'
                && $context['prompt_tokens'] === 25
                && $context['candidate_tokens'] === 40
                && $context['total_tokens'] === 65;
        }))
        ->once();
});

test('gemini chat sends configured thinking budget', function () {
    config()->set('rag.ai.provider', 'gemini');
    config()->set('rag.ai.chat_provider', 'gemini');
    config()->set('rag.ai.chat_model', 'gemini-2.5-flash');
    config()->set('rag.ai.max_tokens', 400);
    config()->set('rag.ai.gemini_thinking_budget', 0);
    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

    Http::fake([
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent' => Http::response([
            'candidates' => [
                [
                    'finishReason' => 'STOP',
                    'content' => [
                        'parts' => [
                            ['text' => 'Complete reply.'],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    app(AiClientService::class)->chat([
        ['role' => 'user', 'content' => 'Polish this reply.'],
    ]);

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return data_get($payload, 'generationConfig.thinkingConfig.thinkingBudget') === 0;
    });
});

test('openrouter chat sends compatible request and logs metadata', function () {
    config()->set('rag.ai.provider', 'openrouter');
    config()->set('rag.ai.chat_provider', 'openrouter');
    config()->set('rag.ai.chat_model', 'openai/gpt-4o-mini');
    config()->set('rag.ai.max_tokens', 400);
    config()->set('rag.ai.temperature', 0.2);
    config()->set('services.openrouter.key', 'openrouter-key');
    config()->set('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
    config()->set('services.openrouter.referer', 'http://localhost');
    config()->set('services.openrouter.title', 'Jewelry E-commerce');

    Http::fake([
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'finish_reason' => 'stop',
                    'native_finish_reason' => 'STOP',
                    'message' => [
                        'content' => 'Hi, thanks for reaching out. Please share your order number so we can check this for you.',
                    ],
                ],
            ],
            'usage' => [
                'prompt_tokens' => 30,
                'completion_tokens' => 18,
                'total_tokens' => 48,
            ],
        ], 200),
    ]);

    Log::spy();

    $reply = app(AiClientService::class)->chat([
        ['role' => 'system', 'content' => 'You are support.'],
        ['role' => 'user', 'content' => 'Draft a reply.'],
    ]);

    expect($reply)->toBe('Hi, thanks for reaching out. Please share your order number so we can check this for you.');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->url() === 'https://openrouter.ai/api/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer openrouter-key')
            && $request->hasHeader('HTTP-Referer', 'http://localhost')
            && $request->hasHeader('X-OpenRouter-Title', 'Jewelry E-commerce')
            && data_get($payload, 'model') === 'openai/gpt-4o-mini'
            && data_get($payload, 'max_tokens') === 400
            && data_get($payload, 'temperature') === 0.2
            && count(data_get($payload, 'messages')) === 2;
    });

    Log::shouldHaveReceived('info')
        ->with('OpenRouter chat response received', Mockery::on(function (array $context) {
            return $context['model'] === 'openai/gpt-4o-mini'
                && $context['finish_reason'] === 'stop'
                && $context['native_finish_reason'] === 'STOP'
                && $context['prompt_tokens'] === 30
                && $context['completion_tokens'] === 18
                && $context['total_tokens'] === 48;
        }))
        ->once();
});

test('openrouter provider is configured by openrouter key', function () {
    config()->set('rag.ai.provider', 'openrouter');
    config()->set('rag.ai.chat_provider', 'openrouter');
    config()->set('services.openrouter.key', 'openrouter-key');

    expect(app(AiClientService::class)->isConfigured())->toBeTrue();
});

test('chat and embedding providers can be configured separately', function () {
    config()->set('rag.ai.provider', 'gemini');
    config()->set('rag.ai.chat_provider', 'nvidia');
    config()->set('rag.ai.embedding_provider', 'gemini');
    config()->set('services.nvidia.key', 'nvidia-key');
    config()->set('services.gemini.key', 'gemini-key');

    expect(app(AiClientService::class)->isConfigured())->toBeTrue();
});

test('nvidia chat sends openai compatible request and logs metadata', function () {
    config()->set('rag.ai.chat_provider', 'nvidia');
    config()->set('rag.ai.chat_model', 'meta/llama-3.3-70b-instruct');
    config()->set('rag.ai.max_tokens', 1024);
    config()->set('rag.ai.temperature', 0.2);
    config()->set('services.nvidia.key', 'nvidia-key');
    config()->set('services.nvidia.base_url', 'https://integrate.api.nvidia.com/v1');

    Http::fake([
        'https://integrate.api.nvidia.com/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'finish_reason' => 'stop',
                    'message' => [
                        'content' => 'Hi, thanks for reaching out. Please provide your order number so we can check this for you.',
                    ],
                ],
            ],
            'usage' => [
                'prompt_tokens' => 22,
                'completion_tokens' => 17,
                'total_tokens' => 39,
            ],
        ], 200),
    ]);

    Log::spy();

    $reply = app(AiClientService::class)->chat([
        ['role' => 'user', 'content' => 'Draft a reply.'],
    ]);

    expect($reply)->toBe('Hi, thanks for reaching out. Please provide your order number so we can check this for you.');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->url() === 'https://integrate.api.nvidia.com/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer nvidia-key')
            && data_get($payload, 'model') === 'meta/llama-3.3-70b-instruct'
            && data_get($payload, 'max_tokens') === 1024
            && data_get($payload, 'temperature') === 0.2;
    });

    Log::shouldHaveReceived('info')
        ->with('NVIDIA chat response received', Mockery::on(function (array $context) {
            return $context['model'] === 'meta/llama-3.3-70b-instruct'
                && $context['finish_reason'] === 'stop'
                && $context['prompt_tokens'] === 22
                && $context['completion_tokens'] === 17
                && $context['total_tokens'] === 39;
        }))
        ->once();
});
