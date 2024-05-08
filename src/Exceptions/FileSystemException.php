<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileSystemException extends \Exception
{
    function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
    public static function folderNotExist(): self
    {
        return new self("The folder does not exist or the script does not have read access", 404);
    }
    public static function createForbidden(): self
    {
        return new self("This folder cannot be created due to a permissions error", 403);
    }
    public function isErrorFolderNotExist(): bool
    {
        return $this->getCode() === 404;
    }
    public function isErrorCreateForbidden(): bool
    {
        return $this->getCode() === 403;
    }
}
