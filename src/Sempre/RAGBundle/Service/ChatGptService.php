<?php
namespace Sempre\RAGBundle\Service;

use GuzzleHttp\Client;

class ChatGptService
{
    private Client $client;

    public function __construct(private string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
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

        $response = $this->client->post('chat/completions', [
            'json' => [
                'model' => 'gpt-5',
                'messages' => $messages,
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['choices'][0]['message']['content'];
    }
}
