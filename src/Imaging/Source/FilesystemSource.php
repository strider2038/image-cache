<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging\Source;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSource implements FilesystemSourceInterface
{
    private const CHUNK_SIZE = 8 * 1024 * 1024;

    /** @var string */
    private $baseDirectory;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FileOperationsInterface */
    private $fileOperations;
    
    public function __construct(
        string $baseDirectory,
        FileOperationsInterface $fileOperations,
        ImageFactoryInterface $imageFactory
    ) {
        $this->fileOperations = $fileOperations;

        $this->baseDirectory = rtrim($baseDirectory, '/');
        if (!$this->fileOperations->isDirectory($this->baseDirectory)) {
            throw new InvalidConfigurationException("Directory '{$this->baseDirectory}' does not exist");
        }
        $this->baseDirectory .= '/';

        $this->imageFactory = $imageFactory;
    }
    
    public function getBaseDirectory(): string
    {
        return $this->baseDirectory;
    }
    
    public function get(FilenameKeyInterface $key): ? Image
    {
        $sourceFilename = $this->composeSourceFilename($key);
        
        if (!$this->fileOperations->isFile($sourceFilename)) {
            return null;
        }

        return $this->imageFactory->createFromFile($sourceFilename);
    }

    public function exists(FilenameKeyInterface $key): bool
    {
        $sourceFilename = $this->composeSourceFilename($key);

        return $this->fileOperations->isFile($sourceFilename);
    }

    public function put(FilenameKeyInterface $key, StreamInterface $stream): void
    {
        $sourceFilename = $this->composeSourceFilename($key);
        $this->fileOperations->createDirectory(dirname($sourceFilename));

        $mode = new ResourceStreamModeEnum(ResourceStreamModeEnum::WRITE_AND_READ);
        $outputStream = $this->fileOperations->openFile($sourceFilename, $mode);
        while (!$stream->eof()) {
            $outputStream->write($stream->read(self::CHUNK_SIZE));
        }
    }

    public function delete(FilenameKeyInterface $key): void
    {
        $sourceFilename = $this->composeSourceFilename($key);
        $this->fileOperations->deleteFile($sourceFilename);
    }

    private function composeSourceFilename(FilenameKeyInterface $key): string
    {
        return $this->baseDirectory . $key->getValue();
    }
}
