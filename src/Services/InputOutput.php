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
        $this->block($message, gmdate("H:i:s", time()) . ' OK', 'fg=black;bg=green', ' ', true);
    }
    public function error(string|array $message): void
    {
        $this->block($message, gmdate("H:i:s", time()) . ' ERROR', 'fg=white;bg=red', ' ', true);
    }
    public function warning(string|array $message): void
    {
        $this->block($message, gmdate("H:i:s", time()) . ' WARNING', 'fg=black;bg=yellow', ' ', true);
    }
    /**
     * Formats an info message.
     */
    public function info(string|array $message): void
    {
        $this->block($message, gmdate("H:i:s", time()) . ' INFO', 'fg=green', ' ', false);
    }
    public function wrong(string $message): void
    {
        $this->block(sprintf(' 😮  %s', $message), null, 'fg=white;bg=red', ' ', false);
    }
}
