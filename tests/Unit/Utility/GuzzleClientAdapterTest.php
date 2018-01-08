<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Utility;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Utility\GuzzleClientAdapter;

class GuzzleClientAdapterTest extends TestCase
{
    private const METHOD = 'method';
    private const URI = 'uri';
    private const OPTIONS = ['options'];
    private const RESOURCE = 'resource';
    private const APPLICATION_JSON = 'application/json';

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
    public function request_givenMethodUriAndOptions_clientRequestCalledAndTransformedResponseReturned(): void
    {
        $adapter = $this->createGuzzleClientAdapter();
        $psrResponse = $this->givenClient_request_returnsPsrResponse();
        $this->givenPsrResponse_getStatusCode_returnsCode($psrResponse, HttpStatusCodeEnum::OK);
        $this->givenPsrResponse_getHeaders_returnsArray($psrResponse, [
            'Content-Type' => [
                self::APPLICATION_JSON,
            ],
        ]);
        $psrStream = $this->givenPsrResponse_getBody_returnsPsrStream($psrResponse);
        $this->givenPsrStream_detach_returnsResource($psrStream, self::RESOURCE);
        $stream = $this->givenStreamFactory_createStreamFromResouce_returnsStream();

        $response = $adapter->request(self::METHOD, self::URI, self::OPTIONS);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertClient_request_isCalledOnceWithMethodAndUriAndOptions(
            self::METHOD,
            self::URI,
            self::OPTIONS
        );
        $this->assertPsrResponse_getStatusCode_isCalledOnce($psrResponse);
        $this->assertPsrResponse_getBody_isCalledOnce($psrResponse);
        $this->assertPsrResponse_getHeaders_isCalledOnce($psrResponse);
        $this->assertPsrStream_detach_isCalledOnce($psrStream);
        $this->assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(self::RESOURCE);
        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
        $this->assertEquals($stream, $response->getBody());
        $this->assertHeadersAreValid($response->getHeaders());
    }

