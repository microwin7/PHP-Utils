<?php

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Attributes\AsArguments;
use Microwin7\PHPUtils\Attributes\RegexArguments;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

class RequiredArguments
{
    private array $arguments;
    private array $requiredArguments;
    private ?array $optionalArguments;
    private array $where;

    private AsArguments $argumentsInstance;
    private ?array $regexArguments = null;

    public function __construct()
    {
        $this->argumentsInstance = $this->getArgumentsInstance();
        $this->setRegexArguments();
        $this->setWhereSearch()->setRequiredArguments()->setOptionalArguments()->execute();
    }
    public function __get($name): string|array|null
    {
        return $this->arguments[$name];
    }
    private function getArgumentsInstance(): object
    {
        if ($attributes = (new \ReflectionClass(static::class))->getAttributes(AsArguments::class)) {
            return $attributes[0]->newInstance();
        }
    }
    private function setRegexArguments(): void
    {
        foreach ((new \ReflectionClass(static::class))->getAttributes(RegexArguments::class) as $attribute) {
            $instance = $attribute->newInstance();
            $this->regexArguments[$instance->argument] = $instance->regexp;
        }
    }
    private function setWhereSearch(): static
    {
        $this->where = match ($this->argumentsInstance->whereSearch) {
            'GET' => $_GET,
            'POST' => $_POST,
            'REQUEST' => $_REQUEST,
            'JSON' => Data::getData(),
        };
        return $this;
    }
    private function setRequiredArguments(): static
    {
        $this->requiredArguments = $this->argumentsInstance->required;
        return $this;
    }
    private function setOptionalArguments(): static
    {
        $this->optionalArguments = $this->argumentsInstance?->optional;
        return $this;
    }
    /**
     * Undocumented function
     *
     * @return static
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException
     * @throws \InvalidArgumentException
     */
    private function execute(): void
    {
        if ((!empty($this->requiredArguments) && !empty($this->where)) && count($this->requiredArguments) <= count($this->where)) {
            foreach ($this->requiredArguments as $argument) {
                if (is_array($argument)) {
                    $count = 0;
                    foreach ($argument as $oneFromAllArgumets) {
                        try {
                            $this->setVariable($oneFromAllArgumets);
                            $count++;
                        } catch (RequiredArgumentMissingException $ignored) {
                            $this->with($oneFromAllArgumets, null);
                        }
                    }
                    if ($count === 0) throw new RequiredArgumentMissingException(implode(' or ', $argument));
                } else if (is_string($argument)) {
                    $this->setVariable($argument);
                }
            }
        } else throw new RequiredArgumentMissingException($this->requiredArguments);
        if (!empty($this->optionalArguments)) {
            foreach ($this->requiredArguments as $v) {
                if (is_array($v)) {
                    foreach ($v as $v2level) {
                        foreach (array_keys($this->optionalArguments, $v2level) as $k) {
                            unset($this->optionalArguments[$k]);
                        }
                    }
                }
                foreach (array_keys($this->optionalArguments, $v) as $k) {
                    unset($this->optionalArguments[$k]);
                }
            }
            foreach ($this->optionalArguments as $argument) {
                $this->setVariable($argument, true);
            }
        }
    }
    private function setVariable(string $argument, bool $optional = false)
    {
        if (strrpos($argument, '\\') === false) {
            $this->with($argument, $this->where[$argument] ?? ($optional ? null : throw new RequiredArgumentMissingException($argument)));
            if (isset($this->regexArguments[$argument])) $this->validateVariable($argument, $this->regexArguments[$argument]);
        } else if (enum_exists($argument)) {
            // var_dump(is_a($argument, EnumInterface::class, true));
            $argumentClazz = new \ReflectionClass($argument);
            if ($argumentClazz->implementsInterface(EnumInterface::class) && $argumentClazz->implementsInterface(\BackedEnum::class)) {
                try {
                    if (is_numeric($this->where[$argument::getNameRequestVariable()]))
                        $this->with($argument::getNameVariable(), $argument::from((int)$this->where[$argument::getNameRequestVariable()]));
                    else
                        $this->with($argument::getNameVariable(), $argument::fromString($this->where[$argument::getNameRequestVariable()]));
                } catch (\InvalidArgumentException $exception) {
                    if (!$optional) throw new \InvalidArgumentException($exception);
                    $this->with($argument::getNameVariable(), $argument::getDefault());
                }
            }
        }
    }
    protected function validateVariable(string $key, string $regexp): void
    {
        null === $this->$key
            ?: filter_var($this->$key, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]])
            ?: throw new \ValueError(sprintf('Field "' . $key . '" should be valid with pattern: [' . $regexp . '], "%s" given', $this->$key));
    }
    private function with(string $property, mixed $value): void
    {
        $this->arguments[$property] = $value;
    }
}
