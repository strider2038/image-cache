<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexDiskStorageDriver;
use Yandex\Disk\DiskClient;
use Yandex\Disk\Exception\DiskRequestException;

class YandexDiskStorageDriverTest extends TestCase
{
    private const BASE_DIRECTORY = 'base_directory';
    private const FILENAME = 'filename.jpg';
    private const FILENAME_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME;
    private const RESOURCE = 'resource';

    /** @var DiskClient */
    private $diskClient;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    protected function setUp(): void
    {
        $this->diskClient = \Phake::mock(DiskClient::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
    }

    /** @test */
    public function getFileContents_givenFilenameKeyAndFileExists_streamReturned(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $responseStream = $this->givenDiskClient_getFile_returnsArrayWithStream();
        $this->givenStream_detach_returnsResource($responseStream, self::RESOURCE);
        $expectedStream = $this->givenStreamFactory_createStreamFromResource_returnsStream();

        $stream = $driver->getFileContents($key);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertDiskClient_getFile_isCalledOnceWithFilename(self::FILENAME_FULL);
        $this->assertStream_detach_isCalledOnce($responseStream);
        $this->assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(self::RESOURCE);
        $this->assertSame($expectedStream, $stream);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found/
     */
    public function getFileContents_givenFilenameKeyAndFileNotExists_exceptionThrown(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $this->givenDiskClient_getFile_throwsException(new DiskRequestException('', 404));

        $driver->getFileContents($key);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Bad api response
     */
    public function getFileContents_givenFilenameKeyAndInvalidResponse_exceptionThrown(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $this->givenDiskClient_getFile_throwsException(new DiskRequestException());

        $driver->getFileContents($key);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Api response has empty body
     */
    public function getFileContents_givenFilenameKeyAndResponseHasEmptyBody_exceptionThrown(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $responseStream = $this->givenDiskClient_getFile_returnsArrayWithStream();
        $this->givenStream_detach_returnsNull($responseStream);

        $driver->getFileContents($key);
    }

    /**
     * @test
     * @dataProvider directoryContentsProvider
     * @param array $givenResponseArray
     * @param bool $expectedFileExists
     */
    public function fileExists_givenFilenameKeyAndResponseHasArrayWithFilename_expectedBoolReturned(
        array $givenResponseArray,
        bool $expectedFileExists
    ): void {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $this->givenDiskClient_directoryContents_returnsArray($givenResponseArray);

        $fileExists = $driver->fileExists($key);

        $this->assertDiskClient_directoryContents_isCalledOnceWithFilename(self::FILENAME_FULL);
        $this->assertEquals($expectedFileExists, $fileExists);
    }
    public function directoryContentsProvider(): array
    {
        return [
            [
                [],
                false,
            ],
            [
                [[]],
                false,
            ],
            [
                [['href' => self::FILENAME_FULL]],
                true,
            ],
        ];
    }

    /** @test */
    public function fileExists_givenFilenameKeyAndResponseThrowsExceptionWithCode404_falseReturned(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $this->givenDiskClient_directoryContents_throwsException(
            new DiskRequestException('Not found', HttpStatusCodeEnum::NOT_FOUND)
        );

        $fileExists = $driver->fileExists($key);

        $this->assertDiskClient_directoryContents_isCalledOnceWithFilename(self::FILENAME_FULL);
        $this->assertFalse($fileExists);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Bad api response
     */
    public function fileExists_givenFilenameKeyAndResponseThrowsExceptionWithNot404Code_exceptionThrown(): void
    {
        $driver = $this->createYandexDiskStorageDriver();
        $key = $this->givenFilenameKey();
        $this->givenDiskClient_directoryContents_throwsException(
            new DiskRequestException('Not found', HttpStatusCodeEnum::FORBIDDEN)
        );

        $driver->fileExists($key);
    }

    private function createYandexDiskStorageDriver(): YandexDiskStorageDriver
    {
        return new YandexDiskStorageDriver(self::BASE_DIRECTORY, $this->diskClient, $this->streamFactory);
    }

    private function givenFilenameKey(): StorageFilenameInterface
    {
        $key = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($key)->getValue()->thenReturn(self::FILENAME);

        return $key;
    }

    private function assertDiskClient_getFile_isCalledOnceWithFilename(string $storageFilename): void
    {
        \Phake::verify($this->diskClient, \Phake::times(1))->getFile($storageFilename);
    }

    private function assertStream_detach_isCalledOnce(PsrStreamInterface $responseStream): void
    {
        \Phake::verify($responseStream, \Phake::times(1))->detach();
    }

    private function givenDiskClient_getFile_returnsArrayWithStream(): PsrStreamInterface
    {
        $responseStream = \Phake::mock(PsrStreamInterface::class);
        \Phake::when($this->diskClient)->getFile(\Phake::anyParameters())->thenReturn(['body' => $responseStream]);

        return $responseStream;
    }

    private function givenDiskClient_getFile_throwsException(\Exception $exception): void
    {
        \Phake::when($this->diskClient)->getFile(\Phake::anyParameters())->thenThrow($exception);
    }

    private function givenStream_detach_returnsResource(PsrStreamInterface $stream, string $resource): void
    {
        \Phake::when($stream)->detach()->thenReturn($resource);
    }

    private function givenStream_detach_returnsNull(PsrStreamInterface $stream): void
    {
        \Phake::when($stream)->detach()->thenReturn(null);
    }

    private function assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(string $resource): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromResource($resource);
    }

    private function givenStreamFactory_createStreamFromResource_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamFromResource(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertDiskClient_directoryContents_isCalledOnceWithFilename(string $filename): void
    {
        \Phake::verify($this->diskClient, \Phake::times(1))->directoryContents($filename);
    }

    private function givenDiskClient_directoryContents_returnsArray(array $array): void
    {
        \Phake::when($this->diskClient)->directoryContents(\Phake::anyParameters())->thenReturn($array);
    }

    private function givenDiskClient_directoryContents_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->diskClient)->directoryContents(\Phake::anyParameters())->thenThrow($exception);
    }
}
