<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

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
        // $this->options = $options;
        return $this;
    }
    /**
     * @param interface-string<\BackedEnum & EnumInterface & EnumRequestInterface> $enumClass
     */
    protected function addEnum(string $enumClass, bool $maybeNull = false): static
    {
        if (array_key_exists($enumClass::getNameRequestVariable(), $this->options)) {

            $optionValueEnum = $this->options[$enumClass::getNameRequestVariable()];
            if (is_numeric($optionValueEnum))
                return $this->with($enumClass::getNameVariable(), $enumClass::from((int)$optionValueEnum));
            else
                return $this->with($enumClass::getNameVariable(), $enumClass::fromString($optionValueEnum));
        } elseif ($maybeNull) {
            return $this->with($enumClass::getNameVariable(), null);
        }
        return $this->with($enumClass::getNameVariable(), $enumClass::getDefault());
    }
    protected function addVariable(string $key, string $regexp, bool $maybeNull = false): static
    {
        return array_key_exists($key, $this->options)
            ? $this->with($key, $this->options[$key])->validateVariable($key, $regexp)
            : ($maybeNull ? $this->with($key, null) : throw new \ValueError('The key: "' . $key . '" value can only be string'));
    }
    /**
     * @param string|EnumRequestInterface|EnumInterface|\BackedEnum|null $value
     */
    protected function with(string $property, string|object|array|null $value, string|null $regexp = null): static
    {
        // $clazz = $this ?? new static();

        $this->arguments[$property] = $value;
        if ($value !== null && $regexp !== null) $this->validateVariable($property, $regexp);
        return $this;
    }
}
