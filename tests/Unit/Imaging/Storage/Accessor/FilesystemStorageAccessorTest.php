<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Accessor;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Accessor\FilesystemStorageAccessor;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class FilesystemStorageAccessorTest extends TestCase
{
    use ProviderTrait, LoggerTrait;

    private const FILENAME = 'test';

    /** @var FilesystemStorageDriverInterface */
    private $storageDriver;
    /** @var ImageFactoryInterface */
    private $imageFactory;
    /** @var StorageFilenameFactoryInterface */
    private $storageFilenameFactory;
    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->storageDriver = \Phake::mock(FilesystemStorageDriverInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->storageFilenameFactory = \Phake::mock(StorageFilenameFactoryInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function getImage_givenKeyAndSourceFileExists_imageIsReturned(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $storageFilename = $this->givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(self::FILENAME);
        $stream = $this->givenStorageDriver_getFileContents_returnsStream($storageFilename);
        $createdImage = $this->givenImageFactory_createImageFromStream_returnsImage();

        $image = $accessor->getImage(self::FILENAME);

        $this->assertSame($createdImage, $image);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
        $this->assertStorageFilenameFactory_createStorageFilename_isCalledOnceWithFilename(self::FILENAME);
        $this->assertStorageDriver_getFileContents_isCalledOnceWithFilenameKey($storageFilename);
        $this->assertImageFactory_createImageFromStream_isCalledOnceWithStream($stream);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function imageExists_givenKeyAndSourceFileExistStatus_boolIsReturned(bool $expectedExists): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $storageFilename = $this->givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(self::FILENAME);
        $this->givenStorageDriver_fileExists_returns($storageFilename, $expectedExists);

        $actualExists = $accessor->imageExists(self::FILENAME);

        $this->assertEquals($expectedExists, $actualExists);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function putImage_givenKeyAndStream_streamIsPuttedToSource(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $image = \Phake::mock(Image::class);
        $stream = $this->givenImage_getData_returnsStream($image);

        $storageFilename = $this->givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(self::FILENAME);

        $accessor->putImage(self::FILENAME, $image);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertStorageDriver_createFile_isCalledOnceWith($storageFilename, $stream);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function deleteImage_givenKey_sourceDeleteIsCalled(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $storageFilename = $this->givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(self::FILENAME);

        $accessor->deleteImage(self::FILENAME);

        $this->assertStorageDriver_deleteFile_isCalledOnceWith($storageFilename);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function deleteDirectoryContents_givenDirectory_directoryContentsDeletedByDriver(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $storageFilename = $this->givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(self::FILENAME);

        $accessor->deleteDirectoryContents(self::FILENAME);

        $this->assertStorageDriver_deleteDirectoryContents_isCalledOnceWith($storageFilename);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    private function createFilesystemStorageAccessor(): FilesystemStorageAccessor
    {
        $accessor = new FilesystemStorageAccessor($this->storageDriver, $this->imageFactory, $this->storageFilenameFactory);
        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenStorageFilenameFactory_createStorageFilename_returnsStorageFilename(string $filename): StorageFilenameInterface
    {
        $storageFilename = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($this->storageFilenameFactory)->createStorageFilename($filename)->thenReturn($storageFilename);

        return $storageFilename;
    }

    private function givenStorageDriver_getFileContents_returnsStream(StorageFilenameInterface $storageFilename): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->storageDriver)->getFileContents($storageFilename)->thenReturn($stream);

        return $stream;
    }

    private function givenStorageDriver_fileExists_returns(StorageFilenameInterface $storageFilename, bool $value): void
    {
        \Phake::when($this->storageDriver)
            ->fileExists($storageFilename)
            ->thenReturn($value);
    }

    private function assertStorageDriver_createFile_isCalledOnceWith(StorageFilenameInterface $storageFilename, StreamInterface $stream): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))
            ->createFile($storageFilename, $stream);
    }

    private function assertStorageDriver_deleteFile_isCalledOnceWith(StorageFilenameInterface $storageFilename): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))
            ->deleteFile($storageFilename);
    }

    private function assertStorageDriver_deleteDirectoryContents_isCalledOnceWith(StorageFilenameInterface $storageFilename): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))
            ->deleteDirectoryContents($storageFilename);
    }

    private function assertImage_getData_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getData();
    }

    private function givenImage_getData_returnsStream(Image $image): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($image)
            ->getData()
            ->thenReturn($stream);

        return $stream;
    }

    private function assertStorageFilenameFactory_createStorageFilename_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->storageFilenameFactory, \Phake::times(1))->createStorageFilename($filename);
    }

    private function assertStorageDriver_getFileContents_isCalledOnceWithFilenameKey(StorageFilenameInterface $storageFilename): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))->getFileContents($storageFilename);
    }

    private function assertImageFactory_createImageFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createImageFromStream($stream);
    }

    private function givenImageFactory_createImageFromStream_returnsImage(): Image
    {
        $createdImage = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createImageFromStream(\Phake::anyParameters())->thenReturn($createdImage);

        return $createdImage;
    }
}
