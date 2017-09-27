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
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;
use Strider2038\ImgCache\Imaging\Insertion\NullWriter;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory that contains image files
     * @var string
     */
    private $baseDirectory;

    /** @var FileOperationsInterface */
    private $fileOperations;

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    /** @var ImageWriterInterface */
    private $imageWriter;

    /** @var ImageFactoryInterface */
    private $imageFactory;
    
    public function __construct(
        string $baseDirectory,
        FileOperationsInterface $fileOperations,
        ImageFactoryInterface $imageFactory,
        ImageExtractorInterface $imageExtractor,
        ImageWriterInterface $imageWriter = null
    ) {
        $this->fileOperations = $fileOperations;
        if (!$this->fileOperations->isDirectory($baseDirectory)) {
            throw new InvalidConfigurationException("Directory '{$baseDirectory}' does not exist");
        }
        $this->baseDirectory = rtrim($baseDirectory, '/');
        $this->imageFactory = $imageFactory;
        $this->imageExtractor = $imageExtractor;
        $this->imageWriter = $imageWriter ?? new NullWriter();
    }

    /**
     * First request for image file extracts image from the source and puts it into cache
     * directory. Next requests will be processed by nginx.
     * @param string $key
     * @return null|ImageInterface
     */
    public function get(string $key): ? ImageInterface
    {
        $this->validateKey($key);

        /** @var ImageInterface $extractedImage */
        $extractedImage = $this->imageExtractor->extract($key);
        if ($extractedImage === null) {
            return null;
        }

        $destinationFilename = $this->composeDestinationFilename($key);
        $extractedImage->saveTo($destinationFilename);

        return $this->imageFactory->createImageFile($destinationFilename);
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

        $filename = $this->baseDirectory . $key;
        if ($this->fileOperations->isFile($filename)) {
            $this->fileOperations->deleteFile($filename);
        }
        // @todo delete all thumbnails
    }
    
    public function exists(string $key): bool
    {
        $this->validateKey($key);

        return $this->imageWriter->exists($key);
    }

    public function rebuild(string $key): void
    {
        $this->validateKey($key);

        // @todo cascade thumbnail rebuild
        $destinationFilename = $this->composeDestinationFilename($key);
        if ($this->fileOperations->isFile($destinationFilename)) {
            $this->fileOperations->deleteFile($destinationFilename);
        }

        $this->get($key);
    }

    private function composeDestinationFilename(string $key): string
    {
        return $this->baseDirectory . $key;
    }

    private function validateKey(string $key): void
    {
        if (@$key[0] !== '/') {
            throw new InvalidValueException('Key must start with slash');
        }
    }
}
