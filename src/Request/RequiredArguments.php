<?php

namespace Microwin7\PHPUtils\Request;

use Microwin7\PHPUtils\Attributes\AsArguments;
use Microwin7\PHPUtils\Attributes\RegexArguments;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

use function Microwin7\PHPUtils\implodeRecursive;

class RequiredArguments
{
    /** @var array<string, null|string|array<array-key, mixed>|\BackedEnum|EnumInterface|EnumRequestInterface> $arguments */
    private array $arguments = [];
    /** @var array<string|string[]> */
    private array $requiredArguments;
    /**  @var string[]|null */
    private ?array $optionalArguments;
    private array $where;

    private AsArguments $argumentsInstance;
    private ?array $regexArguments = null;

    public function __construct()
    {
        $this->setArgumentsInstance();
        $this->setRegexArguments();
        $this->setWhereSearch();
        $this->setRequiredArguments();
        $this->setOptionalArguments();
        $this->execute();
    }
    /**
     * @return BackedEnum|Microwin7\PHPUtils\Contracts\Enum\EnumInterface|Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface|array<array-key, mixed>|null|string
     */
    public function __get($name): string|array|null|object
    {
        return $this->arguments[$name];
    }
    private function setArgumentsInstance(): void
    {
        if ($attributes = (new \ReflectionClass(static::class))->getAttributes(AsArguments::class))
            $this->argumentsInstance = $attributes[0]->newInstance();
        else
            throw new RequiredArgumentMissingException('Attribute ' . AsArguments::class . ' missing');
    }
    private function setRegexArguments(): void
    {
        foreach ((new \ReflectionClass(static::class))->getAttributes(RegexArguments::class) as $attribute) {
            $instance = $attribute->newInstance();
            $this->regexArguments[$instance->argument] = $instance->regexp;
        }
    }
    private function setWhereSearch(): void
    {
        $this->where = match ($this->argumentsInstance->whereSearch) {
            'GET' => $_GET,
            'POST' => $_POST,
            'REQUEST' => $_REQUEST,
            'JSON' => Data::getData(),
        };
    }
    private function setRequiredArguments(): void
    {
        $this->requiredArguments = $this->argumentsInstance->required;
    }
    private function setOptionalArguments(): void
    {
        $this->optionalArguments = $this->argumentsInstance?->optional;
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
                        } catch (RequiredArgumentMissingException $ignored) {
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
    /**
     * @param string $argument
     * 
     * @return void
     */
    private function setVariable(string $argument, bool $optional = false): void
    {
        if (strrpos($argument, '\\') === false) {
            $this->with($argument, $this->where[$argument] ?? ($optional ? null : throw new RequiredArgumentMissingException($argument)));
            if (isset($this->regexArguments[$argument])) $this->validateVariable($argument, $this->regexArguments[$argument]);
        } else if (enum_exists($argument)) {
            $argumentClazz = new \ReflectionClass($argument);
            if (
                $argumentClazz->implementsInterface(\BackedEnum::class) &&
                $argumentClazz->implementsInterface(EnumInterface::class) &&
                $argumentClazz->implementsInterface(EnumRequestInterface::class)
            ) {
                /** @var \BackedEnum & EnumInterface & EnumRequestInterface $enumClass */
                $enumClass = $argument;
                try {
                    if (is_numeric($this->where[$enumClass::getNameRequestVariable()]))
                        $this->with($enumClass::getNameVariable(), $enumClass::from((int)$this->where[$enumClass::getNameRequestVariable()]));
                    else
                        $this->with($enumClass::getNameVariable(), $enumClass::fromString($this->where[$enumClass::getNameRequestVariable()]));
                } catch (\InvalidArgumentException $exception) {
                    if (!$optional) throw new \InvalidArgumentException($exception);
                    $this->with($enumClass::getNameVariable(), $enumClass::getDefault());
                }
            }
        }
    }
    protected function validateVariable(string $key, string $regexp): void
    {
        null === $this->$key
            ?: filter_var($this->$key, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]])
            ?: throw new \ValueError(sprintf('Field "%s" should be valid with pattern: [%s], "%s" given', $key, $regexp, (string)$this->$key));
    }
    private function with(string $property, mixed $value): void
    {
        $this->arguments[$property] = $value;
    }
}
