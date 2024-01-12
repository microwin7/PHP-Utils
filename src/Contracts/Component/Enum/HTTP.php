<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Contracts\Component\Enum;

enum HTTP
{
    case GET;
    case POST;
    case REQUEST;
    case JSON;
}
