<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Storage\Driver;

use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FilesystemStorageDriverInterface
{
    public function getFileContents(StorageFilenameInterface $filename): StreamInterface;
    public function fileExists(StorageFilenameInterface $filename): bool;
    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void;
    public function deleteFile(StorageFilenameInterface $filename): void;
}
