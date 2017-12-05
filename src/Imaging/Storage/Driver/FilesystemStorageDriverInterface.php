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

use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
interface FilesystemStorageDriverInterface
{
    public function getFileContents(StorageFilenameInterface $key): StreamInterface;
    public function fileExists(StorageFilenameInterface $key): bool;
    public function createFile(StorageFilenameInterface $key, StreamInterface $data): void;
    public function deleteFile(StorageFilenameInterface $key): void;
}
