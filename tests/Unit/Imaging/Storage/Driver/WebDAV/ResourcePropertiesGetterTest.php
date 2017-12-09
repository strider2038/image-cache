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
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesCollection;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResourcePropertiesGetter;
use Strider2038\ImgCache\Imaging\Storage\Driver\WebDAV\ResponseParserInterface;
use Strider2038\ImgCache\Utility\HttpClientInterface;

class ResourcePropertiesGetterTest extends TestCase
{
    private const CONTENTS = 'contents';
    private const RESOURCE_URI = 'resource_uri';

    /** @var HttpClientInterface */
    private $client;

    /** @var ResponseParserInterface */
    private $responseParser;

    protected function setUp(): void
    {
        $this->client = \Phake::mock(HttpClientInterface::class);
        $this->responseParser = \Phake::mock(ResponseParserInterface::class);
    }

    /** @test */
    public function getResourcePropertiesCollection_givenResourceUriAndResponseHasResourceProperties_resourcePropertiesReturned(): void
    {
        $propertiesGetter = $this->createResourcePropertiesGetter();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::MULTI_STATUS);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_getContents_returnsString($responseBody, self::CONTENTS);
        $expectedResourcePropertiesCollection = $this->givenResponseParser_parseResponse_returnsResourcePropertiesCollection();

        $resourcePropertiesCollection = $propertiesGetter->getResourcePropertiesCollection(self::RESOURCE_URI);

        $this->assertInstanceOf(ResourcePropertiesCollection::class, $resourcePropertiesCollection);
        $this->assertClient_request_isCalledOnceWithMethodAndPathAndOptions(
            WebDAVMethodEnum::PROPFIND,
            self::RESOURCE_URI,
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
        $this->assertSame($expectedResourcePropertiesCollection, $resourcePropertiesCollection);
    }

    /** @test */
    public function getResourcePropertiesCollection_givenResourceUriAndResponseHasCode404_falseReturned(): void
    {
        $propertiesGetter = $this->createResourcePropertiesGetter();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::NOT_FOUND);

        $resourcePropertiesCollection = $propertiesGetter->getResourcePropertiesCollection(self::RESOURCE_URI);

        $this->assertInstanceOf(ResourcePropertiesCollection::class, $resourcePropertiesCollection);
        $this->assertClient_request_isCalledOnceWithMethodAndPathAndOptions(
            WebDAVMethodEnum::PROPFIND,
            self::RESOURCE_URI,
            [
                'headers' => [
                    'Depth' => '0',
                ],
            ]
        );
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isNeverCalled($response);
        $this->assertCount(0, $resourcePropertiesCollection);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getResourcePropertiesCollection_givenResourceUriAndResponseHasCode401_falseReturned(): void
    {
        $propertiesGetter = $this->createResourcePropertiesGetter();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsCode($response, HttpStatusCodeEnum::UNAUTHORIZED);

        $propertiesGetter->getResourcePropertiesCollection(self::RESOURCE_URI);
    }

    private function createResourcePropertiesGetter(): ResourcePropertiesGetter
    {
        return new ResourcePropertiesGetter($this->client, $this->responseParser);
    }

    private function assertClient_request_isCalledOnceWithMethodAndPathAndOptions(
        string $method,
        string $path,
        array $options
    ): void {
        \Phake::verify($this->client, \Phake::times(1))->request($method, $path, $options);
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

    private function givenResponseParser_parseResponse_returnsResourcePropertiesCollection(): ResourcePropertiesCollection
    {
        $resourcePropertiesCollection = \Phake::mock(ResourcePropertiesCollection::class);
        \Phake::when($this->responseParser)
            ->parseResponse(\Phake::anyParameters())
            ->thenReturn($resourcePropertiesCollection);

        return $resourcePropertiesCollection;
    }
}
