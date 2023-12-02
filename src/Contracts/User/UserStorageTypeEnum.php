<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\User;

use JsonSerializable;
use Microwin7\PHPUtils\Contracts\Enum\EnumTrait;
use Microwin7\PHPUtils\Contracts\Enum\EnumInterface;

enum UserStorageTypeEnum: int implements JsonSerializable, EnumInterface
{
    use EnumTrait;

    case USERNAME     = 0;
    case UUID         = 1;
    case DB_USER_ID   = 2;
    case DB_SHA1      = 3;
    case DB_SHA256    = 4;
}
