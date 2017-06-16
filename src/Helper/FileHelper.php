<?php

namespace Strider2038\ImgCache\Helper;

use Strider2038\ImgCache\Exception\ApplicationException;

class FileHelper
{

    public static function createDirectory(
        string $directory, 
        int $mode = 0775, 
        bool $recursive = true
    ): bool
    {
        if (is_dir($directory)) {
            return true;
        }
        $parentDirectory = dirname($directory);
        if ($recursive && !is_dir($parentDirectory) && $parentDirectory !== $directory) {
            static::createDirectory($parentDirectory, $mode, $recursive);
        }
        try {
            if (!mkdir($directory, $mode)) {
                return false;
            }
        } catch (\Exception $e) {
            if (!is_dir($directory)) {
                throw new ApplicationException("Cannot create directory '{$directory}'");
            }
        }
        try {
            return chmod($directory, $mode);
        } catch (\Exception $e) {
            throw new ApplicationException("Cannot change permissions for directory '{$directory}'");
        }
    }

}
