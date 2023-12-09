<?php

namespace Microwin7\PHPUtils\Test;

use Microwin7\PHPUtils\Rules\Regex;
use Microwin7\PHPUtils\Attributes\AsArguments;
use Microwin7\PHPUtils\Attributes\RegexArguments;
use Microwin7\PHPUtils\Request\RequiredArguments;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

#[AsArguments(whereSearch: 'GET', required: [['username', TextureStorageTypeEnum::class]], optional: ['login'])]
#[RegexArguments('login', Regex::LOGIN)]
#[RegexArguments('username', Regex::USERNAME)]
#[RegexArguments('uuid', Regex::UUIDv1_AND_v4)]
class RequiredArgumentsTest extends RequiredArguments
{

}
