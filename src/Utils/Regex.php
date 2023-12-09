<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Response\JsonResponse;

class Regex
{
    private $data;
    /**
     * @var string[]
     */
    private array $pattern = [];
    private string $last_name = '';
    private string|null $last_data = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
    public function name(string $name_data): static
    {
        $this->last_name = $name_data;
        $this->last_data = $this->data->{$this->last_name};
        return $this;
    }
    public function pattern(string ...$pattern): static
    {
        $this->pattern = $pattern;
        return $this;
    }
    public function valid(): static
    {
        $this->not_empty()
            ->isset()
            ->preg_match();
        return $this;
    }

    public function isset(): static
    {
        return isset($this->last_data) ? $this : $this->reply();
    }
    public function not_null(): static
    {
        return !is_null($this->last_data) ? $this : $this->reply();
    }
    public function not_empty(): static
    {
        return !empty($this->last_data) ? $this : $this->reply();
    }
    private function preg_match(): static
    {
        foreach ($this->pattern as $pattern) {
            if (!empty($pattern) && $this->last_data !== null && preg_match($pattern, $this->last_data, $v) !== 1) {
                $this->pattern = [];
                $this->reply();
            }
        }
        $this->pattern = [];
        return $this;
    }

    /**
     * @return never
     */
    private function reply()
    {
        JsonResponse::failed('[' . $this->last_name . '] ' . ($this->last_data ?? 'NULL') . ' - значение не прошло проверку');
    }
}
