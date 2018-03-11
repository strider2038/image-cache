<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Support;

use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Core\Application;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Http\HeaderCollection;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Core\NullErrorHandler;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Utility\HttpClientFactoryInterface;
use Strider2038\ImgCache\Utility\HttpClientInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTestCase extends FunctionalTestCase
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var string */
    private $configurationFilename;
    /** @var string */
    private $bearerAccessToken;
    /** @var ResponseInterface|null */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = \Phake::mock(HttpClientInterface::class);
    }

    protected function setConfigurationFilename(string $configurationFilename): void
    {
        $this->configurationFilename = $configurationFilename;
    }

    protected function setBearerAccessToken(string $bearerAccessToken): void
    {
        $this->bearerAccessToken = $bearerAccessToken;
    }

    protected function sendRequest(string $httpMethod, string $uri, StreamInterface $stream = null): ResponseInterface
    {
        $responseSender = \Phake::mock(ResponseSenderInterface::class);
        $request = $this->createRequest($httpMethod, $uri, $stream);
        $httpClientFactory = $this->givenHttpClientFactory();

        $containerModifierCallable = function (ContainerBuilder $container) use (
            $responseSender,
            $request,
            $httpClientFactory
        ) {
            $container->set('response_sender', $responseSender);
            $container->set('request', $request);
            $container->set('guzzle_client_factory', $httpClientFactory);
        };

        $application = $this->createApplication($containerModifierCallable);
        $application->run();

        return $this->response = $this->captureResponse($responseSender);
    }

    protected function createApplication(\Closure $containerModifierCallable): Application
    {
        return new Application(
            new ApplicationParameters(
                self::APPLICATION_DIRECTORY,
                []
            ),
            new NullErrorHandler(),
            new TestingServiceContainerLoader($this->configurationFilename),
            new TestingSequentialServiceRunner($containerModifierCallable)
        );
    }

    protected function sendGET(string $uri, StreamInterface $stream = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::GET, $uri, $stream);
    }

    protected function sendPOST(string $uri, StreamInterface $stream = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::POST, $uri, $stream);
    }

    protected function sendPUT(string $uri, StreamInterface $stream = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::PUT, $uri, $stream);
    }

    protected function sendDELETE(string $uri, StreamInterface $stream = null): ResponseInterface
    {
        return $this->sendRequest(HttpMethodEnum::DELETE, $uri, $stream);
    }

    protected function assertResponseHasStatusCode(int $statusCode): void
    {
        $responseStatusCode = $this->response->getStatusCode()->getValue();
        $message = '';

        if ($responseStatusCode >= 300) {
            $message = 'Response body: ' . $this->response->getBody()->getContents();
        }

        $this->assertEquals($statusCode, $responseStatusCode, $message);
    }

    protected function givenHttpClient_request_returnsResponse(int $statusCode, StreamInterface $body = null): void
    {
        $response = new Response(new HttpStatusCodeEnum($statusCode));

        if ($body) {
            $response->setBody($body);
        }

        \Phake::when($this->httpClient)
            ->request(\Phake::anyParameters())
            ->thenReturn($response);
    }

    private function createRequest(string $httpMethod, string $uri, StreamInterface $stream = null): RequestInterface
    {
        $request = new Request(new HttpMethodEnum($httpMethod), new Uri($uri));

        if ($stream) {
            $request->setBody($stream);
        }

        $request->setHeaders(
            new HeaderCollection([
                HttpHeaderEnum::AUTHORIZATION => new StringList([
                    'Bearer ' . $this->bearerAccessToken,
                ])
            ])
        );

        return $request;
    }

    private function captureResponse(ResponseSenderInterface $responseSender): ResponseInterface
    {
        \Phake::verify($responseSender, \Phake::times(1))
            ->sendResponse(\Phake::capture($response));

        return $response;
    }

    private function givenHttpClientFactory(): HttpClientFactoryInterface
    {
        $httpClientFactory = \Phake::mock(HttpClientFactoryInterface::class);
        \Phake::when($httpClientFactory)
            ->createClient(\Phake::anyParameters())
            ->thenReturn($this->httpClient);

        return $httpClientFactory;
    }
}
