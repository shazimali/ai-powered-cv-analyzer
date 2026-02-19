<?php

namespace App\Services;

use App\Models\DocumentChunk;

class VectorService
{
    public function findRelevantChunks(array $queryEmbedding, string $queryText, int $limit = 6)
    {
        $chunks = DocumentChunk::all();
        $scoredChunks = [];

        $queryWords = array_filter(explode(' ', strtolower(preg_replace('/[^\w\s]/', '', $queryText))));

        foreach ($chunks as $chunk) {
            $semanticScore = $this->cosineSimilarity($queryEmbedding, $chunk->embedding);
            
            // Keyword Score (Word Overlap)
            $chunkContentLower = strtolower($chunk->content);
            $matchCount = 0;
            foreach ($queryWords as $word) {
                if (str_contains($chunkContentLower, $word)) {
                    $matchCount++;
                }
            }
            $keywordScore = count($queryWords) > 0 ? $matchCount / count($queryWords) : 0;

            // Hybrid Score: 70% Semantic, 30% Keyword
            $hybridScore = ($semanticScore * 0.7) + ($keywordScore * 0.3);

            $scoredChunks[] = [
                'chunk' => $chunk,
                'score' => $hybridScore,
            ];
        }

        usort($scoredChunks, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($scoredChunks, 0, $limit);
    }

    protected function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vec1 as $i => $val) {
            $dotProduct += $val * $vec2[$i];
            $normA += $val * $val;
            $normB += $vec2[$i] * $vec2[$i];
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}
