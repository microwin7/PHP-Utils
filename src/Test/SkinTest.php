<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Test;

use Microwin7\PHPUtils\Utils\Texture;
use Microwin7\PHPUtils\Request\RequestParams;
use Microwin7\PHPUtils\Contracts\Texture\Models\Skin;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

final class SkinTest extends Skin
{
    public function jsonSerialize(): array
    {
        $json = [
            // 'url' => Texture::urlComplete($this->textureStorageType, $this->url),
            'digest' => Texture::digest($this->data),
        ];
        if ($this->isSlim) $json['metadata'] = ['model' => 'slim'];
        return $json;
    }
}
