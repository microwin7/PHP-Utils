<?php

namespace Microwin7\PHPUtils\Response;

use Microwin7\PHPUtils\Exceptions\NoSuchRequestMethodException;

/**
 * @method \Microwin7\PHPUtils\Response\JsonResponse message(string $message)
 * @method \Microwin7\PHPUtils\Response\JsonResponse error(string $error)
 * @method \Microwin7\PHPUtils\Response\JsonResponse code(int $code)
 * @method \Microwin7\PHPUtils\Response\JsonResponse code_response(int $code_response)
 * @method \Microwin7\PHPUtils\Response\JsonResponse extra(array $array)
 * @method \Microwin7\PHPUtils\Response\JsonResponse success(?string $message = null, bool $need_success = false)
 * @method \Microwin7\PHPUtils\Response\JsonResponse failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400)
 * @method \Microwin7\PHPUtils\Response\JsonResponse json_encode(mixed $data = null)
 * @method \Microwin7\PHPUtils\Response\JsonResponse response(mixed $data = null)
 */
class JsonResponse
{
    private static array $data = [];

    /**
     * Create a new array data for response.
     *
     * @param array|null $data
     * @return void
     */
    public function __construct(?array $data = null)
    {
        null === $data ?: self::$data = $data;
    }
    /**
     * Undocumented function
     *
     * @param string  $method
     * @param array  $parameters
     * @return $this
     * 
     * @throws \Microwin7\PHPUtils\Exceptions\NoSuchRequestMethodException
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this, $method) && is_callable(self::class, $method) && !str_starts_with($method, '__')) {
            $this->$method(...$parameters);
            return $this;
        }
        throw new NoSuchRequestMethodException();
    }
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
    public static function code_response(int $code_response): void
    {
        http_response_code($code_response);
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
        !$need_success ?: self::$data['success'] = true;
        self::response();
    }
    public static function failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400): void
    {
        null === $message ?: self::message($message);
        null === $error ?: self::error($error);
        !$need_success ?: self::$data['success'] = false;
        0 === $code ?: self::code($code);
        self::code_response($code_response);
        self::response();
    }
    private static function header(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
    }
    public static function json_encode(mixed $data = null): string
    {
        return json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new \stdClass), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
    /**
     * Undocumented function
     *
     * @param mixed $data
     * @return never
     */
    public static function response(mixed $data = null)
    {
        self::header();
        die(self::json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new \stdClass)));
    }
}
