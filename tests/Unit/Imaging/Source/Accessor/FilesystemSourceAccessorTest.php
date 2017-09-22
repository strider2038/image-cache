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
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\Source\Accessor\FilesystemSourceAccessor;
use Strider2038\ImgCache\Imaging\Source\FilesystemSourceInterface;
use Strider2038\ImgCache\Imaging\Source\Key\FilenameKeyInterface;
use Strider2038\ImgCache\Imaging\Source\Mapping\FilenameKeyMapperInterface;
use Strider2038\ImgCache\Tests\Support\Phake\ImageTrait;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;

class FilesystemSourceAccessorTest extends TestCase
{
    use ImageTrait, ProviderTrait, LoggerTrait;

    private const KEY = 'test';

    /** @var FilesystemSourceInterface */
    private $source;

    /** @var FilenameKeyMapperInterface */
    private $keyMapper;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->source = \Phake::mock(FilesystemSourceInterface::class);
        $this->keyMapper = \Phake::mock(FilenameKeyMapperInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function get_givenKeyAndSourceFileDoesNotExist_nullIsReturned(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $this->givenSource_get_returns($filenameKey, null);

        $image = $accessor->get(self::KEY);

        $this->assertNull($image);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function get_givenKeyAndSourceFileExists_imageIsReturned(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);
        $sourceImage = $this->givenImage();
        $this->givenSource_get_returns($filenameKey, $sourceImage);

        $image = $accessor->get(self::KEY);

        $this->assertInstanceOf(ImageInterface::class, $image);
        $this->assertSame($sourceImage, $image);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
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
        $this->givenSource_exists_returns($filenameKey, $expectedExists);

        $actualExists = $accessor->exists(self::KEY);

        $this->assertEquals($expectedExists, $actualExists);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function put_givenKeyAndStream_streamIsPuttedToSource(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $stream = \Phake::mock(StreamInterface::class);
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->put(self::KEY, $stream);

        $this->assertSource_put_isCalledOnceWith($filenameKey, $stream);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function delete_givenKey_sourceDeleteIsCalled(): void
    {
        $accessor = $this->createFilesystemSourceAccessor();
        $filenameKey = $this->givenKeyMapper_getKey_returnsFilenameKey(self::KEY);

        $accessor->delete(self::KEY);

        $this->assertSource_delete_isCalledOnceWith($filenameKey);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    private function createFilesystemSourceAccessor(): FilesystemSourceAccessor
    {
        $accessor = new FilesystemSourceAccessor($this->source, $this->keyMapper);
        $accessor->setLogger($this->logger);

        return $accessor;
    }

    private function givenKeyMapper_getKey_returnsFilenameKey($filename): FilenameKeyInterface
    {
        $filenameKey = \Phake::mock(FilenameKeyInterface::class);

        \Phake::when($this->keyMapper)->getKey($filename)->thenReturn($filenameKey);

        return $filenameKey;
    }

    private function givenSource_get_returns(FilenameKeyInterface $filenameKey, ?ImageInterface $image): void
    {
        \Phake::when($this->source)->get($filenameKey)->thenReturn($image);
    }

    private function givenSource_exists_returns(FilenameKeyInterface $filenameKey, bool $value): void
    {
        \Phake::when($this->source)->exists($filenameKey)->thenReturn($value);
    }

    private function assertSource_put_isCalledOnceWith(FilenameKeyInterface $filenameKey, StreamInterface $stream): void
    {
        \Phake::verify($this->source, \Phake::times(1))->put($filenameKey, $stream);
    }

    private function assertSource_delete_isCalledOnceWith(FilenameKeyInterface $filenameKey): void
    {
        \Phake::verify($this->source, \Phake::times(1))->delete($filenameKey);
    }
}
