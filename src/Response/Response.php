<?php

namespace Microwin7\PHPUtils\Response;

use stdClass;

class Response
{
    private static array $data = [];

    public static function message(string $message): void
    {
        self::$data['message'] = $message;
    }
    public static function error(string $error): void
    {
        self::$data['error'] = $error;
    }
    public static function code(int $code): void
    {
        self::$data['code'] = $code;
    }
    public function code_response(int $code): void
    {
        http_response_code($code);
    }
    public static function extra(array $array)
    {
        foreach ($array as $k => $v) {
            self::$data[$k] = $v;
        }
    }
    public static function success(?string $message = null, bool $need_success = false): void
    {
        null === $message ?: self::message($message);
        $need_success ?: self::$data['success'] = true;
        self::response();
    }
    public static function failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 400): void
    {
        null === $message ?: self::message($message);
        null === $error ?: self::error($error);
        $need_success ?: self::$data['success'] = false;
        self::code_response($code);
        self::response();
    }
    private static function header(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
    }
    public static function json_encode(mixed $data = null): string
    {
        return json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new stdClass), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
    public static function response(mixed $data = null)
    {
        self::header();
        die(self::json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new stdClass)));
    }
}
