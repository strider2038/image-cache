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
use Strider2038\ImgCache\Imaging\Storage\Data\FilenameKeyMapperInterface;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\FilesystemStorageDriverInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class FilesystemStorageAccessorTest extends TestCase
{
    use ProviderTrait, LoggerTrait;

    private const KEY = 'test';

    /** @var FilesystemStorageDriverInterface */
    private $storageDriver;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->storageDriver = \Phake::mock(FilesystemStorageDriverInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->keyMapper = \Phake::mock(FilenameKeyMapperInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function getImage_givenKeyAndSourceFileExists_imageIsReturned(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $stream = $this->givenStorageDriver_getFileContents_returnsStream($filenameKey);
        $createdImage = $this->givenImageFactory_createImageFromStream_returnsImage();

        $image = $accessor->getImage(self::KEY);

        $this->assertSame($createdImage, $image);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
        $this->assertKeyMapper_getKey_isCalledOnceWithKey(self::KEY);
        $this->assertStorageDriver_getFileContents_isCalledOnceWithFilenameKey($filenameKey);
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
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $this->givenStorageDriver_fileExists_returns($filenameKey, $expectedExists);

        $actualExists = $accessor->imageExists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function putImage_givenKeyAndStream_streamIsPuttedToSource(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $image = \Phake::mock(Image::class);
        $stream = $this->givenImage_getData_returnsStream($image);

        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->putImage(self::KEY, $image);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertStorageDriver_createFile_isCalledOnceWith($filenameKey, $stream);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function deleteImage_givenKey_sourceDeleteIsCalled(): void
    {
        $accessor = $this->createFilesystemStorageAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->deleteImage(self::KEY);

        $this->assertStorageDriver_deleteFile_isCalledOnceWith($filenameKey);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    private function createFilesystemStorageAccessor(): FilesystemStorageAccessor
    {
        $accessor = new FilesystemStorageAccessor($this->storageDriver, $this->imageFactory, $this->keyMapper);
        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenKeyMapper_getKey_returnsFilenameKey($filename): StorageFilenameInterface
    {
        $filenameKey = \Phake::mock(StorageFilenameInterface::class);

        \Phake::when($this->keyMapper)->getKey($filename)->thenReturn($filenameKey);

        return $filenameKey;
    }

    private function givenStorageDriver_getFileContents_returnsStream(StorageFilenameInterface $filenameKey): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->storageDriver)->getFileContents($filenameKey)->thenReturn($stream);

        return $stream;
    }

    private function givenStorageDriver_fileExists_returns(StorageFilenameInterface $filenameKey, bool $value): void
    {
        \Phake::when($this->storageDriver)->fileExists($filenameKey)->thenReturn($value);
    }

    private function assertStorageDriver_createFile_isCalledOnceWith(StorageFilenameInterface $filenameKey, StreamInterface $stream): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))->createFile($filenameKey, $stream);
    }

    private function assertStorageDriver_deleteFile_isCalledOnceWith(StorageFilenameInterface $filenameKey): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))->deleteFile($filenameKey);
    }

    private function assertImage_getData_isCalledOnce(Image $image): void
    {
        \Phake::verify($image, \Phake::times(1))->getData();
    }

    private function givenImage_getData_returnsStream(Image $image): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($image)->getData()->thenReturn($stream);

        return $stream;
    }

    private function assertKeyMapper_getKey_isCalledOnceWithKey(string $key): void
    {
        \Phake::verify($this->keyMapper, \Phake::times(1))->getKey($key);
    }

    private function assertStorageDriver_getFileContents_isCalledOnceWithFilenameKey(StorageFilenameInterface $filenameKey): void
    {
        \Phake::verify($this->storageDriver, \Phake::times(1))->getFileContents($filenameKey);
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
