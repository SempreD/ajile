<?php

namespace App\Service\Documentation;

use Smalot\PdfParser\Parser as PdfParser;

class DocumentLoaderService
{
    private PdfParser $parser;

    public function __construct()
    {
        $this->parser = new PdfParser();
    }

    public function loadDocuments(string $directory): array
    {
        $documents = [];

        foreach (scandir($directory) as $file) {
            if (in_array($file, ['.', '..'])) continue;

            $path = $directory . '/' . $file;

            if (str_ends_with($file, '.pdf')) {
                
                $text = $this->parser->parseFile($path)->getText();
                $documents[] = $this->cleanText($text);
            } elseif (str_ends_with($file, '.csv')) {
                $raw = file_get_contents($path);
                $documents[] = $this->cleanText($raw);
            }
        }

        return $documents;
    }

    private function cleanText(string $text): string
    {
        // Supprime les caractères non imprimables (hors retour/tabulations)
        //$text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        // Supprime les caractères de remplacement (ex : �)
        $text = str_replace(["�", "\xEF\xBB\xBF", "\x00", "\x1A"], '', $text);

        // Nettoie les chaînes visuellement vides ou bruitées
        //$text = preg_replace('/[^\PC\s]/u', '', $text); // conserve ponctuation

        // Normalize: supprime doublons, espaces multiples, sauts de lignes parasites
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{2,}/', "\n\n", $text);
    
        // Re-encode clean UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Trim final
        $text = trim($text);

        if (json_encode($text) === false) {
            throw new \RuntimeException('Texte non encodable en JSON');
        }

        if (empty($text)) {
            throw new \RuntimeException('Texte vide après nettoyage (probablement image uniquement)');
        }

        return $text;
    }

}
