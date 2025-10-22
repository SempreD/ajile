<?php
namespace Sempre\RAGBundle\Service;

use GuzzleHttp\Client;

class MistralEmbeddingService
{
    private Client $client;

    public function __construct(private string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.mistral.ai/v1/',
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    public function embed(string $text): array
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x1F\x7F]/u', '', $text);
        $text = trim($text);

        if (!mb_check_encoding($text, 'UTF-8')) {
            throw new \RuntimeException('Texte non valide (UTF-8).');
        }

        $response = $this->client->post('embeddings', [
            'json' => [
                'model' => 'mistral-embed',
                'input' => $text
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][0]['embedding'];
    }

}
