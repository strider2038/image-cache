<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\AccessControlInterface;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Exception\ApplicationException;
use Strider2038\ImgCache\Service\HttpRequestHandler;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

class HttpRequestHandlerTest extends TestCase
{
    use LoggerTrait;

    private const EXCEPTION_MESSAGE = 'exception_message';

    /** @var AccessControlInterface */
    private $accessControl;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestHandlerInterface */
    private $concreteRequestHandler;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->accessControl = \Phake::mock(AccessControlInterface::class);
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->concreteRequestHandler = \Phake::mock(RequestHandlerInterface::class);
        $this->logger = $this->givenLogger();
    }

    /** @test */
    public function handleRequest_givenRequest_accessDeniedAndForbiddenResponseReturned(): void
    {
        $requestHandler = $this->createHttpRequestHandler();
        $request = \Phake::mock(RequestInterface::class);
        $this->givenAccessControl_canHandleRequest_returnsBool(false);
        $expectedResponse = $this->givenResponseFactory_createMessageResponse_returnsResponse();

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertAccessControl_canHandleRequest_isCalledOnceWithRequest($request);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithHttpCode(HttpStatusCodeEnum::FORBIDDEN);
        $this->assertSame($expectedResponse, $response);
    }

    /** @test */
    public function handleRequest_givenRequest_accessGrantedAndResponseFromConcreteRequestHandlerReturned(): void
    {
        $requestHandler = $this->createHttpRequestHandler();
        $request = \Phake::mock(RequestInterface::class);
        $this->givenAccessControl_canHandleRequest_returnsBool(true);
        $expectedResponse = $this->givenConcreteRequestHandler_handleRequest_returnsResponse();

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertAccessControl_canHandleRequest_isCalledOnceWithRequest($request);
        $this->assertConcreteRequestHandler_handleRequest_isCalledOnceWithRequest($request);
        $this->assertSame($expectedResponse, $response);
    }

    /** @test */
    public function handleRequest_givenRequestAndConcreteRequestHandlerThrowsException_responseWithExceptionReturned(): void
    {
        $requestHandler = $this->createHttpRequestHandler();
        $request = \Phake::mock(RequestInterface::class);
        $this->givenAccessControl_canHandleRequest_returnsBool(true);
        $exception = new ApplicationException(self::EXCEPTION_MESSAGE);
        $this->givenConcreteRequestHandler_handleRequest_throwsException($exception);
        $expectedResponse = $this->givenResponseFactory_createExceptionResponse_returnsResponse();

        $response = $requestHandler->handleRequest($request);

        $this->assertConcreteRequestHandler_handleRequest_isCalledOnceWithRequest($request);
        $this->assertResponseFactory_createExceptionResponse_isCalledOnceWithException($exception);
        $this->assertLogger_error_isCalledOnce($this->logger);
        $this->assertSame($expectedResponse, $response);
    }

    private function createHttpRequestHandler(): HttpRequestHandler
    {
        $handler = new HttpRequestHandler(
            $this->accessControl,
            $this->responseFactory,
            $this->concreteRequestHandler
        );
        $handler->setLogger($this->logger);

        return $handler;
    }

    private function givenAccessControl_canHandleRequest_returnsBool(bool $value): void
    {
        \Phake::when($this->accessControl)->canHandleRequest(\Phake::anyParameters())->thenReturn($value);
    }

    private function assertAccessControl_canHandleRequest_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->accessControl, \Phake::times(1))->canHandleRequest($request);
    }

    private function assertResponseFactory_createMessageResponse_isCalledOnceWithHttpCode(int $statusCode): void
    {
        /** @var HttpStatusCodeEnum $statusCodeEnum */
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::capture($statusCodeEnum));
        $this->assertEquals($statusCode, $statusCodeEnum->getValue());
    }

    private function assertResponseFactory_createExceptionResponse_isCalledOnceWithException(\Throwable $exception): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createExceptionResponse($exception);
    }

    private function givenResponseFactory_createMessageResponse_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function givenResponseFactory_createExceptionResponse_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->responseFactory, \Phake::times(1))
            ->createExceptionResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function givenConcreteRequestHandler_handleRequest_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->concreteRequestHandler)
            ->handleRequest(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function givenConcreteRequestHandler_handleRequest_throwsException(\Throwable $exception): void
    {
        \Phake::when($this->concreteRequestHandler)
            ->handleRequest(\Phake::anyParameters())
            ->thenThrow($exception);
    }

    private function assertConcreteRequestHandler_handleRequest_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->concreteRequestHandler, \Phake::times(1))->handleRequest($request);
    }
}
