<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Routing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Strider2038\ImgCache\Service\Routing\RoutingPathCollection;
use Strider2038\ImgCache\Service\Routing\UrlRouteDetector;

class UrlRouteDetectorTest extends TestCase
{
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Routing map cannot be empty
     */
    public function construct_givenRoutingMapIsEmpty_exceptionThrown(): void
    {
        $map = new RoutingPathCollection();

        new UrlRouteDetector($map);
    }

    /**
     * @test
     * @dataProvider validUrlMapAndUrlRouteParametersProvider
     * @param RoutingPathCollection $map
     * @param string $requestUrl
     * @param string $controllerId
     * @param string $routeUrl
     */
    public function getUrlRoute_givenRoutingMapAndValidRequestUrl_urlRouteReturned(
        RoutingPathCollection $map,
        string $requestUrl,
        string $controllerId,
        string $routeUrl
    ): void {
        $detector = new UrlRouteDetector($map);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsValue($uri, $requestUrl);

        $route = $detector->getUrlRoute($request);

        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertEquals($controllerId, $route->getControllerId());
        $this->assertEquals($routeUrl, $route->getUrl());
    }

    /**
     * @test
     * @dataProvider invalidUrlMapAndUrlRouteParametersProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Route not found
     * @param RoutingPathCollection $map
     * @param string $requestUrl
     */
    public function getUrlRoute_givenRoutingMapAndInvalidRequestUrl_exceptionThrown(
        RoutingPathCollection $map,
        string $requestUrl
    ): void {
        $detector = new UrlRouteDetector($map);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsValue($uri, $requestUrl);

        $detector->getUrlRoute($request);
    }

    public function validUrlMapAndUrlRouteParametersProvider(): array
    {
        return [
            [
                new RoutingPathCollection([
                    new RoutingPath('/i', 'controller1')
                ]),
                '/i/file.jpg',
                'controller1',
                '/file.jpg',
            ],
            [
                new RoutingPathCollection([
                    new RoutingPath('/i/j', 'controller2')
                ]),
                '/i/j/k/file.jpg',
                'controller2',
                '/k/file.jpg'
            ],
            [
                new RoutingPathCollection([
                    new RoutingPath('/i', 'controller1'),
                    new RoutingPath('/j', 'controller2'),
                ]),
                '/j/image.png',
                'controller2',
                '/image.png',
            ],
        ];
    }

    public function invalidUrlMapAndUrlRouteParametersProvider(): array
    {
        return [
            [
                new RoutingPathCollection([
                    new RoutingPath('/i', 'controller1')
                ]),
                '/ij/file.jpg'
            ],
            [
                new RoutingPathCollection([
                    new RoutingPath('/i', 'controller1')
                ]),
                'file.jpg'
            ],
        ];
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function givenRequest_getUri_returnsUri(RequestInterface $request): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($request)->getUri()->thenReturn($uri);

        return $uri;
    }

    private function givenUri_getPath_returnsValue(UriInterface $uri, string $requestUrl): void
    {
        \Phake::when($uri)->getPath()->thenReturn($requestUrl);
    }
}
