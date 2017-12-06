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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;

class WebDAVStorageDriverTest extends TestCase
{
    private const BASE_DIRECTORY = 'base_directory';
    private const FILENAME = 'filename.jpg';
    private const FILENAME_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME;
    private const RESOURCE = 'resource';

    /** @var ClientInterface */
    private $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    protected function setUp(): void
    {
        $this->client = \Phake::mock(ClientInterface::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
    }

    /** @test */
    public function getFileContents_givenExistingStorageFilename_streamReturned(): void
    {
        $driver = new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->client, $this->streamFactory);
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_detach_returnsResource($responseBody, self::RESOURCE);
        $expectedStream = $this->givenStreamFactory_createStreamFromResource_returnsStream();

        $fileContents = $driver->getFileContents($storageFilename);

        $this->assertInstanceOf(StreamInterface::class, $fileContents);
        $this->assertClient_request_isCalledOnceWithMethodAndPath(WebDAVMethodEnum::GET, self::FILENAME_FULL);
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertResponse_detach_isCalledOnce($responseBody);
        $this->assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(self::RESOURCE);
        $this->assertSame($expectedStream, $fileContents);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found in storage/
     */
    public function getFileContents_givenStorageFilenameAndClientThrows404_exceptionThrown(): void
    {
        $driver = new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->client, $this->streamFactory);
        $storageFilename = $this->givenStorageFilename();
        $exception = \Phake::mock(ClientException::class);
        \Phake::when($exception)->getResponse();
        $this->givenClient_request_throwsException($exception);

        $driver->getFileContents($storageFilename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Bad api response for filename
     */
    public function getFileContents_givenStorageFilenameAndClientThrows400_exceptionThrown(): void
    {
        $driver = new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->client, $this->streamFactory);
        $storageFilename = $this->givenStorageFilename();
        $exception = new class ('', HttpStatusCodeEnum::BAD_REQUEST) extends \Exception implements GuzzleException {};
        $this->givenClient_request_throwsException($exception);

        $driver->getFileContents($storageFilename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getFileContents_givenStorageFilenameAndResponseHasCode400_exceptionThrown(): void
    {
        $driver = new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->client, $this->streamFactory);
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::NOT_FOUND);

        $driver->getFileContents($storageFilename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Api response has empty body
     */
    public function getFileContents_givenStorageFilenameAndResponseHasEmptyBody_exceptionThrown(): void
    {
        $driver = new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->client, $this->streamFactory);
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_detach_returnsNull($responseBody);

        $driver->getFileContents($storageFilename);
    }

    private function givenStorageFilename(): StorageFilenameInterface
    {
        $key = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($key)->getValue()->thenReturn(self::FILENAME);

        return $key;
    }

    private function assertClient_request_isCalledOnceWithMethodAndPath(string $method, string $path): void
    {
        \Phake::verify($this->client, \Phake::times(1))->request($method, $path);
    }

    private function assertResponse_getStatusCode_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getStatusCode();
    }

    private function givenClient_request_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function givenClient_request_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->client)
            ->request(\Phake::anyParameters())
            ->thenThrow($exception);
    }

    private function givenResponse_getStatusCode_returnsCode(ResponseInterface $response, int $code): void
    {
        \Phake::when($response)->getStatusCode()->thenReturn($code);
    }

    private function givenResponse_getBody_returnsStream(ResponseInterface $response): PsrStreamInterface
    {
        $body = \Phake::mock(PsrStreamInterface::class);
        \Phake::when($response)->getBody(\Phake::anyParameters())->thenReturn($body);

        return $body;
    }

    private function assertResponse_getBody_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getBody();
    }

    private function assertResponse_detach_isCalledOnce(PsrStreamInterface $stream): void
    {
        \Phake::verify($stream, \Phake::times(1))->detach();
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
}