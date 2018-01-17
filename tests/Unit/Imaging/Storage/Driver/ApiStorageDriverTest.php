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

use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParameterCollection;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Storage\Driver\ApiStorageDriver;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;
use Strider2038\ImgCache\Utility\HttpClientInterface;

class ApiStorageDriverTest extends TestCase
{
    use LoggerTrait;

    private const PARAMETER_NAME = 'parameter_name';
    private const PARAMETER_VALUE = 'parameter_value';
    private const KEY = 'key';
    private const KEY_VALUE = 'key_value';

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
    public function getImageContents_givenQueryParameters_apiQuerySuccessfulAndStreamReturned(): void
    {
        $driver = new ApiStorageDriver($this->client);
        $driver->setLogger($this->logger);
        $query = new QueryParameterCollection([
            new QueryParameter(self::PARAMETER_NAME, self::PARAMETER_VALUE)
        ]);
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $responseBody = $this->givenResponse_getBody_returnsStream($response);

        $contents = $driver->getImageContents($query);

        $this->assertInstanceOf(StreamInterface::class, $contents);
        $this->assertClient_request_isCalledOnceWithHttpMethodAndQueryParameters(
            HttpMethodEnum::GET,
            [
                self::PARAMETER_NAME => self::PARAMETER_VALUE
            ]
        );
        $this->assertResponse_getStatusCode_isCalledOnce($response);
        $this->assertResponse_getBody_isCalledOnce($response);
        $this->assertSame($responseBody, $contents);
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /** @test */
    public function getImageContents_givenQueryParametersAndAdditionalQueryParameters_apiQueriedWithAdditionalParameters(): void
    {
        $keyParameter = new QueryParameter(self::KEY, self::KEY_VALUE);
        $additionalParameters = new QueryParameterCollection([$keyParameter]);
        $driver = new ApiStorageDriver($this->client, $additionalParameters);
        $query = new QueryParameterCollection([
            new QueryParameter(self::PARAMETER_NAME, self::PARAMETER_VALUE)
        ]);
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::OK);
        $this->givenResponse_getBody_returnsStream($response);

        $driver->getImageContents($query);

        $this->assertClient_request_isCalledOnceWithHttpMethodAndQueryParameters(
            HttpMethodEnum::GET,
            [
                self::PARAMETER_NAME => self::PARAMETER_VALUE,
                self::KEY => self::KEY_VALUE
            ]
        );
        $this->assertFalse($query->contains($keyParameter));
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponseException
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function getImageContents_givenQueryParameters_apiQueryFailedAndBadApiResponseExceptionThrown(): void
    {
        $driver = new ApiStorageDriver($this->client);
        $query = new QueryParameterCollection([
            new QueryParameter(self::PARAMETER_NAME, self::PARAMETER_VALUE)
        ]);
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returnsStatusCode($response, HttpStatusCodeEnum::BAD_REQUEST);

        $driver->getImageContents($query);
    }

    private function givenClient_request_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function givenResponse_getStatusCode_returnsStatusCode(ResponseInterface $response, int $statusCode): void
    {
        \Phake::when($response)->getStatusCode()->thenReturn(new HttpStatusCodeEnum($statusCode));
    }

    private function givenResponse_getBody_returnsStream(ResponseInterface $response): StreamInterface
    {
        $responseBody = \Phake::mock(StreamInterface::class);
        \Phake::when($response)->getBody()->thenReturn($responseBody);

        return $responseBody;
    }

    private function assertClient_request_isCalledOnceWithHttpMethodAndQueryParameters(
        string $httpMethod,
        array $queryParameters
    ): void {
        \Phake::verify($this->client, \Phake::times(1))
            ->request($httpMethod, '', \Phake::capture($options));
        $this->assertArraySubset(
            [
                RequestOptions::QUERY => $queryParameters
            ],
            $options
        );
    }

    private function assertResponse_getStatusCode_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getStatusCode();
    }

    private function assertResponse_getBody_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getBody();
    }
}
