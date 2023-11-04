<?php

namespace Microwin7\PHPUtils\Exceptions\Handler;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Response\Response;

/**
 * Use:
 *     "autoload": {
 *         "psr-4": {
 *             "Microwin7\\PHPUtils\\Exceptions\\Handler\\": "src/Exceptions/Handler/",
 *         }
 *     },
 * 
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
        //set_exception_handler(array($this, 'exception_handler'));
    }
    public function exception_handler(\Throwable $exception)
    {
        /**
         * Example:
         */
        if ($exception instanceof \Throwable) {
            /**
             * Need Library Sentry, install: composer require sentry/sdk
             */
            //if (MainConfig::SENTRY_ENABLE) \Sentry\captureException($e);
            Response::failed(error: $exception->getMessage());
        }
    }
}
