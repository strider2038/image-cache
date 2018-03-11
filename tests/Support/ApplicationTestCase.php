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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Core\NullErrorHandler;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpHeaderEnum;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTestCase extends FunctionalTestCase
{
    /** @var string */
    private $configurationFilename;
    /** @var string */
    private $bearerAccessToken;
    /** @var ResponseInterface|null */
    private $response;

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

        $application = $this->createApplication($responseSender, $request);
        $application->run();

        return $this->response = $this->captureResponse($responseSender);
    }

    protected function createApplication(ResponseSenderInterface $responseSender, RequestInterface $request): Application
    {
        return new Application(
            new ApplicationParameters(
                self::APPLICATION_DIRECTORY,
                []
            ),
            new NullErrorHandler(),
            new TestingServiceContainerLoader($this->configurationFilename),
            new TestingSequentialServiceRunner(function (ContainerBuilder $container) use ($responseSender, $request) {
                $container->set('response_sender', $responseSender);
                $container->set('request', $request);
            })
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
}
