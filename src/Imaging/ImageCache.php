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

use Strider2038\ImgCache\Exception\InvalidConfigException;
use Strider2038\ImgCache\Exception\NotAllowedException;
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Insertion\ImageWriterInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCache implements ImageCacheInterface
{
    /**
     * Web directory that contains image files
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var ImageExtractorInterface
     */
    private $imageExtractor;

    /**
     * @var ImageWriterInterface
     */
    private $imageWriter;

    /**
     * @var ImageFactoryInterface
     */
    private $imageFactory;
    
    public function __construct(
        string $cacheDirectory,
        ImageFactoryInterface $imageFactory,
        ImageExtractorInterface $imageExtractor,
        ImageWriterInterface $imageWriter = null
    ) {
        if (!is_dir($cacheDirectory)) {
            throw new InvalidConfigException("Directory '{$cacheDirectory}' does not exist");
        }
        $this->cacheDirectory = rtrim($cacheDirectory, '/') . '/';
        $this->imageFactory = $imageFactory;
        $this->imageExtractor = $imageExtractor;
        $this->imageWriter = $imageWriter;
    }

    /**
     * First request for image file extracts image from the source and puts it into cache
     * directory. Next requests will be processed by nginx.
     * @param string $key
     * @return null|ImageInterface
     */
    public function get(string $key): ?ImageInterface
    {
        /** @var ImageInterface $extractedImage */
        $extractedImage = $this->imageExtractor->extract($key);
        if ($extractedImage === null) {
            return null;
        }

        $destinationFilename = $this->composeDestinationFilename($key);

        $extractedImage->saveTo($destinationFilename);

        return $this->imageFactory->createImageFile($destinationFilename);
    }

    public function put(string $key, $data): void
    {
        if ($this->imageWriter === null) {
            throw new NotAllowedException(
                "Operation 'put' is not allowed for this type of cache"
            );
        }

        $this->imageWriter->insert($key, $data);
    }

    public function delete(string $key): void
    {
        if ($this->imageWriter === null) {
            throw new NotAllowedException(
                "Operation 'delete' is not allowed for this type of cache"
            );
        }

        $this->imageWriter->delete($key);

        unlink($this->cacheDirectory . $key);
        // @todo delete all thumbnails
    }
    
    public function exists(string $key): bool
    {
        return $this->imageExtractor->exists($key);
    }

    public function rebuild(string $key): void
    {
        // @todo cascade thumbnail rebuild
        $destinationFilename = $this->composeDestinationFilename($key);
        if (file_exists($destinationFilename)) {
            unlink($destinationFilename);
        }

        $this->get($key);
    }

    private function composeDestinationFilename(string $key): string
    {
        return $this->cacheDirectory . $key;
    }
}
