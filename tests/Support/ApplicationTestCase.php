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

use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ApplicationTestCase extends FunctionalTestCase
{
    /** @var ContainerInterface */
    private $container;
    /** @var Application */
    private $application;
    /** @var ResponseSenderInterface */
    private $responseSender;

    protected function loadApplicationWithConfiguration(string $configurationFilename): void
    {
        $this->container = $this->loadContainer('test.yml');
        $this->container->setParameter('configuration_filename', $configurationFilename);

        $this->responseSender = \Phake::mock(ResponseSenderInterface::class);
        $this->container->set('response_sender', $this->responseSender);

        $this->application = new Application($this->container, function (\Throwable $exception) {
            throw $exception;
        });
    }

    protected function sendRequest(string $httpMethod, string $uri, StreamInterface $stream = null): ResponseInterface
    {
        $request = $this->createRequest($httpMethod, $uri, $stream);
        $this->container->set('request', $request);

        $this->application->run();

        return $this->captureResponse();
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

    private function captureResponse(): ResponseInterface
    {
        \Phake::verify($this->responseSender, \Phake::times(1))->send(\Phake::capture($response));

        return $response;
    }
}
