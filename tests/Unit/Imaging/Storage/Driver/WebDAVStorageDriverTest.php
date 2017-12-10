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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Imaging\Storage\Data\StorageFilenameInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceCheckerInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAVStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\ProviderTrait;
use Strider2038\ImgCache\Utility\GuzzleClientAdapter;

class WebDAVStorageDriverTest extends TestCase
{
    use ProviderTrait;

    private const BASE_DIRECTORY = 'base_directory';
    private const FILENAME = 'filename.jpg';
    private const FILENAME_FULL = self::BASE_DIRECTORY . '/' . self::FILENAME;

    /** @var GuzzleClientAdapter */
    private $clientAdapter;

    /** @var ResourceCheckerInterface */
    private $resourceChecker;

    protected function setUp(): void
    {
        $this->clientAdapter = \Phake::mock(GuzzleClientAdapter::class);
        $this->resourceChecker = \Phake::mock(ResourceCheckerInterface::class);
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

    /**
     * @test
     * @dataProvider boolValuesProvider
     * @param bool $expectedFileExists
     */
    public function fileExists_givenStorageFilenameAndResourcePropertiesCollection_boolReturned(
        bool $expectedFileExists
    ): void {
        $driver = $this->createWebDAVStorageDriver();
        $storageFilename = $this->givenStorageFilename();
        $this->givenResourceChecker_isFile_returnsBool($expectedFileExists);

        $fileExists = $driver->fileExists($storageFilename);

        $this->assertResourceChecker_isFile_isCalledOnceWithResourceUri(self::FILENAME_FULL);
        $this->assertEquals($expectedFileExists, $fileExists);
    }

    private function createWebDAVStorageDriver(): WebDAVStorageDriver
    {
        return new WebDAVStorageDriver(self::BASE_DIRECTORY, $this->clientAdapter, $this->resourceChecker);
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

    private function assertResourceChecker_isFile_isCalledOnceWithResourceUri(string $storageFilename): void
    {
        \Phake::verify($this->resourceChecker, \Phake::times(1))->isFile($storageFilename);
    }

    private function givenResourceChecker_isFile_returnsBool(bool $isFile): void
    {
        \Phake::when($this->resourceChecker)->isFile(\Phake::anyParameters())->thenReturn($isFile);
    }
}
