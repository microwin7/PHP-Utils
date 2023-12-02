<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Test;

use Microwin7\PHPUtils\Rules\Regex;
use Microwin7\PHPUtils\Contracts\Texture\Enum\MethodTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\ResponseTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;
use Microwin7\PHPUtils\Request\RequestParamsAbstract;

final class RequestParamsTest extends RequestParamsAbstract
{
    public static function fromRequest(?self $requestParams = null): static
    {
        $requestParams ??= new static();
        return $requestParams->setOptions($_GET)
            ->addEnum(ResponseTypeEnum::class)
            ->addEnum(TextureStorageTypeEnum::class)
            ->addVariable('login', Regex::LOGIN)
            ->addVariable('username', Regex::USERNAME)
            ->addVariable('uuid', Regex::UUIDv1_AND_v4)
            ->addEnum(MethodTypeEnum::class);
    }
    public function __toString(): string
    {
        return '?' .
            'type=' . $this->responseType->name .
            (null === $this->textureStorageType ? '' : '&storage=' . $this->textureStorageType->name) .
            (null === $this->login ? '' : '&login=' . $this->login);
    }
}
