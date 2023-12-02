<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\DB;

enum SubDBTypeEnum: string
{
    case MySQL      = 'mysql';
    case PostgreSQL = 'pgsql';
}
