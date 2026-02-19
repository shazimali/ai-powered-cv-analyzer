<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    protected Parser $pdfParser;
    protected WordExtractor $wordExtractor;
    protected OllamaService $ollama;

    public function __construct(OllamaService $ollama, WordExtractor $wordExtractor)
    {
        $this->pdfParser = new Parser();
        $this->wordExtractor = $wordExtractor;
        $this->ollama = $ollama;
    }

    public function extractContent(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $fullPath = storage_path('app/private/' . $path);

        if ($extension === 'pdf') {
            $pdf = $this->pdfParser->parseFile($fullPath);
            return $pdf->getText();
        } elseif ($extension === 'docx') {
            return $this->wordExtractor->extractText($fullPath);
        }

        return '';
    }

    protected function extractText(string $path): string
    {
        return $this->extractContent($path);
    }

    protected function chunkText(string $text, int $chunkSize = 400, int $overlap = 100): array
    {
        $text = preg_replace('/\s+/', ' ', $text);
        $chunks = [];
        $start = 0;
        $length = strlen($text);

        while ($start < $length) {
            $end = $start + $chunkSize;
            if ($end < $length) {
                $lastSpace = strrpos(substr($text, $start, $chunkSize), ' ');
                if ($lastSpace !== false) {
                    $end = $start + $lastSpace;
                }
            }
            $chunks[] = trim(substr($text, $start, $end - $start));
            $start = $end - $overlap;
            if ($start < 0) $start = 0;
            if ($start >= $length - $overlap) break;
        }

        return array_filter($chunks);
    }
}
