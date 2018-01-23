<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\RequestHandlerInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\Image\ImageRequestHandler;
use Strider2038\ImgCache\Service\Image\ImageRequestHandlerFactoryInterface;

class ImageRequestHandlerTest extends TestCase
{
    /** @var ImageRequestHandlerFactoryInterface */
    private $requestHandlerFactory;

    protected function setUp(): void
    {
        $this->requestHandlerFactory = \Phake::mock(ImageRequestHandlerFactoryInterface::class);
    }

    /** @test */
    public function handleRequest_givenRequest_imageRequestHandlerCreatedAndHandledResponseReturned(): void
    {
        $requestHandler = new ImageRequestHandler($this->requestHandlerFactory);
        $request = \Phake::mock(RequestInterface::class);
        $method = $this->givenRequest_getMethod_returnsMethod($request);
        $concreteRequestHandler = $this->givenRequestHandlerFactory_createRequestHandlerByHttpMethod_returnsRequestHandler();
        $expectedResponse = $this->givenRequestHandler_handleRequest_returnsResponse($concreteRequestHandler);

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getMethod_isCalledOnce($request);
        $this->assertRequestHandlerFactory_createRequestHandlerByHttpMethod_isCalledOnceWithMethod($method);
        $this->assertRequestHandler_handleRequest_isCalledOnceWithRequest($concreteRequestHandler, $request);
        $this->assertSame($expectedResponse, $response);
    }

    private function givenRequest_getMethod_returnsMethod(RequestInterface $request): HttpMethodEnum
    {
        $method = \Phake::mock(HttpMethodEnum::class);
        \Phake::when($request)->getMethod()->thenReturn($method);

        return $method;
    }

    private function assertRequest_getMethod_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getMethod();
    }

    private function givenRequestHandlerFactory_createRequestHandlerByHttpMethod_returnsRequestHandler(): RequestHandlerInterface
    {
        $requestHandler = \Phake::mock(RequestHandlerInterface::class);
        \Phake::when($this->requestHandlerFactory)
            ->createRequestHandlerByHttpMethod(\Phake::anyParameters())
            ->thenReturn($requestHandler);

        return $requestHandler;
    }

    private function assertRequestHandlerFactory_createRequestHandlerByHttpMethod_isCalledOnceWithMethod(HttpMethodEnum $method): void
    {
        \Phake::verify($this->requestHandlerFactory, \Phake::times(1))
            ->createRequestHandlerByHttpMethod($method);
    }

    private function givenRequestHandler_handleRequest_returnsResponse(
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($requestHandler)
            ->handleRequest(\Phake::anyParameters())
            ->thenReturn($response);

        return $response;
    }

    private function assertRequestHandler_handleRequest_isCalledOnceWithRequest(
        RequestHandlerInterface $requestHandler,
        RequestInterface $request
    ): void {
        \Phake::verify($requestHandler, \Phake::times(1))->handleRequest($request);
    }
}
