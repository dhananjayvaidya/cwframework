<?php

declare(strict_types=1);

namespace CWF\AI\Providers;

use CWF\AI\AIResponse;
use OpenAI\Client;

/**
 * OpenAI Provider using official PHP client
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class OpenAIProvider implements AIProviderInterface
{
    private Client $client;
    private string $model;

    public function __construct(array $config)
    {
        $this->client = \OpenAI::client($config['api_key']);
        $this->model = $config['model'] ?? 'gpt-4-turbo-preview';
    }

    /**
     * Chat completion
     */
    public function chat(array $messages, array $options = []): AIResponse
    {
        $response = $this->client->chat()->create([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'top_p' => $options['top_p'] ?? 1,
            'frequency_penalty' => $options['frequency_penalty'] ?? 0,
            'presence_penalty' => $options['presence_penalty'] ?? 0,
        ]);

        return new AIResponse(
            content: $response->choices[0]->message->content,
            usage: [
                'prompt_tokens' => $response->usage->promptTokens,
                'completion_tokens' => $response->usage->completionTokens,
                'total_tokens' => $response->usage->totalTokens,
            ],
            model: $response->model,
            raw: $response->toArray()
        );
    }

    /**
     * Text completion
     */
    public function completion(string $prompt, array $options = []): AIResponse
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt]
        ], $options);
    }

    /**
     * Generate embeddings
     */
    public function embeddings(string|array $input, array $options = []): array
    {
        $response = $this->client->embeddings()->create([
            'model' => $options['model'] ?? 'text-embedding-3-small',
            'input' => $input,
        ]);

        return array_map(
            fn($item) => $item->embedding,
            $response->embeddings
        );
    }

    /**
     * Stream chat completion
     */
    public function streamChat(array $messages, callable $callback, array $options = []): void
    {
        $stream = $this->client->chat()->createStreamed([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        foreach ($stream as $response) {
            $content = $response->choices[0]->delta->content ?? '';
            if ($content) {
                $callback($content);
            }
        }
    }

    /**
     * Vision/Image analysis
     */
    public function vision(string $imageUrl, string $prompt, array $options = []): AIResponse
    {
        return $this->chat([
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $prompt],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]]
                ]
            ]
        ], array_merge($options, ['model' => 'gpt-4-vision-preview']));
    }

    /**
     * Function calling
     */
    public function functions(array $messages, array $functions, array $options = []): AIResponse
    {
        $response = $this->client->chat()->create([
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'functions' => $functions,
            'function_call' => $options['function_call'] ?? 'auto',
        ]);

        $choice = $response->choices[0];

        return new AIResponse(
            content: $choice->message->content ?? '',
            usage: [
                'prompt_tokens' => $response->usage->promptTokens,
                'completion_tokens' => $response->usage->completionTokens,
                'total_tokens' => $response->usage->totalTokens,
            ],
            model: $response->model,
            raw: $response->toArray(),
            functionCall: isset($choice->message->functionCall) ? [
                'name' => $choice->message->functionCall->name,
                'arguments' => json_decode($choice->message->functionCall->arguments, true)
            ] : null
        );
    }
}
