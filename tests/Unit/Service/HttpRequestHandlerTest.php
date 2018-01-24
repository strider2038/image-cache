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
use Strider2038\ImgCache\Core\AccessControlInterface;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Service\HttpRequestHandler;

class HttpRequestHandlerTest extends TestCase
{
    /** @var AccessControlInterface */
    private $accessControl;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var RequestHandlerInterface */
    private $concreteRequestHandler;

    protected function setUp(): void
    {
        $this->accessControl = \Phake::mock(AccessControlInterface::class);
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->concreteRequestHandler = \Phake::mock(RequestHandlerInterface::class);
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

    private function createHttpRequestHandler(): HttpRequestHandler
    {
        return new HttpRequestHandler(
            $this->accessControl,
            $this->responseFactory,
            $this->concreteRequestHandler
        );
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

    private function givenResponseFactory_createMessageResponse_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function givenConcreteRequestHandler_handleRequest_returnsResponse(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->concreteRequestHandler, \Phake::times(1))
            ->handleRequest(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function assertConcreteRequestHandler_handleRequest_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->concreteRequestHandler, \Phake::times(1))->handleRequest($request);
    }
}
