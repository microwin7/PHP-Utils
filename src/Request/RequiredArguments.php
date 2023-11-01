<?php

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

class RequiredArguments
{
    private array $arguments;
    public function __construct()
    {
    }
    public function __get($name): string|array
    {
        return $this->arguments[$name];
    }
    public static function validate(array $requiredArguments, ?array $where): static
    {
        if ((!empty($requiredArguments) && !empty($where)) && count($requiredArguments) >= count($where)) {
            $class = new static();
            foreach ($requiredArguments as $argument) {
                $class->with($argument, $where[$argument] ?? new RequiredArgumentMissingException($requiredArguments));
            }
        } else throw new RequiredArgumentMissingException($requiredArguments);
        return $class;
    }
    private function with(string $property, string|array $value): void
    {
        $this->arguments[$property] = $value;
    }
}
