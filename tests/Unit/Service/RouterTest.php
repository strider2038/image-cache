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
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Uri;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Service\Router;
use Strider2038\ImgCache\Service\Routing\UrlRoute;
use Strider2038\ImgCache\Service\Routing\UrlRouteDetectorInterface;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RouterTest extends TestCase
{
    use LoggerTrait;

    private const DEFAULT_CONTROLLER_ID = 'imageController';
    private const CONTROLLER_ID = 'controllerId';
    private const ACTION_ID_GET = 'get';
    private const REQUEST_URL = '/a.jpg';
    private const REQUEST_METHOD_GET = 'GET';

    /** @var RequestInterface */
    private $request;

    /** @var UrlRouteDetectorInterface */
    private $urlRouteDetector;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp(): void
    {
        $this->request = \Phake::mock(RequestInterface::class);
        $this->urlRouteDetector = \Phake::mock(UrlRouteDetectorInterface::class);
        $this->logger = $this->givenLogger();
    }

    /**
     * @test
     * @param string $requestMethod
     * @param string $actionName
     * @dataProvider requestMethodsProvider
     */
    public function getRoute_requestMethodIsSet_controllerAndActionReturned(
        string $requestMethod,
        string $actionName
    ): void {
        $router = $this->createRouter();
        $this->givenRequest_getMethod_returns($requestMethod);
        $this->givenRequest_getUri_getPath_returns(self::REQUEST_URL);
        $this->givenUrlRouteDetector_getUrlRoute_returnsUrlRouteWithControllerIdAndUri();
        $processedRequest = $this->givenRequest_withUri_returnsProcessedRequest();

        $route = $router->getRoute($this->request);
        
        $this->assertInstanceOf(Route::class, $route);
        $this->assertUrlRouteDetector_getUrlRoute_isCalledOnceWithRequest($this->request);
        $this->assertEquals(self::CONTROLLER_ID, $route->getControllerId());
        $this->assertEquals($actionName, $route->getActionId());
        $this->assertRequest_withUri_isCalledOnceWithUriWithPath(self::REQUEST_URL);
        $this->assertSame($processedRequest, $route->getRequest());
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    public function requestMethodsProvider(): array
    {
        return [
            [self::REQUEST_METHOD_GET, self::ACTION_ID_GET],
            ['POST', 'create'],
            ['PUT', 'replace'],
            ['DELETE', 'delete'],
        ];
    }

    /** @test */
    public function getRoute_givenRequestAndDefaultRouteDetector_defaultRouteReturned(): void
    {
        $router = new Router();
        $this->givenRequest_getMethod_returns(self::REQUEST_METHOD_GET);
        $this->givenRequest_getUri_getPath_returns(self::REQUEST_URL);
        $processedRequest = $this->givenRequest_withUri_returnsProcessedRequest();

        $route = $router->getRoute($this->request);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals(self::DEFAULT_CONTROLLER_ID, $route->getControllerId());
        $this->assertEquals(self::ACTION_ID_GET, $route->getActionId());
        $this->assertRequest_withUri_isCalledOnceWithUriWithPath(self::REQUEST_URL);
        $this->assertSame($processedRequest, $route->getRequest());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Route not found
     */
    public function getRoute_requestMethodIsNotSet_exceptionThrown(): void
    {
        $router = $this->createRouter();
        $this->givenRequest_getUri_getPath_returns('');
        
        $router->getRoute($this->request);
    }

    private function createRouter(): Router
    {
        $router = new Router($this->urlRouteDetector);
        $router->setLogger($this->logger);

        return $router;
    }

    private function givenRequest_getMethod_returns(string $requestMethod): void
    {
        \Phake::when($this->request)
            ->getMethod()
            ->thenReturn(new HttpMethodEnum($requestMethod));
    }

    private function givenRequest_getUri_getPath_returns(string $value): void
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($this->request)->getUri()->thenReturn($uri);
        \Phake::when($uri)->getPath()->thenReturn($value);
    }

    private function assertRequest_withUri_isCalledOnceWithUriWithPath(string $expectedPath): void
    {
        \Phake::verify($this->request, \Phake::times(1))->withUri(\Phake::capture($uri));
        /** @var UriInterface $uri */
        $this->assertEquals($expectedPath, $uri->getPath());
    }

    private function givenRequest_withUri_returnsProcessedRequest(): RequestInterface
    {
        $processedRequest = \Phake::mock(RequestInterface::class);
        \Phake::when($this->request)->withUri(\Phake::anyParameters())->thenReturn($processedRequest);
        \Phake::when($processedRequest)->getUri()->thenReturn(\Phake::mock(UriInterface::class));

        return $processedRequest;
    }

    private function assertUrlRouteDetector_getUrlRoute_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($this->urlRouteDetector, \Phake::times(1))->getUrlRoute($request);
    }

    private function givenUrlRouteDetector_getUrlRoute_returnsUrlRouteWithControllerIdAndUri(): void
    {
        $urlRoute = new UrlRoute(self::CONTROLLER_ID, new Uri(self::REQUEST_URL));
        \Phake::when($this->urlRouteDetector)->getUrlRoute(\Phake::anyParameters())->thenReturn($urlRoute);
    }
}
