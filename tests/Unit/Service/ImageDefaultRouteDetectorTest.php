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
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Service\ImageDefaultRouteDetector;
use Strider2038\ImgCache\Service\Routing\UrlRoute;

class ImageDefaultRouteDetectorTest extends TestCase
{
    private const CONTROLLER_ID = 'imageController';
    private const REQUEST_PATH = '/path';

    /** @test */
    public function getUrlRoute_givenRequest_urlRouteCreatedAndReturned(): void
    {
        $detector = new ImageDefaultRouteDetector();
        $request = \Phake::mock(RequestInterface::class);
        $uri = $this->givenRequest_getUri_returnsUri($request);
        $this->givenUri_getPath_returnsPath($uri, self::REQUEST_PATH);

        $route = $detector->getUrlRoute($request);

        $this->assertInstanceOf(UrlRoute::class, $route);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertEquals(self::CONTROLLER_ID, $route->getControllerId());
        $this->assertEquals(self::REQUEST_PATH, $route->getUrl());
    }

    private function givenRequest_getUri_returnsUri(RequestInterface $request): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($request)->getUri()->thenReturn($uri);

        return $uri;
    }

    private function givenUri_getPath_returnsPath(UriInterface $uri, string $path): void
    {
        \Phake::when($uri)->getPath()->thenReturn($path);
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }
}
