<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

#[\AllowDynamicProperties]
abstract class RequestParamsAbstract implements RequestParamsInterface
{
    protected array $options;
    /** Валидация только если ключ найден */
    protected function validateVariable(string $key, string $regexp): static
    {
        null === $this->$key
            ?: filter_var($this->$key, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]])
            ?: throw new \ValueError(sprintf('Field "%s" should be valid with pattern: [%s], "%s" given', $key, $regexp, (string)$this->$key));
        return $this;
    }
    protected function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }
    /**
     * @param class-string<BackedEnum & EnumInterface & EnumRequestInterface> $classEnum
     */
    protected function addEnum(string $classEnum): static
    {
        if (array_key_exists($classEnum::getNameRequestVariable(), $this->options)) {
            if (is_numeric($this->options[$classEnum::getNameRequestVariable()]))
                return $this->with($classEnum::getNameVariable(), $classEnum::from((int)$this->options[$classEnum::getNameRequestVariable()]));
            else
                return $this->with($classEnum::getNameVariable(), $classEnum::fromString($this->options[$classEnum::getNameRequestVariable()]));
        }
        return $this->with($classEnum::getNameVariable(), $classEnum::getDefault());
    }
    protected function addVariable(string $key, string $regexp): static
    {
        return array_key_exists($key, $this->options)
            ? $this->with($key, $this->options[$key])->validateVariable($key, $regexp)
            : $this->with($key, null);
    }
    /**
     * @param EnumRequestInterface|string|null $value
     */
    protected function with(string $property, EnumRequestInterface|string|null $value): static
    {
        $clazz = $this ?? new static();
        $clazz->$property = $value;
        return $clazz;
    }
}
