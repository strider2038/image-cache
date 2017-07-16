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

use Strider2038\ImgCache\Imaging\Extraction\{
    ExtractedImageInterface,
    ImageExtractorInterface
};
use Strider2038\ImgCache\Exception\{
    InvalidConfigException,
    ApplicationException
};

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
    
    public function __construct(
        string $cacheDirectory,    
        ImageExtractorInterface $imageExtractor
    ) {
        if (!is_dir($cacheDirectory)) {
            throw new InvalidConfigException("Directory '{$cacheDirectory}' does not exist");
        }
        $this->cacheDirectory = rtrim($cacheDirectory, '/');
        $this->imageExtractor = $imageExtractor;
    }

    /**
     * First request for image file extracts image from the source and puts it into cache
     * directory. Next requests will be processed by nginx.
     * @param string $key
     * @return null|Image
     */
    public function get(string $key): ?Image
    {
        /** @var ExtractedImageInterface $extractedImage */
        $extractedImage = $this->imageExtractor->extract($key);
        if ($extractedImage === null) {
            return null;
        }

        $destinationFilename = $this->cacheDirectory . $key;

        $extractedImage->saveTo($destinationFilename);

        return new Image($destinationFilename);
    }

    public function put(string $key, $data): void
    {
        
    }
    
    public function delete(string $key): void
    {
        
    }
    
    public function exists(string $key): bool
    {
        
    }

    public function rebuild(string $key): void
    {

    }
}
