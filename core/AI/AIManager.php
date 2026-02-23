<?php

declare(strict_types=1);

namespace CWF\AI;

use CWF\AI\Providers\OpenAIProvider;
use CWF\AI\Providers\AnthropicProvider;
use CWF\AI\Providers\AIProviderInterface;

/**
 * AI Manager for handling multiple AI providers
 *
 * Supports OpenAI, Anthropic, and custom providers with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class AIManager
{
    private array $providers = [];
    private ?string $defaultProvider = null;

    // PHP 8.5 property hooks
    public array $config {
        get => $this->config;
        set {
            $this->config = $value;
            $this->initializeProviders();
        }
    }

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->defaultProvider = $config['default'] ?? 'openai';
    }

    /**
     * Initialize AI providers
     */
    private function initializeProviders(): void
    {
        if (isset($this->config['providers']['openai'])) {
            $this->providers['openai'] = new OpenAIProvider(
                $this->config['providers']['openai']
            );
        }

        if (isset($this->config['providers']['anthropic'])) {
            $this->providers['anthropic'] = new AnthropicProvider(
                $this->config['providers']['anthropic']
            );
        }
    }

    /**
     * Get AI provider
     */
    public function provider(?string $name = null): AIProviderInterface
    {
        $name = $name ?? $this->defaultProvider;

        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException("Provider {$name} not found");
        }

        return $this->providers[$name];
    }

    /**
     * Chat completion
     */
    public function chat(array $messages, array $options = []): AIResponse
    {
        return $this->provider()->chat($messages, $options);
    }

    /**
     * Generate completion
     */
    public function completion(string $prompt, array $options = []): AIResponse
    {
        return $this->provider()->completion($prompt, $options);
    }

    /**
     * Generate embeddings
     */
    public function embeddings(string|array $input, array $options = []): array
    {
        return $this->provider()->embeddings($input, $options);
    }

    /**
     * Stream chat completion
     */
    public function streamChat(array $messages, callable $callback, array $options = []): void
    {
        $this->provider()->streamChat($messages, $callback, $options);
    }

    /**
     * Analyze image (for vision models)
     */
    public function vision(string $imageUrl, string $prompt, array $options = []): AIResponse
    {
        return $this->provider()->vision($imageUrl, $prompt, $options);
    }

    /**
     * Function calling
     */
    public function functions(array $messages, array $functions, array $options = []): AIResponse
    {
        return $this->provider()->functions($messages, $functions, $options);
    }

    /**
     * Register custom provider
     */
    public function extend(string $name, AIProviderInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }
}
