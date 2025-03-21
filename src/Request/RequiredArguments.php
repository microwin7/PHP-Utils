<?php

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Rules\Regex;
use Microwin7\PHPUtils\Attributes\AsArguments;
use function Microwin7\PHPUtils\implodeRecursive;
use Microwin7\PHPUtils\Attributes\RegexArguments;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Component\Enum\HTTP;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;
use Microwin7\PHPUtils\Exceptions\RegexArgumentsFailedException;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

class RequiredArguments
{
    /** @var array<string, string|int|bool|EnumRequestInterface|EnumInterface|\BackedEnum|non-empty-array<int|string, array<int|string, mixed>|string>|null> $arguments */
    private array $arguments = [];
    /** @var array<string|string[]> $requiredArguments */
    private array $requiredArguments;
    /** @var string[]|null $optionalArguments */
    private ?array $optionalArguments;
    /** @var array<int|string, non-empty-array<int|string, array<int|string, mixed>|string>|string> $where */
    private array $where;

    private AsArguments $argumentsInstance;
    /** @var array<string, RegexArguments>|null $regexArguments */
    private ?array $regexArguments = null;

    public function __construct(private \ReflectionFunctionAbstract|null $reflectionFunctionAbstract = null)
    {
        $this->setArgumentsInstance();
        $this->setRegexArguments();
        $this->setWhereSearch();
        $this->setRequiredArguments();
        $this->setOptionalArguments();
        $this->execute();
    }
    /**
     * @return string|int|bool|EnumRequestInterface|\BackedEnum|EnumInterface|non-empty-array<int|string, array<int|string, mixed>|string>|null
     */
    public function __get(string $name): string|int|bool|array|null|object
    {
        return $this->arguments[$name];
    }
    public function __isset(string $name): bool
    {
        return isset($this->arguments[$name]);
    }
    public function getInstance(): static
    {
        return $this;
    }
    private function setArgumentsInstance(): void
    {
        if ($this->reflectionFunctionAbstract !== null) {
            $this->argumentsInstance = $this->reflectionFunctionAbstract->getAttributes(AsArguments::class)[0]->newInstance();
        } else if ($attributes = (new \ReflectionClass(static::class))->getAttributes(AsArguments::class))
            $this->argumentsInstance = $attributes[0]->newInstance();
        else
            throw new RequiredArgumentMissingException('Attribute: ' . AsArguments::class . ' missing');
    }
    private function setRegexArguments(): void
    {
        if ($this->reflectionFunctionAbstract !== null) {
            foreach ($this->reflectionFunctionAbstract->getAttributes(RegexArguments::class) as $attribute) {
                $instance = $attribute->newInstance();
                $this->regexArguments[$instance->argument] = $instance;
            }
        }
        foreach ((new \ReflectionClass(static::class))->getAttributes(RegexArguments::class) as $attribute) {
            $instance = $attribute->newInstance();
            $this->regexArguments[$instance->argument] = $instance;
        }
    }
    private function setWhereSearch(): void
    {
        $this->where = match ($this->argumentsInstance->whereSearch) {
            HTTP::GET => $_GET,
            HTTP::POST => $_POST,
            HTTP::REQUEST => $_REQUEST,
            HTTP::JSON => Data::getData(),
        };
    }
    private function setRequiredArguments(): void
    {
        $this->requiredArguments = $this->argumentsInstance->required;
    }
    private function setOptionalArguments(): void
    {
        $this->optionalArguments = $this->argumentsInstance->optional;
    }
    /**
     * @return void
     * 
     * @throws RequiredArgumentMissingException
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
                        } catch (RequiredArgumentMissingException) {
                            $this->with($oneFromAllArgumets, null);
                        }
                    }
                    if ($count === 0) throw new RequiredArgumentMissingException(implodeRecursive(' or ', $argument));
                } else {
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
    private function setVariable(string $argument, bool $optional = false): void
    {
        if (strrpos($argument, '\\') === false) {
            $VALUE = $this->where[$argument] ?? ($optional ? null : throw new RequiredArgumentMissingException($argument));
            $this->with($argument, $VALUE);
            if (isset($this->regexArguments[$argument]) && isset($this->where[$argument])) {
                $this->validateVariable($this->regexArguments[$argument]);
                /** @psalm-suppress RiskyCast */
                $VALUE = match ($this->regexArguments[$argument]->regexp) {
                    Regex::BOOLEAN_REGXP => (bool) $VALUE,
                    Regex::NUMERIC_REGXP => (int) $VALUE,
                    default => $VALUE
                };
            }
        } else if (enum_exists($argument)) {
            $argumentClazz = new \ReflectionClass($argument);
            if (
                $argumentClazz->implementsInterface(\BackedEnum::class) &&
                $argumentClazz->implementsInterface(EnumInterface::class) &&
                $argumentClazz->implementsInterface(EnumRequestInterface::class)
            ) {
                /** @var interface-string<\BackedEnum & EnumInterface & EnumRequestInterface> $enumClass */
                $enumClass = $argument;
                try {
                    $VALUE = $this->where[$enumClass::getNameRequestVariable()] ?? throw new RequiredArgumentMissingException('Missing Request variable: ' . $enumClass::getNameRequestVariable());
                    if (is_numeric($VALUE))
                        $this->with($enumClass::getNameVariable(), $enumClass::from((int)$VALUE));
                    elseif (is_string($VALUE))
                        $this->with($enumClass::getNameVariable(), $enumClass::fromString($VALUE));
                } catch (\InvalidArgumentException $exception) {
                    if (!$optional) throw new \InvalidArgumentException((string)$exception);
                    $this->with($enumClass::getNameVariable(), $enumClass::getDefault());
                }
            }
        }
    }
    protected function validateVariable(RegexArguments $regexArgument): void
    {
        null === $this->{$regexArgument->argument}
            ?: filter_var($this->{$regexArgument->argument}, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexArgument->regexp]]) !== false
            ?: (
                !is_null($regexArgument->messageCallback)
                ? throw new RegexArgumentsFailedException($regexArgument->messageCallback)
                : throw RegexArgumentsFailedException::pattern($regexArgument->argument, $regexArgument->regexp, $this->{$regexArgument->argument})
            );
    }
    /** @param string|int|bool|EnumRequestInterface|EnumInterface|\BackedEnum|non-empty-array<int|string, array<int|string, mixed>|string>|null $value */
    private function with(string $property, string|int|bool|object|array|null $value): void
    {
        if (strrpos($property, '\\') === false) {
            $this->arguments[$property] = $value;
        } else if (enum_exists($property)) {
            $argumentClazz = new \ReflectionClass($property);
            if (
                $argumentClazz->implementsInterface(\BackedEnum::class) &&
                $argumentClazz->implementsInterface(EnumInterface::class) &&
                $argumentClazz->implementsInterface(EnumRequestInterface::class)
            ) {
                /** @var interface-string<\BackedEnum & EnumInterface & EnumRequestInterface> $enumClass */
                $enumClass = $property;
                $this->arguments[$enumClass::getNameRequestVariable()] = $value;
            }
        } else throw new \ValueError('The with method cannot accept such a variable property key');
    }
}
