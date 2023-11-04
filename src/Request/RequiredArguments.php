<?php

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Attributes\AsArguments;
use Microwin7\PHPUtils\Exceptions\Handler\ExceptionHandler;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

class RequiredArguments extends ExceptionHandler
{
    private array $arguments;
    private array $requiredArguments;
    private ?array $optionalArguments;
    private array $where;

    private AsArguments $attributesInstance;

    public function __construct()
    {
        parent::__construct();
        $this->attributesInstance = $this->getAttributesInstance();
        $this->setWhereSearch()->setRequiredArguments()->setOptionalArguments()->execute();
    }
    public function __get($name): string|array|null
    {
        return $this->arguments[$name];
    }
    private static function getAttributesInstance(): object
    {
        if ($attribute = (new \ReflectionClass(static::class))->getAttributes(AsArguments::class)) {
            return $attribute[0]->newInstance();
        }
    }
    private function setWhereSearch(): static
    {
        $this->where = match ($this->attributesInstance->whereSearch) {
            'GET' => $_GET,
            'POST' => $_POST,
            'REQUEST' => $_REQUEST,
            'JSON' => Data::getData(),
        };
        return $this;
    }
    private function setRequiredArguments(): static
    {
        $this->requiredArguments = $this->attributesInstance->required;
        return $this;
    }
    private function setOptionalArguments(): static
    {
        $this->optionalArguments = $this->attributesInstance?->optional;
        return $this;
    }
    private function execute(): static
    {
        if ((!empty($this->requiredArguments) && !empty($this->where)) && count($this->requiredArguments) <= count($this->where)) {
            foreach ($this->requiredArguments as $argument) {
                if (is_string($argument)) {
                    $this->with($argument, $this->where[$argument] ?? new RequiredArgumentMissingException($this->requiredArguments));
                } else {
                    $count = 0;
                    foreach ($argument as $oneFromAllArgumets) {
                        if (isset($this->where[$oneFromAllArgumets])) {
                            $this->with($oneFromAllArgumets, $this->where[$oneFromAllArgumets]);
                            $count++;
                        } else {
                            $this->with($oneFromAllArgumets, null);
                        }
                    }
                    if ($count === 0) new RequiredArgumentMissingException(implode(' or ', $argument));
                }
            }
        } else throw new RequiredArgumentMissingException($this->requiredArguments);
        if (!empty($this->optionalArguments)) {
            foreach (array_intersect($this->requiredArguments, $this->optionalArguments) as $argument) {
                unset($this->optionalArguments[$argument]);
            }
            foreach ($this->optionalArguments as $argument) {
                $this->with($argument, $this->where[$argument] ?? null);
            }
        }
        return $this;
    }
    // array value??
    private function with(string $property, string|array|null $value): void
    {
        $this->arguments[$property] = $value;
    }
}
