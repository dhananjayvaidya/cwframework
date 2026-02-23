<?php

declare(strict_types=1);

namespace CWF\AI;

/**
 * AI Response DTO with PHP 8.5 features
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
readonly class AIResponse
{
    public function __construct(
        public string $content,
        public array $usage,
        public string $model,
        public array $raw = [],
        public ?array $functionCall = null
    ) {
    }

    /**
     * Get content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get token usage
     */
    public function getUsage(): array
    {
        return $this->usage;
    }

    /**
     * Get total tokens
     */
    public function getTotalTokens(): int
    {
        return $this->usage['total_tokens'] ?? 0;
    }

    /**
     * Get model used
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get raw response
     */
    public function getRaw(): array
    {
        return $this->raw;
    }

    /**
     * Check if function was called
     */
    public function hasFunctionCall(): bool
    {
        return $this->functionCall !== null;
    }

    /**
     * Get function call
     */
    public function getFunctionCall(): ?array
    {
        return $this->functionCall;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'usage' => $this->usage,
            'model' => $this->model,
            'function_call' => $this->functionCall,
        ];
    }

    /**
     * Convert to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
