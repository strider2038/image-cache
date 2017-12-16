<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Core;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FileOperationsInterface
{
    public function isFile(string $filename): bool;
    public function isDirectory(string $directory): bool;
    public function findByMask(string $filenameMask): StringList;
    public function copyFileTo(string $source, string $destination): void;
    public function getFileContents(string $filename): string;
    public function createFile(string $filename, string $data): void;
    public function deleteFile(string $filename): void;
    public function createDirectory(string $directory, int $mode = 0775): void;
    public function openFile(string $filename, ResourceStreamModeEnum $mode): StreamInterface;
}
