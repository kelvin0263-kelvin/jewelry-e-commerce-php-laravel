<?php

return [
    'bot' => [
        'enabled' => (bool) env('RAG_BOT_ENABLED', true),
        'name' => env('RAG_BOT_NAME', 'Tiffany Assistant'),
        'reply_delay_seconds' => (int) env('RAG_BOT_REPLY_DELAY_SECONDS', 2),
        'max_replies_per_conversation' => (int) env('RAG_BOT_MAX_REPLIES_PER_CONVERSATION', 5),
    ],

    'ai' => [
        'enabled' => (bool) env('RAG_AI_ENABLED', true),
        'provider' => env('AI_PROVIDER', 'gemini'),
        'chat_provider' => env('AI_CHAT_PROVIDER', env('AI_PROVIDER', 'gemini')),
        'embedding_provider' => env('AI_EMBEDDING_PROVIDER', env('AI_PROVIDER', 'gemini')),
        'chat_model' => env('RAG_CHAT_MODEL', env('OPENROUTER_CHAT_MODEL', env('GEMINI_CHAT_MODEL', 'gemini-2.5-flash'))),
        'embedding_model' => env('RAG_EMBEDDING_MODEL', env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-001')),
        'temperature' => (float) env('RAG_CHAT_TEMPERATURE', 0.2),
        'max_tokens' => (int) env('RAG_CHAT_MAX_TOKENS', 220),
        'gemini_thinking_budget' => env('GEMINI_THINKING_BUDGET', 0) === '' ? null : (int) env('GEMINI_THINKING_BUDGET', 0),
    ],

    'retrieval' => [
        'top_k' => (int) env('RAG_TOP_K', 4),
        'chunk_size' => (int) env('RAG_CHUNK_SIZE', 1200),
    ],

    'seed' => [
        'rebuild_chunks' => (bool) env('RAG_REBUILD_ON_SEED', true),
    ],
];
