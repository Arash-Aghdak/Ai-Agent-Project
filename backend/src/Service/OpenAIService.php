<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenAIService
{
    private const DEFAULT_MODEL = 'gpt-4o-mini';

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $openAiApiKey
    ) {
    }

    public function generateText(string $prompt, string $model = self::DEFAULT_MODEL): string
    {
        $model = trim($model) !== '' ? trim($model) : self::DEFAULT_MODEL;

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'timeout' => 60,
                'max_duration' => 90,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'temperature' => 0.4,
                    'max_tokens' => 1200,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional AI agent. Answer clearly, structured, and in Markdown when useful.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($statusCode >= 400) {
                throw new \RuntimeException(
                    $this->buildOpenAIErrorMessage($statusCode, $data)
                );
            }

            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!is_string($content) || trim($content) === '') {
                throw new \RuntimeException('OpenAI returned an empty response.');
            }

            return trim($content);

        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException(
                'OpenAI network error or timeout. Please check your internet connection and try again.'
            );
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'OpenAI request failed: ' . $e->getMessage()
            );
        }
    }

    private function buildOpenAIErrorMessage(int $statusCode, array $data): string
    {
        $message = $data['error']['message'] ?? 'Unknown OpenAI error.';
        $type = $data['error']['type'] ?? null;
        $code = $data['error']['code'] ?? null;

        return match ($statusCode) {
            401 => 'OpenAI authentication failed. Please check your API key.',
            403 => 'OpenAI access denied. Please check project permissions or model access.',
            404 => 'OpenAI model or endpoint was not found. Please check the selected model.',
            408 => 'OpenAI request timed out. Please try again.',
            429 => 'OpenAI rate limit or quota reached. Please check billing, quota, or try again later.',
            500, 502, 503, 504 => 'OpenAI service is temporarily unavailable. Please try again later.',
            default => sprintf(
                'OpenAI error (%d): %s%s%s',
                $statusCode,
                $message,
                $type ? ' Type: ' . $type . '.' : '',
                $code ? ' Code: ' . $code . '.' : ''
            ),
        };
    }
}