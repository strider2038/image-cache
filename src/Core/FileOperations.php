<?php

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Exception\FileOperationException;

class FileOperations
{

    public function isFile(string $filename): bool
    {
        return file_exists($filename) && !is_dir($filename);
    }

    public function isDirectory(string $directory): bool
    {
        return is_dir($directory);
    }

    public function copyFileTo(string $source, string $destination): void
    {
        try {
            if (!copy($source, $destination)) {
                throw new FileOperationException("Cannot copy file from '{$source}' to '{$destination}'");
            }
        } catch (\Exception $exception) {
            throw new FileOperationException("Cannot copy file from '{$source}' to '{$destination}'", $exception);
        }
    }

    public function getFileContents(string $filename): string
    {
        try {
            $contents = file_get_contents($filename);

            if ($contents === false) {
                throw new FileOperationException("Cannot read file '{$filename}'");
            }
        } catch (\Exception $exception) {
            throw new FileOperationException("Cannot read file '{$filename}'", $exception);
        }

        return $contents;
    }

    public function createFile(string $filename, string $data): void
    {
        try {
            if (!file_put_contents($filename, $data)) {
                throw new FileOperationException("Cannot create file '{$filename}'");
            }
        } catch (\Exception $exception) {
            throw new FileOperationException("Cannot create file '{$filename}'", $exception);
        }
    }

    public function deleteFile(string $filename): void
    {
        try {
            if (!unlink($filename)) {
                throw new FileOperationException("Cannot delete file '{$filename}'");
            }
        } catch (\Exception $exception) {
            throw new FileOperationException("Cannot delete file '{$filename}'", $exception);
        }
    }

    public function createDirectory(
        string $directory, 
        int $mode = 0775, 
        bool $recursive = true
    ): void {
        if (is_dir($directory)) {
            return;
        }

        $parentDirectory = dirname($directory);
        if ($recursive && !is_dir($parentDirectory) && $parentDirectory !== $directory) {
            $this->createDirectory($parentDirectory, $mode, $recursive);
        }

        try {
            if (!mkdir($directory, $mode)) {
                throw new FileOperationException("Cannot create directory '{$directory}'");
            }
        } catch (\Exception $previous) {
            if (!is_dir($directory)) {
                throw new FileOperationException("Cannot create directory '{$directory}'", $previous);
            }
        }

        try {
            if (!chmod($directory, $mode)) {
                throw new FileOperationException("Cannot change permissions for directory '{$directory}'");
            }
        } catch (\Exception $previous) {
            throw new FileOperationException("Cannot change permissions for directory '{$directory}'", $previous);
        }
    }


}
