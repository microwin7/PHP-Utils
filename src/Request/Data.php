<?php

namespace Microwin7\PHPUtils\Request;

class Data
{
    public static function getData()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
    public static function requiredUrl(): string
    {
        return $_SERVER['QUERY_STRING'];
    }
}
