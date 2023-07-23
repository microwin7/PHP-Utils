<?php

namespace Microwin7\PHPUtils\Utils;

class Response
{
    private static array $data;

    public static function message(string $message)
    {
        self::$data['message'] = $message;
    }
    public static function extra(array $array)
    {
        foreach ($array as $k => $v) {
            self::$data[$k] = $v;
        }
    }
    public static function success(string $message = '')
    {
        if (!empty($message)) self::message($message);
        self::$data['success'] = true;
        self::response();
    }
    public static function failed(string $message = '')
    {
        if (!empty($message)) self::message($message);
        self::$data['success'] = false;
        self::response();
    }
    private static function header(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
    }
    public static function response()
    {
        self::header();
        die(json_encode(self::$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
    }
}
