<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Texture\Models;

use Microwin7\PHPUtils\Utils\Texture;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

abstract class Skin implements \JsonSerializable
{
    public readonly string $digest;

    public function __construct(
        /** @psalm-suppress PossiblyUnusedProperty */
        public readonly TextureStorageTypeEnum          $textureStorageType,
        public readonly string                          $data,
        public readonly string                          $url,
        public readonly bool                            $isSlim,
                        string|null                     $digest = null,
    ) {
        $this->digest = null !== $digest ? $digest : (Texture::LEGACY_DIGEST() ? Texture::digest_legacy($this->data) : Texture::digest($this->data));
    }
}
