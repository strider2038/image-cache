<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\FileOperationsInterface;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Imaging\ImageCacheFactory;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

class ImageCacheFactoryTest extends TestCase
{
    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var ImageProcessorInterface */
    private $imageProcessor;

    protected function setUp(): void
    {
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
    }

    /** @test */
    public function createImageCacheWithRootDirectory_givenRootDirectory_imageCacheCreatedAndReturned(): void
    {
        $factory = new ImageCacheFactory(
            $this->fileOperations,
            $this->imageProcessor
        );
        $rootDirectory = \Phake::mock(DirectoryNameInterface::class);

        $imageCache = $factory->createImageCacheWithRootDirectory($rootDirectory);

        $this->assertInstanceOf(ImageCache::class, $imageCache);
    }
}
