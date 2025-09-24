<?php
namespace App\Service\Documentation;

use GuzzleHttp\Client;

class OpenAiEmbeddingService
{
    private Client $client;

    public function __construct(private string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ],
        ]);
    }

    /*public function embed(string $text): array
    {
        $response = $this->client->post('embeddings', [
            'json' => [
                'model' => 'text-embedding-3-small',
                'input' => $text
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][0]['embedding'];
    }*/

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
                'model' => 'text-embedding-3-small',
                'input' => $text
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][0]['embedding'];
    }

}
