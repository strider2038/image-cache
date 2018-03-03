<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\Service\ClientRequestProcessor;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class ClientRequestProcessorTest extends TestCase
{
    use LoggerTrait;

    /** @var RequestInterface */
    private $request;

    /** @var RequestHandlerInterface */
    private $requestHandler;

    /** @var ResponseSenderInterface */
    private $responseSender;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->request = \Phake::mock(RequestInterface::class);
        $this->requestHandler = \Phake::mock(RequestHandlerInterface::class);
        $this->responseSender = \Phake::mock(ResponseSenderInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function run_noParameters_requestHandledAndResponseSent(): void
    {
        $processor = $this->createClientRequestProcessor();
        $response = $this->givenRequestHandler_handleRequest_returnsResponse();

        $processor->run();

        $this->assertRequestHandler_handleRequest_isCalledOnceWithRequest($this->request);
        $this->assertResponseSender_sendResponse_isCalledOnceWithResponse($response);
        $this->assertLogger_debug_isCalledTimes($this->logger, 2);
    }

    private function createClientRequestProcessor(): ClientRequestProcessor
    {
        $processor = new ClientRequestProcessor(
            $this->request,
            $this->requestHandler,
            $this->responseSender
        );
        $processor->setLogger($this->logger);

        return $processor;
    }

    private function givenRequestHandler_handleRequest_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->requestHandler)->handleRequest(\Phake::anyParameters())->thenReturn($response);

        return $response;
    }

    private function assertRequestHandler_handleRequest_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->requestHandler, \Phake::times(1))->handleRequest($request);
    }

    private function assertResponseSender_sendResponse_isCalledOnceWithResponse($response): void
    {
        \Phake::verify($this->responseSender, \Phake::times(1))->sendResponse($response);
    }
}
