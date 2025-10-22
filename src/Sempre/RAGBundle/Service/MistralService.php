<?php
namespace Sempre\RAGBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MistralService
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

    public function ask(string $question, array $documents): string
    {
        $context = implode("\n", array_map(fn($doc) => "- " . $doc, $documents));
        $messages = [
            ["role" => "system", "content" => "Tu réponds uniquement à partir des documents fournis."],
            ["role" => "user", "content" => "Voici les documents :\n$context\n\nQuestion : $question"]
        ];

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => 'ministral-3b-latest',
                    'messages' => $messages,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['choices'][0]['message']['content'];
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response) {
                $body = $response->getBody()->getContents(); // <- contenu complet
                dd($body);
            } else {
                dd($e->getMessage());
            }
        }
    }
}
