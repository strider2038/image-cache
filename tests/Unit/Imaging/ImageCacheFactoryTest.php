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
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Processing\ImageProcessorInterface;

class ImageCacheFactoryTest extends TestCase
{
    private const WEB_DIRECTORY = '/web_directory/';

    /** @var FileOperationsInterface */
    private $fileOperations;
    /** @var ImageProcessorInterface */
    private $imageProcessor;
    /** @var DirectoryNameFactoryInterface */
    private $directoryNameFactory;

    protected function setUp(): void
    {
        $this->fileOperations = \Phake::mock(FileOperationsInterface::class);
        $this->imageProcessor = \Phake::mock(ImageProcessorInterface::class);
        $this->directoryNameFactory = \Phake::mock(DirectoryNameFactoryInterface::class);
    }

    /** @test */
    public function createImageCacheWithRootDirectory_givenRootDirectory_imageCacheCreatedAndReturned(): void
    {
        $rootDirectoryName = '/root_directory';
        $factory = $this->createImageCacheFactoryWithRootDirectory($rootDirectoryName);
        $this->givenDirectoryNameFactory_createDirectoryName_returnsDirectoryName();

        $imageCache = $factory->createImageCacheForWebDirectory(self::WEB_DIRECTORY);

        $this->assertInstanceOf(ImageCache::class, $imageCache);
        $directoryName = $rootDirectoryName . self::WEB_DIRECTORY;
        $this->assertDirectoryNameFactory_createDirectoryName_isCalledOnceWithString($directoryName);
    }

    private function createImageCacheFactoryWithRootDirectory($rootDirectoryName): ImageCacheFactory
    {
        return new ImageCacheFactory(
            $this->fileOperations,
            $this->imageProcessor,
            $this->directoryNameFactory,
            $rootDirectoryName
        );
    }

    private function assertDirectoryNameFactory_createDirectoryName_isCalledOnceWithString(string $directoryName): void
    {
        \Phake::verify($this->directoryNameFactory, \Phake::times(1))
            ->createDirectoryName($directoryName);
    }

    private function givenDirectoryNameFactory_createDirectoryName_returnsDirectoryName(): void
    {
        \Phake::when($this->directoryNameFactory)
            ->createDirectoryName(\Phake::anyParameters())
            ->thenReturn(\Phake::mock(DirectoryNameInterface::class));
    }
}
