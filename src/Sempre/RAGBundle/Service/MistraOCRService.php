<?php
namespace Sempre\RAGBundle\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MistraOCRService
{
    private Client $client;

    public function __construct(private string $apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.mistral.ai/v1/',
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
            ],
        ]);
    }

    public function extractTextFromImage(string $imagePath): ?string
    {
        try {
            // 1️⃣ Upload du fichier vers Mistral pour obtenir un file_id
            $uploadResponse = $this->client->post('files', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($imagePath, 'r'),
                        'filename' => basename($imagePath),
                    ],
                ],
            ]);

            $uploadData = json_decode($uploadResponse->getBody()->getContents(), true);
            $fileId = $uploadData['id'] ?? null;

            if (!$fileId) {
                throw new \RuntimeException('Échec de l’upload du fichier vers Mistral.');
            }

            // 2️⃣ Appel du modèle OCR avec ce file_id
            $ocrResponse = $this->client->post('ocr', [
                'json' => [
                    'model' => 'mistral-ocr-latest',
                    'document' => [
                        'type' => 'file',
                        'file_id' => $fileId,
                    ],
                ],
            ]);

            $ocrData = json_decode($ocrResponse->getBody()->getContents(), true);

            // 3️⃣ Le texte OCR se trouve dans `result.text`
            return $ocrData['result']['text'] ?? null;

        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response) {
                dump('Erreur API:', $response->getBody()->getContents());
            } else {
                dump('Erreur réseau:', $e->getMessage());
            }
            return null;
        }
    }
}
