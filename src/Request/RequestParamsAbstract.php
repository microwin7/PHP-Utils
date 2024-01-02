<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

#[\AllowDynamicProperties]
abstract class RequestParamsAbstract implements RequestParamsInterface
{
    /** @var array<string, string|EnumRequestInterface|EnumInterface|\BackedEnum|null> $arguments */
    protected array $arguments = [];
    /** @var array<string, string> */
    protected array $options = [];
    /**
     * @return string|EnumRequestInterface|\BackedEnum|EnumInterface|null
     */
    public function __get(string $name): string|object|null
    {
        if (is_string($this->arguments[$name])) {
            return $this->arguments[$name];
        }
        if ($this->arguments[$name] !== null) {
            if (
                $this->arguments[$name] instanceof \BackedEnum &&
                $this->arguments[$name] instanceof EnumInterface &&
                $this->arguments[$name] instanceof EnumRequestInterface
            ) {
                return $this->arguments[$name];
            }
        }
        return $this->arguments[$name];
    }
    public function __set(string $name, string $value): void
    {
        $this->with($name, $value);
    }
    public function setVariable(string $name, string|null $value): static
    {
        return $this->with($name, $value);
    }
    /**
     * @param EnumRequestInterface $value
     */
    public function withEnum(object $value): static
    {
        return $this->with(null, $value);
    }
    /** Валидация только если ключ найден */
    protected function validateVariable(string $key, string $regexp): static
    {
        null === $this->arguments[$key]
            ?: filter_var($this->$key, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]])
            ?: throw new \ValueError(sprintf('Field "%s" should be valid with pattern: [%s], "%s" given', $key, $regexp, (string)$this->$key));
        return $this;
    }
    /**
     * @param array $options
     * @return static
     */
    protected function setOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                throw new \ValueError('The key: "' . $key . '" value can only be string');
            }
            $this->options[$key] = $value;
        }
        return $this;
    }
    /**
     * @param interface-string<\BackedEnum & EnumInterface & EnumRequestInterface> $enumClass
     * 
     * @throws RequiredArgumentMissingException
     * @throws \ValueError
     */
    protected function addEnum(string $enumClass, bool $maybeDefault = false): static
    {
        try {
            if (array_key_exists($enumClass::getNameRequestVariable(), $this->options)) {
                $optionValueEnum = $this->options[$enumClass::getNameRequestVariable()];
                if (empty($optionValueEnum)) throw new \ValueError;
                if (is_numeric($optionValueEnum))
                    return $this->with($enumClass::getNameVariable(), $enumClass::from((int)$optionValueEnum));
                else
                    return $this->with($enumClass::getNameVariable(), $enumClass::fromString($optionValueEnum));
            } else throw new \ValueError();
        } catch (\InvalidArgumentException | \ValueError $e) {
            if (!$maybeDefault) {
                if ($e instanceof \ValueError) throw new \ValueError('Requested parameter: "' . $enumClass::getNameRequestVariable() . '" value cannot be empty');
                throw new RequiredArgumentMissingException($enumClass::getNameRequestVariable());
            }
            return $this->with($enumClass::getNameVariable(), $enumClass::getDefault());
        }
    }
    /**
     * @throws \ValueError
     */
    protected function addVariable(string $key, string $regexp, bool $maybeNull = false): static
    {
        return array_key_exists($key, $this->options)
            ? $this->with($key, $this->options[$key])->validateVariable($key, $regexp)
            : ($maybeNull ? $this->with($key, null) : throw new \ValueError('The key: "' . $key . '" value can only be string'));
    }
    /**
     * @param string|interface-string<\BackedEnum & EnumInterface & EnumRequestInterface> $property
     * @param string|EnumRequestInterface|EnumInterface|\BackedEnum|null $value
     */
    protected function with(string|null $property, string|object|array|null $value, string|null $regexp = null): static
    {
        if ($property !== null && strrpos($property, '\\') === false) {
            $this->arguments[$property] = $value;
            if ($value !== null && $regexp !== null) $this->validateVariable($property, $regexp);
        } else if ($property !== null && enum_exists($property)) {
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
        } else if ($value instanceof EnumInterface) {
            $this->arguments[$value::getNameVariable()] = $value;
        } else throw new \ValueError('The with method cannot accept such a variable property key');
        return $this;
    }
}
