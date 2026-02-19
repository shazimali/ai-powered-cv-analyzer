<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;
    protected string $embedModel;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.base_url', env('OLLAMA_BASE_URL', 'http://localhost:11434'));
        $this->model = config('services.ollama.model', env('OLLAMA_MODEL', 'gemma3:1b'));
        $this->embedModel = config('services.ollama.embed_model', env('OLLAMA_EMBED_MODEL', 'nomic-embed-text'));
    }

    public function chat(array $messages, array $options = [])
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
        ];

        if (!empty($options)) {
            $payload['options'] = $options;
        }

        $response = Http::timeout(180)->post($this->baseUrl . '/api/chat', $payload);

        return $response->json();
    }

    public function embed(string $text)
    {
        $response = Http::timeout(60)->post($this->baseUrl . '/api/embeddings', [
            'model' => $this->embedModel,
            'prompt' => $text,
        ]);

        return $response->json()['embedding'];
    }
}
