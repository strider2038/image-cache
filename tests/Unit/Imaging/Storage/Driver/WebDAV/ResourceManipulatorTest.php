<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Storage\Driver\WebDAV;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\RequestOptionsFactoryInterface;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourceManipulator;
use Strider2038\ImgCache\Utility\HttpClientInterface;

class ResourceManipulatorTest extends TestCase
{
    private const RESOURCE_URI = 'resource_uri';
    private const REQUEST_OPTIONS = ['request_options'];

    /** @var HttpClientInterface */
    private $client;

    /** @var RequestOptionsFactoryInterface */
    private $requestOptionsFactory;

    protected function setUp(): void
    {
        $this->client = \Phake::mock(HttpClientInterface::class);
        $this->requestOptionsFactory = \Phake::mock(RequestOptionsFactoryInterface::class);
    }

    /** @test */
    public function getResource_givenExistingResourceUri_streamReturned(): void
    {
        $manipulator = $this->createResourceManipulator();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);

        $stream = $manipulator->getResource(self::RESOURCE_URI);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertClient_request_isCalledOnceWithMethodAndPath(WebDAVMethodEnum::GET, self::RESOURCE_URI);
        $this->assertResponse_getStatusCode_isCalledTwice($response);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertSame($responseBody, $stream);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\FileNotFoundException
     * @expectedExceptionCode 404
     * @expectedExceptionMessageRegExp /File .* not found in storage/
     */
    public function getResource_givenResourceUriAndResponseIs404_notFoundExceptionThrown(): void
    {
        $manipulator = $this->createResourceManipulator();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::NOT_FOUND);

        $manipulator->getResource(self::RESOURCE_URI);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getResource_givenResourceUriAndResponseHasCode403_badApiResponseExceptionThrown(): void
    {
        $manipulator = $this->createResourceManipulator();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::FORBIDDEN);

        $manipulator->getResource(self::RESOURCE_URI);
    }

    /** @test */
    public function putResource_givenResourceUriAndContents_contentsSentToServerViaHttpClient(): void
    {
        $manipulator = $this->createResourceManipulator();
        $stream = $this->givenStream();
        $this->givenRequestOptionsFactory_createPutOptions_returnsArray(self::REQUEST_OPTIONS);
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::CREATED);

        $manipulator->putResource(self::RESOURCE_URI, $stream);

        $this->assertRequestOptionsFactory_createPutOptions_isCalledOnceWithStream($stream);
        $this->assertClient_request_isCalledWithMethodAndUriAndOptions(
            WebDAVMethodEnum::PUT,
            self::RESOURCE_URI,
            self::REQUEST_OPTIONS
        );
        $this->assertResponse_getStatusCode_isCalledOnce($response);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function putResource_givenResourceUriAndContentsAndResponseHasErrorCode_badApiResponseExceptionThrown(): void
    {
        $manipulator = $this->createResourceManipulator();
        $stream = $this->givenStream();
        $this->givenRequestOptionsFactory_createPutOptions_returnsArray(self::REQUEST_OPTIONS);
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::FORBIDDEN);

        $manipulator->putResource(self::RESOURCE_URI, $stream);
    }

    /** @test */
    public function createDirectory_givenDirectoryUri_clientReturnsCreatedResponse(): void
    {
        $manipulator = $this->createResourceManipulator();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::CREATED);

        $manipulator->createDirectory(self::RESOURCE_URI);

        $this->assertClient_request_isCalledOnceWithMethodAndPath(WebDAVMethodEnum::MKCOL, self::RESOURCE_URI);
        $this->assertResponse_getStatusCode_isCalledOnce($response);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function createDirectory_givenInvalidDirectoryUri_badApiResponseExceptionThrown(): void
    {
        $manipulator = $this->createResourceManipulator();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::CONFLICT);

        $manipulator->createDirectory(self::RESOURCE_URI);
    }

    private function createResourceManipulator(): ResourceManipulator
    {
        return new ResourceManipulator($this->client, $this->requestOptionsFactory);
    }

    private function assertClient_request_isCalledOnceWithMethodAndPath(string $method, string $path): void
    {
        \Phake::verify($this->client, \Phake::times(1))->request($method, $path);
    }

    private function assertResponse_getStatusCode_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getStatusCode();
    }

    private function assertResponse_getStatusCode_isCalledTwice(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(2))->getStatusCode();
    }

    private function givenClient_request_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($response);

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

    private function assertClient_request_isCalledWithMethodAndUriAndOptions(
        string $method,
        string $uri,
        array $options
    ): void {
        \Phake::verify($this->client, \Phake::times(1))->request($method, $uri, $options);
    }

    private function assertRequestOptionsFactory_createPutOptions_isCalledOnceWithStream(StreamInterface $stream): void
    {
        \Phake::verify($this->requestOptionsFactory, \Phake::times(1))->createPutOptions($stream);
    }

    private function givenRequestOptionsFactory_createPutOptions_returnsArray(array $options): void
    {
        \Phake::when($this->requestOptionsFactory)->createPutOptions(\Phake::anyParameters())->thenReturn($options);
    }

    private function givenStream(): StreamInterface
    {
        return \Phake::mock(StreamInterface::class);
    }
}
