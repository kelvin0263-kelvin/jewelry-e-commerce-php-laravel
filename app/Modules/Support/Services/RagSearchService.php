<?php

namespace App\Modules\Support\Services;

use App\Modules\Support\Models\KnowledgeChunk;
use App\Modules\Support\Models\KnowledgeDocument;

class RagSearchService
{
    public function __construct(private readonly AiClientService $ai)
    {
    }

    public function rebuild(?int $documentId = null): int
    {
        $documents = KnowledgeDocument::query()
            ->where('is_active', true)
            ->when($documentId, fn ($query) => $query->whereKey($documentId))
            ->get();

        $created = 0;

        foreach ($documents as $document) {
            $document->chunks()->delete();

            foreach ($this->splitContent($document->content) as $index => $content) {
                $embedding = $this->ai->embedding($content);

                KnowledgeChunk::create([
                    'knowledge_document_id' => $document->id,
                    'chunk_index' => $index,
                    'content' => $content,
                    'embedding' => $embedding,
                    'embedding_model' => $embedding ? $this->ai->embeddingModelName() : null,
                    'metadata' => [
                        'title' => $document->title,
                        'source_type' => $document->source_type,
                    ],
                ]);

                $created++;
            }
        }

        return $created;
    }

    public function search(string $query, ?int $limit = null): array
    {
        $limit ??= config('rag.retrieval.top_k');
        $queryEmbedding = $this->ai->embedding($query);

        return KnowledgeChunk::query()
            ->with('document')
            ->whereHas('document', fn ($documentQuery) => $documentQuery->where('is_active', true))
            ->get()
            ->map(function (KnowledgeChunk $chunk) use ($query, $queryEmbedding) {
                $score = ($queryEmbedding && $chunk->embedding)
                    ? $this->cosineSimilarity($queryEmbedding, $chunk->embedding)
                    : $this->keywordScore($query, $chunk);

                return [
                    'chunk' => $chunk,
                    'score' => $score,
                ];
            })
            ->filter(fn (array $result) => $result['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();
    }

    private function splitContent(string $content): array
    {
        $chunkSize = max(500, (int) config('rag.retrieval.chunk_size'));
        $paragraphs = preg_split('/\R{2,}/', trim($content)) ?: [];
        $chunks = [];
        $buffer = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (strlen($buffer) + strlen($paragraph) > $chunkSize && $buffer !== '') {
                $chunks[] = trim($buffer);
                $buffer = '';
            }

            $buffer .= ($buffer === '' ? '' : "\n\n").$paragraph;
        }

        if (trim($buffer) !== '') {
            $chunks[] = trim($buffer);
        }

        return $chunks ?: [trim($content)];
    }

    private function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $count = min(count($a), count($b));

        for ($i = 0; $i < $count; $i++) {
            $dot += (float) $a[$i] * (float) $b[$i];
            $normA += (float) $a[$i] ** 2;
            $normB += (float) $b[$i] ** 2;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function keywordScore(string $query, KnowledgeChunk $chunk): float
    {
        $haystack = mb_strtolower($chunk->document->title.' '.$chunk->content);
        $terms = preg_split('/[^\pL\pN]+/u', mb_strtolower($query)) ?: [];
        $score = 0.0;

        foreach (array_unique($terms) as $term) {
            if (mb_strlen($term) < 3) {
                continue;
            }

            $score += substr_count($haystack, $term);
        }

        return $score;
    }
}
