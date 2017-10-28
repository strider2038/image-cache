<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Source\Yandex;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Strider2038\ImgCache\Core\QueryParameter;
use Strider2038\ImgCache\Core\QueryParametersCollection;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\Source\Yandex\YandexMapSource;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;

class YandexMapSourceTest extends TestCase
{
    private const KEY = 'key';
    private const QUERY_PARAMETERS = ['parameter' => 'value'];
    private const QUERY_PARAMETERS_WITH_KEY = ['parameter' => 'value', 'key' => 'key'];
    private const IMAGE_BODY = 'image_body';

    /** @var ImageFactoryInterface */
    private $imageFactory;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var ClientInterface */
    private $client;

    protected function setUp(): void
    {
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->client = \Phake::mock(ClientInterface::class);
    }

    /** @test */
    public function get_givenQueryParameters_clientSendsRequestAndImageIsReturned(): void
    {
        $source = $this->createSource();
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returns($response, HttpStatusCodeEnum::OK);
        $expectedImage = $this->givenImageFactory_createFromData_returnsImage();
        $this->givenResponse_getBody_returnsStreamWithImageBody($response);
        $this->givenImageValidator_hasDataValidImageMimeType_returns(true);

        $image = $source->get($query);

        $this->assertSame($expectedImage, $image);
        $this->assertClient_request_isCalledOnce(self::QUERY_PARAMETERS);
        $this->assertResponse_getBody_isCalledOnce($response);
    }

    /** @test */
    public function get_givenQueryParametersWithKey_clientSendsRequestWithKey(): void
    {
        $source = $this->createSource(self::KEY);
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returns($response, HttpStatusCodeEnum::OK);
        $this->givenResponse_getBody_returnsStreamWithImageBody($response);
        $this->givenImageValidator_hasDataValidImageMimeType_returns(true);

        $source->get($query);

        $this->assertClient_request_isCalledOnce(self::QUERY_PARAMETERS_WITH_KEY);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponse
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function get_givenInvalidQueryParameters_responseCodeIsNot200AndExceptionThrown(): void
    {
        $source = $this->createSource();
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returns($response, HttpStatusCodeEnum::BAD_REQUEST);

        $source->get($query);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponse
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unexpected response from API
     */
    public function get_givenInvalidQueryParameters_clientThrowsException(): void
    {
        $source = $this->createSource();
        $query = $this->givenQueryParametersCollection();
        $this->givenClient_request_throwsException();

        $source->get($query);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\BadApiResponse
     * @expectedExceptionCode 502
     * @expectedExceptionMessage Unsupported mime type in response from API
     */
    public function get_givenResponseBodyHasUnsupportedMimeType_exceptionThrown(): void
    {
        $source = $this->createSource();
        $query = $this->givenQueryParametersCollection();
        $response = $this->givenClient_request_returnsResponse();
        $this->givenResponse_getStatusCode_returns($response, HttpStatusCodeEnum::OK);
        $this->givenResponse_getBody_returnsStreamWithImageBody($response);
        $this->givenImageValidator_hasDataValidImageMimeType_returns(false);

        $source->get($query);
    }

    private function createSource(string $key = ''): YandexMapSource
    {
        return new YandexMapSource($this->imageFactory, $this->imageValidator, $this->client, $key);
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

    private function givenResponse_getStatusCode_returns(ResponseInterface $response, int $code): void
    {
        \Phake::when($response)->getStatusCode()->thenReturn($code);
    }

    private function givenImageFactory_createFromData_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromData(self::IMAGE_BODY)->thenReturn($image);

        return $image;
    }

    private function givenResponse_getBody_returnsStreamWithImageBody(ResponseInterface $response): void
    {
        $body = \Phake::mock(StreamInterface::class);
        \Phake::when($body)->getContents()->thenReturn(self::IMAGE_BODY);
        \Phake::when($response)->getBody(\Phake::anyParameters())->thenReturn($body);
    }

    private function assertResponse_getBody_isCalledOnce(ResponseInterface $response): void
    {
        \Phake::verify($response, \Phake::times(1))->getBody();
    }

    private function givenImageValidator_hasDataValidImageMimeType_returns(bool $value): void
    {
        \Phake::when($this->imageValidator)->hasDataValidImageMimeType(self::IMAGE_BODY)->thenReturn($value);
    }
}
