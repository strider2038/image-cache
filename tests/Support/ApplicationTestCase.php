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

use Psr\Container\ContainerInterface;
use Strider2038\ImgCache\Collection\StringList;
use Strider2038\ImgCache\Configuration\Configuration;
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
use Strider2038\ImgCache\Core\Service\SequentialServiceRunner;
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
    /** @var ContainerBuilder */
    private $container;
    /** @var HttpClientInterface */
    private $httpClient;
    /** @var ResponseSenderInterface */
    private $responseSender;

    /** @var string */
    private $bearerAccessToken;
    /** @var ResponseInterface|null */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->loadContainer('main.yml');
        $this->registerFakeResponseSender();
    }

    protected function registerFakeResponseSender(): void
    {
        $this->responseSender = \Phake::mock(ResponseSenderInterface::class);
        $this->container->set('response_sender', $this->responseSender);
    }

    protected function registerFakeHttpClient(): void
    {
        $this->httpClient = \Phake::mock(HttpClientInterface::class);
        $httpClientFactory = $this->givenHttpClientFactory();
        $this->container->set('guzzle_client_factory', $httpClientFactory);
    }

    protected function loadConfigurationToContainer(Configuration $configuration): void
    {
        $this->container->setParameter('access_control_token', $configuration->getAccessControlToken());
        $this->container->setParameter('cached_image_quality', $configuration->getCachedImageQuality());
        $this->container->setParameter('image_sources', $configuration->getSourceCollection());
    }

    protected function setBearerAccessToken(string $bearerAccessToken): void
    {
        $this->bearerAccessToken = $bearerAccessToken;
    }

    protected function sendRequest(string $httpMethod, string $uri, StreamInterface $stream = null): ResponseInterface
    {
        $request = $this->createRequest($httpMethod, $uri, $stream);
        $this->container->set('request', $request);

        $application = $this->createApplication($this->container);
        $application->run();

        return $this->captureResponse();
    }

    protected function createApplication(ContainerInterface $container): Application
    {
        return new Application(
            new ApplicationParameters(
                self::APPLICATION_DIRECTORY,
                []
            ),
            new NullErrorHandler(),
            new ServiceContainerLoaderFake($container),
            new SequentialServiceRunner()
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

    private function captureResponse(): ResponseInterface
    {
        \Phake::verify($this->responseSender, \Phake::times(1))
            ->sendResponse(\Phake::capture($this->response));

        return $this->response;
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
