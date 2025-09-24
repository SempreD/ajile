<?php
namespace App\Service\Documentation;

class VectorSearchService
{
    public static function cosineSimilarity(array $vec1, array $vec2): float
    {
        $dot = array_sum(array_map(fn($a, $b) => $a * $b, $vec1, $vec2));
        $normA = sqrt(array_sum(array_map(fn($a) => $a ** 2, $vec1)));
        $normB = sqrt(array_sum(array_map(fn($b) => $b ** 2, $vec2)));

        return $dot / ($normA * $normB);
    }

    public function findTopK(array $queryEmbedding, array $documentsWithEmbeddings, int $k = 2): array
    {
        $scores = [];
        foreach ($documentsWithEmbeddings as $doc => $embedding) {
            $scores[$doc] = self::cosineSimilarity($queryEmbedding, $embedding);
        }
        arsort($scores);
        return array_slice($scores, 0, $k, true);
    }
}
