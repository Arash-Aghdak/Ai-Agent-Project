<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAiApiKey
    ) {
    }

    public function generateText(string $prompt, string $model = 'gpt-4o-mini'): string
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ],
            ]);

            $data = $response->toArray();

            return $data['choices'][0]['message']['content'] ?? 'No response.';
        } catch (\Throwable $e) {
            throw new \RuntimeException('OpenAI request failed: ' . $e->getMessage());
        }
    }
}