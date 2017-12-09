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
use Strider2038\ImgCache\Core\GuzzleClientAdapter;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceProperties;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesCollection;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResponseParserInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;

class WebDAVStorageDriverTest extends TestCase
{
    private const BASE_DIRECTORY = 'base_directory';
    private const FILENAME = 'filename.jpg';
    private const FILENAME_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME;
    private const RESOURCE = 'resource';
    const CONTENTS = 'contents';

    /** @var GuzzleClientAdapter */
    private $clientAdapter;

    /** @var ResponseParserInterface */
    private $responseParser;

    protected function setUp(): void
    {
        $this->clientAdapter = \Phake::mock(GuzzleClientAdapter::class);
        $this->responseParser = \Phake::mock(ResponseParserInterface::class);
    }

    /** @test */
    public function getFileContents_givenExistingStorageFilename_streamReturned(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);

        $fileContents = $driver->getFileContents($storageFilename);

        $this->assertInstanceOf(StreamInterface::class, $fileContents);
        $this->assertClientAdapter_request_isCalledOnceWithMethodAndPath(WebDAVMethodEnum::GET, self::FILENAME_FULL);
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertSame($responseBody, $fileContents);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found in storage/
     */
    public function getFileContents_givenStorageFilenameAndResponseIs404_notFoundExceptionThrown(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::NOT_FOUND);

        $driver->getFileContents($storageFilename);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getFileContents_givenStorageFilenameAndResponseHasCode403_badApiResponseExceptionThrown(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::FORBIDDEN);

        $driver->getFileContents($storageFilename);
    }

    /** @test */
    public function fileExists_givenStorageFilenameAndResponseHasResourceProperties_boolReturned(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::MULTI_STATUS);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_getContents_returnsString($responseBody, self::CONTENTS);
        $resourceProperties = \Phake::mock(ResourceProperties::class);
        $resourcePropertiesCollection = new ResourcePropertiesCollection([$resourceProperties]);
        \Phake::when($this->responseParser)->parseResponse()->thenReturn($resourcePropertiesCollection);

        $fileExists = $driver->fileExists($storageFilename);

        $this->assertTrue($fileExists);
        $this->assertClientAdapter_request_isCalledOnceWithMethodAndPathAndOptions(
            WebDAVMethodEnum::PROPFIND,
            self::FILENAME_FULL,
            [
                'headers' => [
                    'Depth' => '0',
                ],
            ]
        );
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertStream_getContents_isCalledOnce($responseBody);
        $this->assertResponseParser_parseResponse_isCalledOnceWithContents(self::CONTENTS);
    }

    /** @test */
    public function fileExists_givenStorageFilenameAndResponseHasCode404_falseReturned(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::NOT_FOUND);

        $fileExists = $driver->fileExists($storageFilename);

        $this->assertFalse($fileExists);
        $this->assertClientAdapter_request_isCalledOnceWithMethodAndPathAndOptions(
            WebDAVMethodEnum::PROPFIND,
            self::FILENAME_FULL,
            [
                'headers' => [
                    'Depth' => '0',
                ],
            ]
        );
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isNeverCalled($response);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function fileExists_givenStorageFilenameAndResponseHasCode401_falseReturned(): void
    {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $response = $this->givenClientAdapter_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::UNAUTHORIZED);

        $driver->fileExists($storageFilename);
    }

    private function createWebDAVStorageDriver(): WebDAVStorageDriver
    {
        return new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->clientAdapter, $this->responseParser);
    }

    private function givenStorageFilename(): StorageFilenameInterface
    {
        $key = \Phake::mock(StorageFilenameInterface::class);
        \Phake::when($key)->getValue()->thenReturn(self::FILENAME);

        return $key;
    }

    private function assertClientAdapter_request_isCalledOnceWithMethodAndPath(string $method, string $path): void
    {
        \Phake::verify($this->clientAdapter, \Phake::times(1))->request($method, $path);
    }

    private function assertClientAdapter_request_isCalledOnceWithMethodAndPathAndOptions(
        string $method,
        string $path,
        array $options
    ): void {
        \Phake::verify($this->clientAdapter, \Phake::times(1))->request($method, $path, $options);
    }

    private function assertResponse_getStatusCode_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getStatusCode();
    }

    private function givenClientAdapter_request_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->clientAdapter)->request(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function givenClient_request_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->clientAdapter)
            ->request(\Phake::anyParameters())
            ->thenThrow($exception);
    }

    private function givenResponse_getStatusCode_returnsCode(ResponseInterface $response, int $code): void
    {
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCodeEnum($code));
    }

    private function givenResponse_getBody_returnsStream(ResponseInterface $response): StreamInterface
    {
        $body = \Phake::mock(StreamInterface::class);
        \Phake::when($response)->getBody(\Phake::anyParameters())->thenReturn($body);

        return $body;
    }

    private function assertResponse_getBody_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getBody();
    }

    private function assertResponse_getBody_isNeverCalled(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(0))->getBody();
    }

    private function assertStream_getContents_isCalledOnce(StreamInterface $responseBody): void
    {
        \Phake::verify($responseBody, \Phake::times(1))->getContents();
    }

    private function assertResponseParser_parseResponse_isCalledOnceWithContents(string $contents): void
    {
        \Phake::verify($this->responseParser, \Phake::times(1))->parseResponse($contents);
    }

    private function givenStream_getContents_returnsString(StreamInterface $responseBody, string $contents): void
    {
        \Phake::when($responseBody)->getContents()->thenReturn($contents);
    }
}
