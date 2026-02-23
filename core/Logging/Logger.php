<?php

declare(strict_types=1);

namespace CWF\Logging;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;

/**
 * Logging Manager using Monolog
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
class Logger implements LoggerInterface
{
    private MonologLogger $logger;

    public function __construct(
        string $name = 'cwframework',
        ?string $path = null
    ) {
        $this->logger = new MonologLogger($name);

        $path = $path ?? app()->storagePath('logs/app.log');
        $logDir = dirname($path);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Rotating file handler (keeps last 7 days)
        $handler = new RotatingFileHandler($path, 7, MonologLogger::DEBUG);
        $handler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context%\n",
            'Y-m-d H:i:s'
        ));

        $this->logger->pushHandler($handler);
    }

    /**
     * System is unusable
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * Action must be taken immediately
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /**
     * Critical conditions
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /**
     * Runtime errors
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * Interesting events
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
