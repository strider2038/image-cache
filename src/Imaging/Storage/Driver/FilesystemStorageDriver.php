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
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\FileNotFoundException;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Imaging\Storage\Data\FilenameKeyInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemStorageDriver implements FilesystemStorageDriverInterface
{
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var string */
    private $baseDirectory;

    /** @var FileOperationsInterface */
    private $fileOperations;
    
    public function __construct(string $baseDirectory, FileOperationsInterface $fileOperations)
    {
        $this->fileOperations = $fileOperations;
        $this->baseDirectory = rtrim($baseDirectory, '/');

        if (!$this->fileOperations->isDirectory($this->baseDirectory)) {
            throw new InvalidConfigurationException(sprintf('Directory "%s" does not exist', $baseDirectory));
        }

        $this->baseDirectory .= '/';
    }
    
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    public function getFileContents(FilenameKeyInterface $key): StreamInterface
    {
        $sourceFilename = $this->composeSourceFilename($key);

        if (!$this->fileOperations->isFile($sourceFilename)) {
            throw new FileNotFoundException(sprintf('File "%s" not found', $sourceFilename));
        }

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::READ_ONLY);
        return $this->fileOperations->openFile($sourceFilename, $mode);
    }

    public function fileExists(FilenameKeyInterface $key): bool
    {
        $sourceFilename = $this->composeSourceFilename($key);

        return $this->fileOperations->isFile($sourceFilename);
    }

    public function createFile(FilenameKeyInterface $key, StreamInterface $data): void
    {
        $sourceFilename = $this->composeSourceFilename($key);
        $this->fileOperations->createDirectory(dirname($sourceFilename));

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::WRITE_AND_READ);
        $outputStream = $this->fileOperations->openFile($sourceFilename, $mode);
        while (!$data->eof()) {
            $outputStream->write($data->read(self::CHUNK_SIZE));
        }
    }

    public function deleteFile(FilenameKeyInterface $key): void
    {
        $sourceFilename = $this->composeSourceFilename($key);
        $this->fileOperations->deleteFile($sourceFilename);
    }

    private function composeSourceFilename(FilenameKeyInterface $key): string
    {
        return $this->baseDirectory . $key->getValue();
    }
}
