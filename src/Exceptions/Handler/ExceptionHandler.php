<?php

namespace Microwin7\PHPUtils\Exceptions\Handler;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Response\JsonResponse;
use Microwin7\PHPUtils\Exceptions\FileUploadException;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureLoaderException;
use Microwin7\PHPUtils\Exceptions\ValidateBearerTokenException;
use Microwin7\PHPUtils\Exceptions\RegexArgumentsFailedException;
use Microwin7\PHPUtils\Exceptions\RequiredArgumentMissingException;

/**
 * Use:
 *     "autoload": {
 *         "psr-4": {
 *             "Microwin7\\PHPUtils\\Exceptions\\Handler\\": "src/Exceptions/Handler/",
 *         }
 *     },
 * Need AutoLoads or creater object
 *     "autoload": {
 *         "files": [
 *             "src/AutoLoads/InitExceptionHandler.php",
 *         ]
 *     },
 */
class ExceptionHandler
{
    public function __construct()
    {
        if (Main::SENTRY_ENABLE()) \Sentry\init(['dsn' => Main::SENTRY_DSN()]);
        /**
         * Sets a user-defined exception handler function
         */
        set_exception_handler(array($this, 'exception_handler'));
    }
    public function exception_handler(\Throwable $e): void
    {
        if ($e instanceof \InvalidArgumentException) {
            $this->error('InvalidArgumentException');
            // provided key/key-array is empty or malformed.
        }
        if ($e instanceof \DomainException) {
            if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
            $this->error('DomainException');
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
        }
        // if ($e instanceof \Firebase\JWT\SignatureInvalidException) {
        //     if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
        //     $this->error('Неправильная сигнатура публичного ключа');
        //     // provided JWT signature verification failed.
        // }
        // if ($e instanceof \Firebase\JWT\BeforeValidException) {
        //     if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
        //     $this->error('BeforeValidException');
        //     // provided JWT is trying to be used before "nbf" claim OR
        //     // provided JWT is trying to be used before "iat" claim.
        // }
        // if ($e instanceof \Firebase\JWT\ExpiredException) {
        //     $this->error('Токен авторизации истёк. Перезапустите лаунчер');
        //     // provided JWT is trying to be used after "exp" claim.
        // }
        if ($e instanceof \UnexpectedValueException) {
            if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
            $this->error('UnexpectedValueException');
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
        }
        if (
            $e instanceof ValidateBearerTokenException ||
            $e instanceof RequiredArgumentMissingException ||
            $e instanceof FileUploadException ||
            $e instanceof TextureSizeException ||
            $e instanceof TextureLoaderException ||
            $e instanceof RegexArgumentsFailedException ||
            $e instanceof \RuntimeException
        ) {
            $this->error($e);
        }
        if ($e instanceof \ErrorException) {
            if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
            $this->error('ErrorException');
        }
        if ($e instanceof \Throwable) {
            if (Main::SENTRY_ENABLE()) \Sentry\captureException($e);
            $this->error($e);
        }
    }
    private function error(\Throwable|string $error): never
    {
        if ($error instanceof \Throwable) {
            JsonResponse::failed(error: $error->getMessage());
        }
        JsonResponse::failed(error: $error);
    }
}
