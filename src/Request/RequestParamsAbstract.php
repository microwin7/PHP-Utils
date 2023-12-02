<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Request;

#[\AllowDynamicProperties]
abstract class RequestParamsAbstract implements RequestParamsInterface
{
    protected array $options;
    protected function validateVariable(string $key, string $regexp): static
    {
        null === $this->$key
            ?: filter_var($this->$key, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]])
            ?: throw new \ValueError(sprintf('Field "' . $key . '" should be valid with pattern: [' . $regexp . '], "%s" given', $this->$key));
        return $this;
    }
    protected function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }
    protected function addEnum($classEnum): static
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
    protected function with(string $property, mixed $value): static
    {
        $clazz = $this ?? new static();
        $clazz->$property = $value;
        return $clazz;
    }
}
