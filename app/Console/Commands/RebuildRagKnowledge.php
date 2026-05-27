<?php

namespace App\Console\Commands;

use App\Modules\Support\Services\RagSearchService;
use Illuminate\Console\Command;

class RebuildRagKnowledge extends Command
{
    protected $signature = 'rag:rebuild {--document-id= : Rebuild only one knowledge document}';

    protected $description = 'Rebuild RAG knowledge chunks and embeddings';

    public function handle(RagSearchService $rag): int
    {
        $documentId = $this->option('document-id') ? (int) $this->option('document-id') : null;

        $this->info('Rebuilding RAG knowledge chunks...');

        $created = $rag->rebuild($documentId);

        $this->info("Created {$created} knowledge chunks.");

        $provider = config('rag.ai.provider');
        $key = $provider === 'gemini' ? config('services.gemini.key') : config('services.openai.key');

        if (! $key) {
            $this->warn(strtoupper($provider).' API key is not set. Chunks were created without embeddings and will use keyword search fallback.');
        }

        return self::SUCCESS;
    }
}
