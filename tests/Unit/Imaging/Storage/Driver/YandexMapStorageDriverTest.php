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
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Core\Streaming\StreamFactoryInterface;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class YandexMapStorageDriverTest extends TestCase
{
    use LoggerTrait;

    private const KEY = 'key';
    private const QUERY_PARAMETERS = ['parameter' => 'value'];
    private const QUERY_PARAMETERS_WITH_KEY = ['parameter' => 'value', 'key' => 'key'];
    private const RESOURCE = 'resource';

    /** @var ClientInterface */
    private $client;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->client = \Phake::mock(ClientInterface::class);
        $this->streamFactory = \Phake::mock(StreamFactoryInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function getMapContents_givenQueryParameters_clientSendsRequestAndStreamReturned(): void
    {
        $driver = $this->createYandexMapStorageDriver();
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_detach_returnsResource($responseBody, self::RESOURCE);
        $expectedStream = $this->givenStreamFactory_createStreamFromResource_returnsStream();

        $stream = $driver->getMapContents($query);

        $this->assertClient_request_isCalledOnce(self::QUERY_PARAMETERS);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertResponse_detach_isCalledOnce($responseBody);
        $this->assertStreamFactory_createStreamFromResource_isCalledOnceWithResource(self::RESOURCE);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
        $this->assertSame($expectedStream, $stream);
    }

    /** @test */
    public function getMapContents_givenQueryParametersWithKey_clientSendsRequestWithKey(): void
    {
        $driver = $this->createYandexMapStorageDriver(self::KEY);
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_detach_returnsResource($responseBody, self::RESOURCE);
        $this->givenStreamFactory_createStreamFromResource_returnsStream();

        $driver->getMapContents($query);

        $this->assertClient_request_isCalledOnce(self::QUERY_PARAMETERS_WITH_KEY);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Response has empty body
     */
    public function getMapContents_givenQueryParametersAndResponseHasEmptyBody_exceptionThrown(): void
    {
        $driver = $this->createYandexMapStorageDriver(self::KEY);
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);
        $this->givenStream_detach_returnsNull($responseBody);

        $driver->getMapContents($query);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getMapContents_givenInvalidQueryParameters_responseCodeIsNot200AndExceptionThrown(): void
    {
        $driver = $this->createYandexMapStorageDriver();
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::BAD_REQUEST);

        $driver->getMapContents($query);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getMapContents_givenInvalidQueryParameters_clientThrowsException(): void
    {
        $driver = $this->createYandexMapStorageDriver();
        $query = $this->givenQueryParametersCollection();
        $this->givenClient_request_throwsException();

        $driver->getMapContents($query);
    }

    private function createYandexMapStorageDriver(string $key = ''): YandexMapStorageDriver
    {
        $driver = new YandexMapStorageDriver($this->client, $this->streamFactory, $key);
        $driver->setLogger($this->logger);

        return $driver;
    }

    private function givenClient_request_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function givenClient_request_throwsException(): void
    {
        \Phake::when($this->client)
            ->request(\Phake::anyParameters())
            ->thenThrow(new class extends \Exception implements GuzzleException {});
    }

    private function assertClient_request_isCalledOnce(array $queryParameters): void
    {
        /** @var string $method */
        /** @var string $uri */
        /** @var array $parameters */
        \Phake::verify($this->client, \Phake::times(1))
            ->request(\Phake::capture($method), \Phake::capture($uri), \Phake::capture($parameters));
        $this->assertEquals(HttpMethodEnum::GET, $method);
        $this->assertEquals('', $uri);
        $this->assertArrayHasKey('query', $parameters);
        $this->assertEquals($queryParameters, $parameters['query']);
    }

    private function givenQueryParametersCollection(): QueryParametersCollection
    {
        $query = new QueryParametersCollection();
        $query->add(new QueryParameter('parameter', 'value'));

        return $query;
    }

    private function givenResponse_getStatusCode_returnsStatusCode(ResponseInterface $response, int $code): void
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
