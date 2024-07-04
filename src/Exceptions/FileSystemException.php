<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileSystemException extends \Exception
{
    function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
    public static function folderNotExist(string $directory): self
    {
        return new self(sprintf("This folder: \"%s\", does not exist or the script does not have read access", $directory), 404);
    }
    public static function createForbidden(string $directory): self
    {
        return new self(sprintf("This folder: \"%s\", cannot be created due to a permissions error", $directory), 403);
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
