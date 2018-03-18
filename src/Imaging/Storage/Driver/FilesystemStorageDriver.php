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

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemStorageDriver implements FilesystemStorageDriverInterface
{
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var FileOperationsInterface */
    private $fileOperations;
    
    public function __construct(FileOperationsInterface $fileOperations)
    {
        $this->fileOperations = $fileOperations;
    }
    
    public function getFileContents(StorageFilenameInterface $filename): StreamInterface
    {
        if (!$this->fileOperations->isFile($filename)) {
            throw new FileNotFoundException(sprintf('File "%s" not found', $filename));
        }

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        return $this->fileOperations->openFile($filename, $mode);
    }

    public function fileExists(StorageFilenameInterface $filename): bool
    {
        return $this->fileOperations->isFile($filename);
    }

    public function createFile(StorageFilenameInterface $filename, StreamInterface $data): void
    {
        $this->fileOperations->createDirectory(\dirname($filename));

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::WRITE_AND_READ);
        $outputStream = $this->fileOperations->openFile($filename, $mode);
        while (!$data->eof()) {
            $outputStream->write($data->read(self::CHUNK_SIZE));
        }
    }

    public function deleteFile(StorageFilenameInterface $filename): void
    {
        $this->fileOperations->deleteFile($filename);
    }

    public function deleteDirectoryContents(StorageFilenameInterface $directory): void
    {
        $this->fileOperations->deleteDirectoryContents($directory);
    }
}
