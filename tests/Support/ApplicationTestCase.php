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

use Strider2038\ImgCache\Core\Application;
use Strider2038\ImgCache\Core\ApplicationParameters;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Core\NullErrorHandler;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTestCase extends FunctionalTestCase
{
    /** @var string */
    private $configurationFilename;

    protected function setConfigurationFilename(string $configurationFilename): void
    {
        $this->configurationFilename = $configurationFilename;
    }

    protected function sendRequest(string $httpMethod, string $uri, StreamInterface $stream = null): ResponseInterface
    {
        $responseSender = \Phake::mock(ResponseSenderInterface::class);
        $request = $this->createRequest($httpMethod, $uri, $stream);

        $application = $this->createApplication($responseSender, $request);
        $application->run();

        return $this->captureResponse($responseSender);
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

    private function createRequest(string $httpMethod, string $uri, StreamInterface $stream = null): RequestInterface
    {
        $request = new Request(new HttpMethodEnum($httpMethod), new Uri($uri));

        if ($stream) {
            $request->setBody($stream);
        }

        return $request;
    }

    private function captureResponse(ResponseSenderInterface $responseSender): ResponseInterface
    {
        \Phake::verify($responseSender, \Phake::times(1))
            ->sendResponse(\Phake::capture($response));

        return $response;
    }
}
