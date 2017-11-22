<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Accessor;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\FilesystemSourceAccessor;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class FilesystemSourceAccessorTest extends TestCase
{
    use ProviderTrait, LoggerTrait;

    private const KEY = 'test';

    /** @var FilesystemSourceInterface */
    private $source;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->source = \Phake::mock(FilesystemSourceInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->keyMapper = \Phake::mock(FilenameKeyMapperInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function get_givenKeyAndSourceFileExists_imageIsReturned(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $stream = $this->givenSource_getFileContents_returnsStream($filenameKey);
        $createdImage = $this->givenImageFactory_createFromStream_returnsImage();

        $image = $accessor->getImage(self::KEY);

        $this->assertSame($createdImage, $image);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
        $this->assertKeyMapper_getKey_isCalledOnceWithKey(self::KEY);
        $this->assertSource_getFileContents_isCalledOnceWithFilenameKey($filenameKey);
        $this->assertImageFactory_createFromStream_isCalledOnceWithStream($stream);
    }

    /**
     * @test
     * @param bool $expectedExists
     * @dataProvider boolValuesProvider
     */
    public function exists_givenKeyAndSourceFileExistStatus_boolIsReturned(bool $expectedExists): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $this->givenSource_fileExists_returns($filenameKey, $expectedExists);

        $actualExists = $accessor->imageExists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function put_givenKeyAndStream_streamIsPuttedToSource(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $image = \Phake::mock(Image::class);
        $stream = $this->givenImage_getData_returnsStream($image);

        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->putImage(self::KEY, $image);

        $this->assertImage_getData_isCalledOnce($image);
        $this->assertSource_createFile_isCalledOnceWith($filenameKey, $stream);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function delete_givenKey_sourceDeleteIsCalled(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->deleteImage(self::KEY);

        $this->assertSource_deleteFile_isCalledOnceWith($filenameKey);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    private function createFilesystemSourceAccessor(): FilesystemSourceAccessor
    {
        $accessor = new FilesystemSourceAccessor($this->source, $this->imageFactory, $this->keyMapper);
        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenKeyMapper_getKey_returnsFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($this->keyMapper)->getKey($filename)->thenReturn($filenameKey);

        return $filenameKey;
    }

    private function givenSource_getFileContents_returnsStream(FilenameKeyInterface $filenameKey): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->source)->getFileContents($filenameKey)->thenReturn($stream);

        return $stream;
    }

    private function givenSource_fileExists_returns(FilenameKeyInterface $filenameKey, bool $value): void
    {
        \Phake::when($this->source)->fileExists($filenameKey)->thenReturn($value);
    }

    private function assertSource_createFile_isCalledOnceWith(FilenameKeyInterface $filenameKey, StreamInterface $stream): void
    {
        \Phake::verify($this->source, \Phake::times(1))->createFile($filenameKey, $stream);
    }

    private function assertSource_deleteFile_isCalledOnceWith(FilenameKeyInterface $filenameKey): void
    {
        \Phake::verify($this->source, \Phake::times(1))->deleteFile($filenameKey);
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

    private function assertSource_getFileContents_isCalledOnceWithFilenameKey(FilenameKeyInterface $filenameKey): void
    {
        \Phake::verify($this->source, \Phake::times(1))->getFileContents($filenameKey);
    }

    private function assertImageFactory_createFromStream_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromStream($stream);
    }

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $createdImage = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($createdImage);

        return $createdImage;
    }
}
