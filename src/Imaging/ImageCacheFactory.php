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
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactoryInterface;
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
    /** @var DirectoryNameFactoryInterface */
    private $directoryNameFactory;
    /** @var string */
    private $rootDirectory;

    public function __construct(
        FileOperationsInterface $fileOperations,
        ImageProcessorInterface $imageProcessor,
        DirectoryNameFactoryInterface $directoryNameFactory,
        string $rootDirectory
    ) {
        $this->fileOperations = $fileOperations;
        $this->imageProcessor = $imageProcessor;
        $this->directoryNameFactory = $directoryNameFactory;
        $this->rootDirectory = $rootDirectory;
    }

    public function createImageCacheForWebDirectory(string $webDirectory): ImageCacheInterface
    {
        $absoluteDirectoryName = $this->createAbsoluteDirectoryName($webDirectory);

        return new ImageCache(
            $absoluteDirectoryName,
            $this->fileOperations,
            $this->imageProcessor
        );
    }

    private function createAbsoluteDirectoryName(string $webDirectory): DirectoryNameInterface
    {
        $directoryName = $this->rootDirectory . $webDirectory;

        return $this->directoryNameFactory->createDirectoryName($directoryName);
    }
}
