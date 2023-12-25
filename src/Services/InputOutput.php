<?php

namespace Microwin7\PHPUtils\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class InputOutput extends SymfonyStyle
{
    /**
     * Ask a question and return the answer.
     */
    public function question(string $question): mixed
    {
        return $this->ask(sprintf(' ✍️  %s', $question));
    }
    /**
     * Formats an success message.
     */
    public function success(string|array $message): void
    {
        $this->block($message, gmdate("H:i:s", time()) . ' OK', 'fg=black;bg=green', ' ', false);
    }
    /**
     * Formats an info message.
     */
    public function info(string|array $message): void
    {
        $this->block($message, gmdate("H:i:s", time()) . ' INFO', 'fg=green', ' ', false);
    }
}
