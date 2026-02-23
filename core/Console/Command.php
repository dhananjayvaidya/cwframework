<?php

declare(strict_types=1);

namespace CWF\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base Command class
 *
 * @author Dhananjay Vaidya
 * @version 3.0.0
 */
abstract class Command extends SymfonyCommand
{
    protected InputInterface $input;
    protected OutputInterface $output;

    /**
     * Execute command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        return $this->handle();
    }

    /**
     * Handle command logic
     */
    abstract protected function handle(): int;

    /**
     * Write info message
     */
    protected function info(string $message): void
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    /**
     * Write error message
     */
    protected function error(string $message): void
    {
        $this->output->writeln("<error>{$message}</error>");
    }

    /**
     * Write success message
     */
    protected function success(string $message): void
    {
        $this->output->writeln("<fg=green>{$message}</>");
    }

    /**
     * Write warning message
     */
    protected function warn(string $message): void
    {
        $this->output->writeln("<comment>{$message}</comment>");
    }

    /**
     * Write line
     */
    protected function line(string $message): void
    {
        $this->output->writeln($message);
    }
}
