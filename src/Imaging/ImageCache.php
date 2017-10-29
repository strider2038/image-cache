<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Imaging;

use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Exception\InvalidConfigurationException;
use Strider2038\ImgCache\Exception\InvalidValueException;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Insertion\NullWriter;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory that contains image files
     * @var string
     */
    private $webDirectory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageWriterInterface */
    private $imageWriter;

    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        string $webDirectory,
        FileOperationsInterface $fileOperations,
        ImageProcessorInterface $imageProcessor,
        ImageExtractorInterface $imageExtractor,
        ImageWriterInterface $imageWriter = null
    ) {
        $this->fileOperations = $fileOperations;
        if (!$this->fileOperations->isDirectory($webDirectory)) {
            throw new InvalidConfigurationException("Directory '{$webDirectory}' does not exist");
        }
        $this->webDirectory = rtrim($webDirectory, '/');
        $this->imageProcessor = $imageProcessor;
        $this->imageExtractor = $imageExtractor;
        $this->imageWriter = $imageWriter ?? new NullWriter();
    }

    /**
     * First request for image file extracts image from the source and puts it into cache
     * directory. Next requests will be processed by nginx.
     * @param string $key
     * @return null|ImageFile
     */
    public function get(string $key): ? ImageFile
    {
        $this->validateKey($key);

        $image = $this->imageExtractor->extract($key);
        if ($image === null) {
            return null;
        }

        $filename = $this->composeDestinationFilename($key);
        $this->imageProcessor->saveToFile($image, $filename);

        return new ImageFile($filename);
    }

    public function put(string $key, StreamInterface $data): void
    {
        $this->validateKey($key);
        $this->imageWriter->insert($key, $data);
    }

    public function delete(string $key): void
    {
        $this->validateKey($key);
        $this->imageWriter->delete($key);

        $mask = $this->imageWriter->getFileMask($key);
        $cachedFiles = $this->fileOperations->findByMask($this->composeDestinationFilename($mask));
        foreach ($cachedFiles as $cachedFile) {
            $this->fileOperations->deleteFile($cachedFile);
        }
    }
    
    public function exists(string $key): bool
    {
        $this->validateKey($key);

        return $this->imageWriter->exists($key);
    }

    private function composeDestinationFilename(string $key): string
    {
        return $this->webDirectory . $key;
    }

    private function validateKey(string $key): void
    {
        if (strlen($key) <= 0 || $key[0] !== '/') {
            throw new InvalidValueException('Key must start with slash');
        }
    }
}
