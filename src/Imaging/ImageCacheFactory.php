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
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageCacheFactory implements ImageCacheFactoryInterface
{
    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var ImageProcessorInterface */
    private $imageProcessor;

    public function __construct(
        FileOperationsInterface $fileOperations,
        ImageProcessorInterface $imageProcessor
    ) {
        $this->fileOperations = $fileOperations;
        $this->imageProcessor = $imageProcessor;
    }

    public function createImageCacheWithRootDirectory(DirectoryNameInterface $rootDirectory): ImageCacheInterface
    {
        return new ImageCache(
            $rootDirectory,
            $this->fileOperations,
            $this->imageProcessor
        );
    }
}
