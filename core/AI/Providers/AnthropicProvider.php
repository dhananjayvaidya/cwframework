<?php

declare(strict_types=1);

namespace CWF\AI\Providers;

use CWF\AI\AIResponse;
use Anthropic\Anthropic;

/**
 * Anthropic (Claude) Provider
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class AnthropicProvider implements AIProviderInterface
{
    private Anthropic $client;
    private string $model;

    public function __construct(array $config)
    {
        $this->client = Anthropic::factory()
            ->withApiKey($config['api_key'])
            ->make();

        $this->model = $config['model'] ?? 'claude-3-5-sonnet-20241022';
    }

    /**
     * Chat completion
     */
    public function chat(array $messages, array $options = []): AIResponse
    {
        // Extract system message if present
        $systemMessage = '';
        $filteredMessages = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemMessage = $message['content'];
            } else {
                $filteredMessages[] = $message;
            }
        }

        $params = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $filteredMessages,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'temperature' => $options['temperature'] ?? 0.7,
        ];

        if ($systemMessage) {
            $params['system'] = $systemMessage;
        }

        $response = $this->client->messages()->create($params);

        return new AIResponse(
            content: $response->content[0]->text,
            usage: [
                'prompt_tokens' => $response->usage->inputTokens,
                'completion_tokens' => $response->usage->outputTokens,
                'total_tokens' => $response->usage->inputTokens + $response->usage->outputTokens,
            ],
            model: $response->model,
            raw: (array) $response
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
     * Generate embeddings (Anthropic doesn't provide this, use OpenAI)
     */
    public function embeddings(string|array $input, array $options = []): array
    {
        throw new \RuntimeException('Anthropic does not support embeddings. Use OpenAI provider.');
    }

    /**
     * Stream chat completion
     */
    public function streamChat(array $messages, callable $callback, array $options = []): void
    {
        // Extract system message
        $systemMessage = '';
        $filteredMessages = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemMessage = $message['content'];
            } else {
                $filteredMessages[] = $message;
            }
        }

        $params = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $filteredMessages,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'stream' => true,
        ];

        if ($systemMessage) {
            $params['system'] = $systemMessage;
        }

        $stream = $this->client->messages()->createStreamed($params);

        foreach ($stream as $response) {
            if (isset($response->delta->text)) {
                $callback($response->delta->text);
            }
        }
    }

    /**
     * Vision/Image analysis
     */
    public function vision(string $imageUrl, string $prompt, array $options = []): AIResponse
    {
        // Download image and convert to base64
        $imageData = file_get_contents($imageUrl);
        $base64Image = base64_encode($imageData);
        $mediaType = 'image/jpeg'; // Detect from URL or content

        return $this->chat([
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $mediaType,
                            'data' => $base64Image
                        ]
                    ],
                    ['type' => 'text', 'text' => $prompt]
                ]
            ]
        ], array_merge($options, ['model' => 'claude-3-5-sonnet-20241022']));
    }

    /**
     * Function calling (Tool use in Anthropic)
     */
    public function functions(array $messages, array $functions, array $options = []): AIResponse
    {
        // Convert OpenAI function format to Anthropic tools format
        $tools = array_map(function ($func) {
            return [
                'name' => $func['name'],
                'description' => $func['description'],
                'input_schema' => $func['parameters']
            ];
        }, $functions);

        $systemMessage = '';
        $filteredMessages = [];

        foreach ($messages as $message) {
            if ($message['role'] === 'system') {
                $systemMessage = $message['content'];
            } else {
                $filteredMessages[] = $message;
            }
        }

        $params = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $filteredMessages,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'tools' => $tools,
        ];

        if ($systemMessage) {
            $params['system'] = $systemMessage;
        }

        $response = $this->client->messages()->create($params);

        $toolUse = null;
        foreach ($response->content as $block) {
            if (isset($block->type) && $block->type === 'tool_use') {
                $toolUse = [
                    'name' => $block->name,
                    'arguments' => (array) $block->input
                ];
                break;
            }
        }

        return new AIResponse(
            content: $response->content[0]->text ?? '',
            usage: [
                'prompt_tokens' => $response->usage->inputTokens,
                'completion_tokens' => $response->usage->outputTokens,
                'total_tokens' => $response->usage->inputTokens + $response->usage->outputTokens,
            ],
            model: $response->model,
            raw: (array) $response,
            functionCall: $toolUse
        );
    }
}
