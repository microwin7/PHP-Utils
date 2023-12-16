<?php

declare(strict_types=1);

namespace Microwin7\PHPUtils\Response;

use Microwin7\PHPUtils\Exceptions\NoSuchRequestMethodException;
use function Microwin7\PHPUtils\getClassMethodsFromAnnotations;
use function Microwin7\PHPUtils\getClassStaticMethodsFromAnnotations;

/**
 * @method static message(string $message)
 * @method static error(string $error)
 * @method static code(int $code)
 * @method static code_response(int $code_response)
 * @method static extra(array $array)
 * @method static success(?string $message = null, bool $need_success = false)
 * @method never failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400)
 * @method never response(mixed $data = null)
 * 
 * @method static void message(string $message)
 * @method static void error(string $error)
 * @method static void code(int $code)
 * @method static void code_response(int $code_response)
 * @method static void extra(array $array)
 * @method static void success(?string $message = null, bool $need_success = false)
 * @method static never failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400)
 * @method static string|false json_encode(string|array|object|null $data = null)
 * @method static never response(mixed $data = null)
 */
class JsonResponse
{
    /** @var array<int|string, mixed> */
    private static array $data = [];

    /**
     * Create a new array data for response.
     * @param array<int|string, mixed>|null $data
     * @return void
     */
    public function __construct(?array $data = null)
    {
        null === $data ?: self::$data = $data;
    }
    /**
     * @param string  $method
     * @param array  $parameters
     * @return static
     * 
     * @throws NoSuchRequestMethodException
     */
    public function __call(string $method, array $parameters): static
    {
        if (in_array($method, getClassMethodsFromAnnotations(static::class))) {
            static::$method(...$parameters);
            return $this;
        }
        throw new NoSuchRequestMethodException();
    }
    /**
     * @param string  $method
     * @param array  $parameters
     * @return string|false|void
     * 
     * @throws NoSuchRequestMethodException
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $classFound = false;
        foreach (getClassStaticMethodsFromAnnotations(static::class) as $reflectionMethod) {
            if ($method === $reflectionMethod->name) {
                $type = $reflectionMethod->type;
                if ($type === 'void' || $type === 'never') {
                    static::$method(...$parameters);
                    $classFound = true;
                    break;
                } else {
                    /** @var string|false $result */
                    $result = static::$method(...$parameters);
                    return $result;
                }
            }
        }
        if (!$classFound) throw new NoSuchRequestMethodException();
    }
    /** @psalm-suppress InvalidReturnType */
    protected static function message(string $message): void
    {
        self::$data['message'] = $message;
    }
    /** @psalm-suppress InvalidReturnType */
    protected static function error(string $error): void
    {
        self::$data['error'] = $error;
    }
    /** @psalm-suppress InvalidReturnType */
    protected static function code(int $code): void
    {
        self::$data['code'] = $code;
    }
    /** @psalm-suppress InvalidReturnType */
    protected static function code_response(int $code_response): void
    {
        http_response_code($code_response);
    }
    /**
     * @param array<int|string, mixed> $array
     * @psalm-suppress InvalidReturnType
     */
    protected static function extra(array $array): void
    {
        static::$data = array_merge(static::$data, $array);
    }
    /** @psalm-suppress InvalidReturnType */
    protected static function success(?string $message = null, bool $need_success = false): void
    {
        null === $message ?: self::message($message);
        !$need_success ?: self::$data['success'] = true;
        self::response();
    }
    protected static function failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 0, int $code_response = 400): never
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
    protected static function json_encode(mixed $data = null): string
    {
        return json_encode(null !== $data ? $data : (!empty(self::$data) ? self::$data : new \stdClass), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION)
            ?: self::json_encode(['error' => 'JSON encoding error']);
    }
    protected static function response(mixed $data = null): never
    {
        self::header();
        die(self::json_encode($data));
    }
}
