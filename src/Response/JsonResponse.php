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
 * 
 * @method static \Microwin7\PHPUtils\Response\JsonResponse message(string $message)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse error(string $error)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse code(int $code)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse code_response(int $code_response)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse extra(array $array)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse success(?string $message = null, bool $need_success = false)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse json_encode(mixed $data = null)
 * @method static \Microwin7\PHPUtils\Response\JsonResponse response(mixed $data = null)
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
     * @param string  $method
     * @param array  $parameters
     * @return $this
     * 
     * @throws NoSuchRequestMethodException
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, $this->getPublicMethodsWithoutMagic())) {
            static::$method(...$parameters);
            return $this;
        }
        throw new NoSuchRequestMethodException();
    }
    /**
     * @param string  $method
     * @param array  $parameters
     * @return void
     * 
     * @throws NoSuchRequestMethodException
     */
    public static function __callStatic($method, $parameters)
    {
        if (in_array($method, (new static)->getPublicMethodsWithoutMagic())) {
            static::$method(...$parameters);
        } else throw new NoSuchRequestMethodException();
    }
    /**
     * @return string[]
     *
     * @psalm-return array{0?: string,...}
     */
    private function getPublicMethodsWithoutMagic(): array
    {
        return (new class extends JsonResponse
        {
            /**
             * @return string[]
             *
             * @psalm-return array{0?: string,...}
             */
            function get($object): array
            {
                foreach ($arrayMethods = get_class_methods($object) as $k => $v) {
                    if (!empty($v) && str_starts_with($v, '__')) unset($arrayMethods[$k]);
                }
                return $arrayMethods;
            }
        })->get($this);
    }
    protected static function message(string $message): void
    {
        self::$data['message'] = $message;
    }
    protected static function error(string $error): void
    {
        self::$data['error'] = $error;
    }
    protected static function code(int $code): void
    {
        self::$data['code'] = $code;
    }
    protected static function code_response(int $code_response): void
    {
        http_response_code($code_response);
    }
    protected static function extra(array $array): void
    {
        foreach ($array as $k => $v) {
            self::$data[$k] = $v;
        }
    }
    protected static function success(?string $message = null, bool $need_success = false): void
    {
        null === $message ?: self::message($message);
        !$need_success ?: self::$data['success'] = true;
        self::response();
    }
    /**
     * @return never
     */
    protected static function failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400): void
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
    /**
     * @param string|array|object|null $data
     * @return string|false
     */
    protected static function json_encode(string|array|object|null $data = null): string|false
    {
        return json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new \stdClass), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
    /**
     * @param mixed $data
     *
     * @return never
     */
    protected static function response(mixed $data = null)
    {
        self::header();
        die(self::json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new \stdClass)) ?: self::json_encode(['error' => 'JSON encoding error']));
    }
}
