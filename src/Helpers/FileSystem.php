<?php

namespace Microwin7\PHPUtils\Helpers;

use UnexpectedValueException;
use Microwin7\PHPUtils\Exceptions\FileSystemException;

class FileSystem
{
    /**
     * @throws FileSystemException
     */
    public function findFile(string $directory, string $fileName, ?string $extension = null): string|null
    {
        if ($this->is_dir($directory)) return $this->recursiveSearchNameFileCaseInsensitive($directory, $fileName, $extension, 0);
        else throw FileSystemException::folderNotExist($directory);
    }
    /**
     * @return string[]
     * @throws FileSystemException
     */
    public function findFiles(string $directory, ?string $extension = null, int $level = 1): array
    {
        if ($this->is_dir($directory)) return $this->recursiveSearchFiles($directory, $extension, $level);
        else throw FileSystemException::folderNotExist($directory);
    }
    /** @param string|string[] $folders */
    public function findFolder(string|array $folders): bool
    {
        if (is_string($folders)) {
            if ($this->is_dir($folders)) return true;
        }
        if (is_array($folders)) {
            foreach ($folders as $directory) {
                if ($this->is_dir($directory)) return true;
            }
        }
        return false;
    }
    public function is_file(string $path): bool
    {
        try {
            $path = preg_replace("/\/+$/", "", $path);
            if (is_file($path)) {
                return true;
            }
        } catch (\Exception $e) {
            throw new FileSystemException("An unexpected error occurred while validating the file: " . $e->getMessage());
        }
        return false;
    }
    public function is_dir(string &$directory): bool
    {
        try {
            $directory = preg_replace("/\/+$/", "", $directory);
            if (is_dir($directory)) {
                return true;
            }
        } catch (\Exception $e) {
            throw new FileSystemException("An unexpected error occurred while validating the folder: " . $e->getMessage());
        }
        return false;
    }
    /** @throws FileSystemException */
    public static function mkdir(string $directory): void
    {
        mkdir($directory, 0755, true) ?: throw FileSystemException::createForbidden($directory);
    }
    public function recursiveSearchNameFileCaseInsensitive(string $directory, string $fileName, ?string $extension = null, int $level = -1): string|null
    {
        $extension ??= '';
        try {
            $directory = preg_replace("/\/+$/", "", $directory);
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            if ($level > -1) {
                $iterator->setMaxDepth($level);
            }
            /**
             * @var string $path
             * @var \SplFileInfo $obj
             */
            foreach ($iterator as $path => $obj) {
                if (!$obj->isDir()) {
                    $basename = pathinfo($path, PATHINFO_BASENAME);
                    if (strtolower($basename) === strtolower($fileName . $extension)) {
                        return mb_substr(
                            mb_stristr($basename, $fileName . $extension, false, mb_internal_encoding()),
                            0,
                            mb_strlen($fileName, mb_internal_encoding()),
                            mb_internal_encoding()
                        );
                    }
                }
            }
        } catch (UnexpectedValueException $e) {
            throw new FileSystemException("An unexpected error occurred while searching for files: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new FileSystemException($e->getMessage());
        }
        return null;
    }
    /** @return string[] */
    private function recursiveSearchFiles(string $directory, ?string $extension = null, int $level = -1): array
    {
        $extension ??= '';
        $extension = str_replace('.', '', $extension);
        /** @var string[] $filename */
        $filename = [];
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            if ($level > -1) {
                $iterator->setMaxDepth($level);
            }
            /**
             * @var string $path
             * @var \SplFileInfo $obj
             */
            foreach ($iterator as $path => $obj) {
                if (!$obj->isDir()) {
                    $filetype = pathinfo($path, PATHINFO_EXTENSION);
                    if (strtolower($filetype) === $extension) {
                        $filename[] = $path;
                    }
                }
            }
        } catch (UnexpectedValueException $e) {
            throw new FileSystemException("An unexpected error occurred while searching for files: " . $e->getMessage());
        } catch (\Exception $e) {
            throw new FileSystemException($e->getMessage());
        } finally {
            return $filename;
        }
    }
}
