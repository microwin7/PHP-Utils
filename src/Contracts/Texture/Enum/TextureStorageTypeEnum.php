<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Texture\Enum;

use JsonSerializable;
use Microwin7\PHPUtils\Contracts\Enum\EnumTrait;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;
use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

enum TextureStorageTypeEnum: int implements JsonSerializable, EnumInterface, EnumRequestInterface
{
    use EnumTrait;

    case STORAGE        = 0;
    case MOJANG         = 1;
    case COLLECTION     = 2;
    case DEFAULT        = 3;

    public static function getDefault(): static
    {
        return TextureStorageTypeEnum::DEFAULT;
    }
    public static function getNameRequestVariable(): string
    {
        return 'storage';
    }
    public static function getNameVariable(): string
    {
        return 'textureStorageType';
    }
    public function next(): TextureStorageTypeEnum
    {
        if ($this->value + 1 < count($this->cases()))
            return $this->from($this->value + 1);
        else return $this;
    }
}
