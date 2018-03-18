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
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\RoutingRequestHandler;

class RoutingRequestHandlerTest extends TestCase
{
    /** @var RequestHandlerInterface */
    private $clearAllRequestHandler;
    /** @var RequestHandlerInterface */
    private $intermediateRequestHandler;

    protected function setUp(): void
    {
        $this->clearAllRequestHandler = \Phake::mock(RequestHandlerInterface::class);
        $this->intermediateRequestHandler = \Phake::mock(RequestHandlerInterface::class);
    }

    /**
     * @test
     * @dataProvider uriProvider
     * @param string $uri
     */
    public function handleRequest_givenRequestUriIsRootAndMethodDelete_requestProcessedByClearAllHandler(
        string $uri
    ): void {
        $request = $this->givenDeleteUriRequest($uri);
        $requestHandler = $this->createRoutingRequestHandler();
        $expectedResponse = $this->givenRequestHandler_handleRequest_returnsResponse($this->clearAllRequestHandler);

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequestHandler_handlerRequest_isCalledOnceWithRequest($this->clearAllRequestHandler, $request);
        $this->assertSame($expectedResponse, $response);
    }

    public function uriProvider(): array
    {
        return [
            [''],
            ['/'],
        ];
    }

    /** @test */
    public function handleRequest_givenRequestUriIsImageAndMethodDelete_requestProcessedByIntermediateHandler(): void
    {
        $request = $this->givenDeleteUriRequest('/image.jpg');
        $requestHandler = $this->createRoutingRequestHandler();
        $expectedResponse = $this->givenRequestHandler_handleRequest_returnsResponse($this->intermediateRequestHandler);

        $response = $requestHandler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequestHandler_handlerRequest_isCalledOnceWithRequest($this->intermediateRequestHandler, $request);
        $this->assertSame($expectedResponse, $response);
    }

    private function createRoutingRequestHandler(): RoutingRequestHandler
    {
        return new RoutingRequestHandler(
            $this->clearAllRequestHandler,
            $this->intermediateRequestHandler
        );
    }

    private function givenDeleteUriRequest(string $uri): Request
    {
        return new Request(
            new HttpMethodEnum(HttpMethodEnum::DELETE),
            new Uri($uri)
        );
    }

    private function assertRequestHandler_handlerRequest_isCalledOnceWithRequest(
        RequestHandlerInterface $requestHandler,
        RequestInterface $request
    ): void {
        \Phake::verify($requestHandler, \Phake::times(1))
            ->handleRequest($request);
    }

    private function givenRequestHandler_handleRequest_returnsResponse(
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $mockResponse = \Phake::mock(ResponseInterface::class);
        \Phake::when($requestHandler)
            ->handleRequest(\Phake::anyParameters())
            ->thenReturn($mockResponse);

        return $mockResponse;
    }
}
