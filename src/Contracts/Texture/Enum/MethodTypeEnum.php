<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Texture\Enum;

use JsonSerializable;
use Microwin7\PHPUtils\Contracts\Enum\EnumTrait;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

enum MethodTypeEnum: int implements JsonSerializable, EnumInterface, EnumRequestInterface
{
    use EnumTrait;

    case NORMAL         = 0;
    case MOJANG         = 1;
    case HYBRID         = 2;

    public static function getDefault(): static
    {
        return MethodTypeEnum::NORMAL;
    }
    public static function getNameRequestVariable(): string
    {
        return 'method';
    }
    public static function getNameVariable(): string
    {
        return 'methodType';
    }
}
