<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Test;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Utils\Path;
use Microwin7\PHPUtils\Utils\Texture;
use Microwin7\PHPUtils\Test\RequestParamsTest;
use Microwin7\PHPUtils\Contracts\Texture\Models\Skin;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

final class SkinTest extends Skin
{
    /** @return array{url: string, digest: string, metadata?: array{model: 'slim'}} */
    public function jsonSerialize(): array
    {
        $json = [
            'url' => static::urlComplete(TextureStorageTypeEnum::COLLECTION, (string)RequestParamsTest::fromRequest()),
            'digest' => (Texture::LEGACY_DIGEST() ? Texture::digest_legacy($this->data) :Texture::digest($this->data)),
        ];
        if ($this->isSlim) $json['metadata'] = ['model' => 'slim'];
        return $json;
    }
    public static function urlComplete(TextureStorageTypeEnum $textureStorageType, string $url): string
    {
        return match ($textureStorageType) {
            TextureStorageTypeEnum::MOJANG => $url,
            default => Main::getApplicationURL() . Path::SCRIPT_PATH() . $url, // GET Params
        };
    }
}
