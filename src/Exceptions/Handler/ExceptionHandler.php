<?php

namespace Microwin7\PHPUtils\Exceptions\Handler;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Response\JsonResponse;

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
        /**
         * Need Library Sentry, install: composer require sentry/sdk
         */
        //if (MainConfig::SENTRY_ENABLE) \Sentry\init(['dsn' => MainConfig::SENTRY_DSN]);

        /**
         * Sets a user-defined exception handler function
         */
        set_exception_handler(array($this, 'exception_handler'));
    }
    public function exception_handler(\Throwable $e)
    {
        /**
         * Example:
         */
        if ($e instanceof \Throwable) {
            /**
             * Need Library Sentry, install: composer require sentry/sdk
             */
            //if (MainConfig::SENTRY_ENABLE) \Sentry\captureException($e);
            JsonResponse::failed(error: $e->getMessage());
        }
    }
}
