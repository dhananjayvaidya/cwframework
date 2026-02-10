<?php

declare(strict_types=1);

namespace CWF\AI\Providers;

use CWF\AI\AIResponse;

/**
 * AI Provider Interface
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
interface AIProviderInterface
{
    /**
     * Chat completion
     */
    public function chat(array $messages, array $options = []): AIResponse;

    /**
     * Text completion
     */
    public function completion(string $prompt, array $options = []): AIResponse;

    /**
     * Generate embeddings
     */
    public function embeddings(string|array $input, array $options = []): array;

    /**
     * Stream chat completion
     */
    public function streamChat(array $messages, callable $callback, array $options = []): void;

    /**
     * Vision/Image analysis
     */
    public function vision(string $imageUrl, string $prompt, array $options = []): AIResponse;

    /**
     * Function calling
     */
    public function functions(array $messages, array $functions, array $options = []): AIResponse;
}
