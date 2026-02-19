<?php

namespace App\Services;

use ZipArchive;

class WordExtractor
{
    /**
     * Extracts text from a .docx file.
     * DOCX files are zip archives containing several XML files.
     * The main content is in word/document.xml.
     */
    public function extractText(string $path): string
    {
        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($xml) {
                // Remove XML tags and keep text content
                // We use a simple regex-based approach for basic extraction
                // w:t tags contain the actual text
                $text = '';
                $xmlObj = simplexml_load_string($xml);
                if ($xmlObj) {
                    $namespaces = $xmlObj->getNamespaces(true);
                    $xmlObj->registerXPathNamespace('w', $namespaces['w']);
                    $texts = $xmlObj->xpath('//w:t');
                    foreach ($texts as $t) {
                        $text .= (string)$t . ' ';
                    }
                }
                return trim($text);
            }
        }

        return '';
    }
}
