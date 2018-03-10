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
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Enum\ImageProcessorTypeEnum;
use Strider2038\ImgCache\Imaging\Extraction\ThumbnailImageCreatorInterface;
use Strider2038\ImgCache\Imaging\FilesystemImageStorageFactory;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorage;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\DirectoryNameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverFactory;
use Strider2038\ImgCache\Utility\EntityValidatorInterface;

class FilesystemImageStorageFactoryTest extends TestCase
{
    private const STORAGE_DIRECTORY = 'storage_directory';
    private const CACHE_DIRECTORY = 'cache_directory';
    private const WEBDAV_DRIVER_URI = 'driver_uri';
    private const WEBDAV_OAUTH_TOKEN = 'oauth_token';

    /** @var FilesystemStorageDriverFactory */
    private $filesystemStorageDriverFactory;
    /** @var EntityValidatorInterface */
    private $validator;
    /** @var ImageFactoryInterface */
    private $imageFactory;
    /** @var ThumbnailImageCreatorInterface */
    private $thumbnailImageCreator;
    /** @var DirectoryNameFactoryInterface */
    private $directoryNameFactory;

    protected function setUp(): void
    {
        $this->filesystemStorageDriverFactory = \Phake::mock(FilesystemStorageDriverFactory::class);
        $this->validator = \Phake::mock(EntityValidatorInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->thumbnailImageCreator = \Phake::mock(ThumbnailImageCreatorInterface::class);
        $this->directoryNameFactory = \Phake::mock(DirectoryNameFactoryInterface::class);
    }

    /**
     * @test
     * @dataProvider ProcessorTypeProvider
     * @param string $processorType
     */
    public function createImageStorageForImageSource_givenFilesystemSource_filesystemImageStorageCreatedAndReturned(
        string $processorType
    ): void {
        $imageStorageFactory = $this->createFilesystemImageStorageFactory();
        $imageSource = $this->givenFilesystemImageSourceWithProcessorType($processorType);
        $this->givenDirectoryNameFactory_createDirectoryName_returnsDirectoryName();

        $imageStorage = $imageStorageFactory->createImageStorageForImageSource($imageSource);

        $this->assertInstanceOf(ImageStorage::class, $imageStorage);
        $this->assertFilesystemStorageDriverFactory_createFilesystemStorageDriver_isCalledOnce();
        $this->assertDirectoryNameFactory_createDirectoryName_isCalledOnceWithString(self::STORAGE_DIRECTORY);
    }

    public function ProcessorTypeProvider(): array
    {
        return [
            [ImageProcessorTypeEnum::COPY],
            [ImageProcessorTypeEnum::THUMBNAIL],
        ];
    }

    /** @test */
    public function createImageStorageForImageSource_givenWebDAVSource_webdavImageStorageCreatedAndReturned(): void
    {
        $imageStorageFactory = $this->createFilesystemImageStorageFactory();
        $imageSource = $this->givenWebDAVImageSource();
        $this->givenDirectoryNameFactory_createDirectoryName_returnsDirectoryName();

        $imageStorage = $imageStorageFactory->createImageStorageForImageSource($imageSource);

        $this->assertInstanceOf(ImageStorage::class, $imageStorage);
        $this->assertFilesystemStorageDriverFactory_createWebDAVStorageDriver_isCalledOnceWithUriAndToken(
            self::WEBDAV_DRIVER_URI,
            self::WEBDAV_OAUTH_TOKEN
        );
        $this->assertDirectoryNameFactory_createDirectoryName_isCalledOnceWithString(self::STORAGE_DIRECTORY);
    }

    private function createFilesystemImageStorageFactory(): FilesystemImageStorageFactory
    {
        return new FilesystemImageStorageFactory(
            $this->filesystemStorageDriverFactory,
            $this->validator,
            $this->imageFactory,
            $this->thumbnailImageCreator,
            $this->directoryNameFactory
        );
    }

    private function givenFilesystemImageSourceWithProcessorType(string $processorType): FilesystemImageSource
    {
        return new FilesystemImageSource(
           self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            $processorType
        );
    }

    private function givenWebDAVImageSource(): WebDAVImageSource
    {
        return new WebDAVImageSource(
           self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            ImageProcessorTypeEnum::COPY,
            self::WEBDAV_DRIVER_URI,
            self::WEBDAV_OAUTH_TOKEN
        );
    }

    private function assertFilesystemStorageDriverFactory_createFilesystemStorageDriver_isCalledOnce(): void
    {
        \Phake::verify($this->filesystemStorageDriverFactory, \Phake::times(1))
            ->createFilesystemStorageDriver();
    }

    private function assertFilesystemStorageDriverFactory_createWebDAVStorageDriver_isCalledOnceWithUriAndToken(
        string $uri,
        string $oauthToken
    ): void {
        \Phake::verify($this->filesystemStorageDriverFactory, \Phake::times(1))
            ->createWebDAVStorageDriver($uri, $oauthToken);
    }

    private function givenDirectoryNameFactory_createDirectoryName_returnsDirectoryName(): void
    {
        \Phake::when($this->directoryNameFactory)
            ->createDirectoryName(\Phake::anyParameters())
            ->thenReturn(\Phake::mock(DirectoryNameInterface::class));
    }

    private function assertDirectoryNameFactory_createDirectoryName_isCalledOnceWithString(string $directoryName): void
    {
        \Phake::verify($this->directoryNameFactory, \Phake::times(1))
            ->createDirectoryName($directoryName);
    }
}
