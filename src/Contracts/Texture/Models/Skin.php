<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Texture\Models;

use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

abstract class Skin implements \JsonSerializable
{
    public function __construct(
        /** @psalm-suppress PossiblyUnusedProperty */
        public readonly TextureStorageTypeEnum          $textureStorageType,
        public readonly string                          $data,
        public readonly string                          $url,
        public readonly bool                            $isSlim,
    ) {
    }
    abstract public static function urlComplete(TextureStorageTypeEnum $textureStorageType, string $url): string;
}
