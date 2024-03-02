<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Texture\Enum;

use JsonSerializable;
use Microwin7\PHPUtils\Contracts\Enum\EnumTrait;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

enum ResponseTypeEnum: int implements JsonSerializable, EnumInterface, EnumRequestInterface
{
    use EnumTrait;

    case JSON               = 0;
    case SKIN               = 1;
    case CAPE               = 2;
    case AVATAR             = 3;
    case FRONT              = 4;
    case FRONT_CAPE         = 5;
    case FRONT_WITH_CAPE    = 6;
    case BACK               = 7;
    case BACK_CAPE          = 8;
    case BACK_WITH_CAPE     = 9;
    case CAPE_RESIZE        = 10;

    public static function getDefault(): static
    {
        return ResponseTypeEnum::JSON;
    }
    public static function getNameRequestVariable(): string
    {
        return 'type';
    }
    public static function getNameVariable(): string
    {
        return 'responseType';
    }
}
