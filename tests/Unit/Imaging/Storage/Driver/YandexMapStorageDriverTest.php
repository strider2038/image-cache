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
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\YandexMapStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Utility\HttpClientInterface;

class YandexMapStorageDriverTest extends TestCase
{
    use LoggerTrait;

    private const KEY = 'key';
    private const QUERY_PARAMETERS = ['parameter' => 'value'];
    private const QUERY_PARAMETERS_WITH_KEY = ['parameter' => 'value', 'key' => 'key'];

    /** @var HttpClientInterface */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->client = \Phake::mock(HttpClientInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function getMapContents_givenQueryParameters_clientSendsRequestAndStreamReturned(): void
    {
        $driver = $this->createYandexMapStorageDriver();
        $query = $this->givenQueryParameterCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);

        $stream = $driver->getMapContents($query);

        $this->assertClient_request_isCalledOnceWithGetAndEmptyUriAndParameters(self::QUERY_PARAMETERS);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
        $this->assertSame($responseBody, $stream);
    }

    /** @test */
    public function getMapContents_givenQueryParametersWithKey_clientSendsRequestWithKey(): void
    {
        $driver = $this->createYandexMapStorageDriver(self::KEY);
        $query = $this->givenQueryParameterCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $this->givenResponse_getBody_returnsStream($response);

        $driver->getMapContents($query);

        $this->assertClient_request_isCalledOnceWithGetAndEmptyUriAndParameters(self::QUERY_PARAMETERS_WITH_KEY);
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
        $query = $this->givenQueryParameterCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::BAD_REQUEST);

        $driver->getMapContents($query);
    }

    private function createYandexMapStorageDriver(string $key = ''): YandexMapStorageDriver
    {
        $driver = new YandexMapStorageDriver($this->client, $key);
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

    private function assertClient_request_isCalledOnceWithGetAndEmptyUriAndParameters(array $queryParameters): void
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

    private function givenQueryParameterCollection(): QueryParameterCollection
    {
        $query = new QueryParameterCollection();
        $query->add(new QueryParameter('parameter', 'value'));

        return $query;
    }

    private function givenResponse_getStatusCode_returnsStatusCode(ResponseInterface $response, int $code): void
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

    private function assertResponse_getStatusCode_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getStatusCode();
    }
}
