<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Test;

use Microwin7\PHPUtils\Rules\Regex;
use Microwin7\PHPUtils\Utils\Texture;
use Microwin7\PHPUtils\Configs\PathConfig;
use Microwin7\PHPUtils\Test\RequestParamsTest;
use function Microwin7\PHPUtils\str_ends_with_slash;
use Microwin7\PHPUtils\Contracts\Texture\Models\Skin;
use Microwin7\PHPUtils\Contracts\Texture\Enum\MethodTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\ResponseTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

final class SkinTest extends Skin
{
    /** @return array{url: string, digest: string, metadata?: array{model: 'slim'}} */
    public function jsonSerialize(): array
    {
        $json = [
            'url' => static::urlComplete(TextureStorageTypeEnum::COLLECTION, (string)RequestParamsTest::fromRequest()),
            'digest' => Texture::digest($this->data),
        ];
        if ($this->isSlim) $json['metadata'] = ['model' => 'slim'];
        return $json;
    }
    public static function urlComplete(TextureStorageTypeEnum $textureStorageType, string $url): string
    {
        return match ($textureStorageType) {
            TextureStorageTypeEnum::MOJANG => $url,
            default => str_ends_with_slash(PathConfig::APP_URL) . 'Config::SCRIPT_URL' . $url, // GET Params
        };
    }
}