    /** @test */
    public function request_givenMethodAndClientThrowsClientException_clientRequestCalledAndTransformedResponseReturned(): void
    {
        $adapter = $this->createGuzzleClientAdapter();
        $clientException = $this->givenClient_request_throwsClientException();
        $psrResponse = $this->givenClientException_getResponse_returnsPsrResponse($clientException);
        $this->givenPsrResponse_getStatusCode_returnsCode($psrResponse, HttpStatusCodeEnum::BAD_REQUEST);
        $this->givenPsrResponse_getHeaders_returnsArray($psrResponse, []);
        $psrStream = $this->givenPsrResponse_getBody_returnsPsrStream($psrResponse);
        $this->givenPsrStream_detach_returnsResource($psrStream, self::RESOURCE);
        $stream = $this->givenStreamFactory_createStreamFromResouce_returnsStream();

        $response = $adapter->request(self::METHOD, self::URI, self::OPTIONS);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertClient_request_isCalledOnceWithMethodAndUriAndOptions(
            self::METHOD,
            self::URI,
            self::OPTIONS
        );
        $this->assertPsrResponse_getStatusCode_isCalledOnce($psrResponse);
        $this->assertPsrResponse_getBody_isCalledOnce($psrResponse);
        $this->assertPsrResponse_getHeaders_isCalledOnce($psrResponse);
        $this->assertPsrStream_detach_isCalledOnce($psrStream);
        $this->assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(self::RESOURCE);
        $this->assertEquals(HttpStatusCodeEnum::BAD_REQUEST, $response->getStatusCode()->getValue());
        $this->assertEquals($stream, $response->getBody());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Api response has empty body
     */
    public function request_givenMethodAndResponseBodyIsEmpty_badApiResponseExceptionThrown(): void
    {
        $adapter = $this->createGuzzleClientAdapter();
        $psrResponse = $this->givenClient_request_returnsPsrResponse();
        $this->givenPsrResponse_getStatusCode_returnsCode($psrResponse, HttpStatusCodeEnum::OK);
        $this->givenPsrResponse_getHeaders_returnsArray($psrResponse, []);
        $psrStream = $this->givenPsrResponse_getBody_returnsPsrStream($psrResponse);
        $this->givenPsrStream_detach_returnsResource($psrStream, null);

        $adapter->request(self::METHOD);
    }

    private function createGuzzleClientAdapter(): GuzzleClientAdapter
    {
        return new GuzzleClientAdapter($this->client, $this->streamFactory);
    }

    private function assertClient_request_isCalledOnceWithMethodAndUriAndOptions(
        string $method,
        string $uri,
        array $options
    ): void {
        \Phake::verify($this->client, \Phake::times(1))->request($method, $uri, $options);
    }

    private function givenClient_request_returnsPsrResponse(): PsrResponseInterface
    {
        $psrResponse = \Phake::mock(PsrResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($psrResponse);

        return $psrResponse;
    }

    private function givenClient_request_throwsClientException(): ClientException
    {
        $exception = \Phake::mock(ClientException::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenThrow($exception);

        return $exception;
    }

    private function assertPsrResponse_getStatusCode_isCalledOnce(PsrResponseInterface $psrResponse): void
    {
        \Phake::verify($psrResponse, \Phake::times(1))->getStatusCode();
    }

    private function givenPsrResponse_getStatusCode_returnsCode(PsrResponseInterface $psrResponse, int $code): void
    {
        \Phake::when($psrResponse)->getStatusCode()->thenReturn($code);
    }

    private function assertPsrResponse_getBody_isCalledOnce(PsrResponseInterface $psrResponse): void
    {
        \Phake::verify($psrResponse, \Phake::times(1))->getBody();
    }

    private function assertPsrStream_detach_isCalledOnce(PsrStreamInterface $psrStream): void
    {
        \Phake::verify($psrStream, \Phake::times(1))->detach();
    }

    private function givenPsrResponse_getBody_returnsPsrStream(PsrResponseInterface $psrResponse): PsrStreamInterface
    {
        $psrStream = \Phake::mock(PsrStreamInterface::class);
        \Phake::when($psrResponse)->getBody()->thenReturn($psrStream);

        return $psrStream;
    }

    private function givenPsrStream_detach_returnsResource(PsrStreamInterface $psrStream, $resource): void
    {
        \Phake::when($psrStream)->detach()->thenReturn($resource);
    }

    private function assertStreamFactory_createStreamFromResource_isCalledOnceWithResource($resource): void
    {
        \Phake::verify($this->streamFactory, \Phake::times(1))->createStreamFromResource($resource);
    }

    private function givenStreamFactory_createStreamFromResouce_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->streamFactory)->createStreamFromResource(\Phake::anyParameters())->thenReturn($stream);

        return $stream;
    }

    private function assertPsrResponse_getHeaders_isCalledOnce($psrResponse): void
    {
        \Phake::verify($psrResponse, \Phake::times(1))->getHeaders();
    }

    private function givenPsrResponse_getHeaders_returnsArray(PsrResponseInterface $psrResponse, array $headers): void
    {
        \Phake::when($psrResponse)->getHeaders()->thenReturn($headers);
    }

    private function assertHeadersAreValid(HeaderCollection $headers): void
    {
        $this->assertCount(1, $headers);
        $contentTypeHeader = HttpHeaderEnum::CONTENT_TYPE;
        $this->assertTrue($headers->containsKey($contentTypeHeader));
        $headerValues = $headers->get($contentTypeHeader);
        $this->assertCount(1, $headerValues);
        $this->assertEquals(self::APPLICATION_JSON, $headerValues->toArray()[0]);
    }

    private function givenClientException_getResponse_returnsPsrResponse(ClientException $clientException): PsrResponseInterface
    {
        $psrResponse = \Phake::mock(PsrResponseInterface::class);
        \Phake::when($clientException)->getResponse()->thenReturn($psrResponse);

        return $psrResponse;
    }
}
