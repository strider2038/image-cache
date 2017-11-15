<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core\Http;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\Request;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Enum\HttpMethodEnum;

class RequestTest extends TestCase
{
    /** @test */
    public function construct_givenMethodAndUri_methodAndUriIsSet(): void
    {
        $method = \Phake::mock(HttpMethodEnum::class);
        $uri = \Phake::mock(UriInterface::class);

        $request = new Request($method, $uri);

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($uri, $request->getUri());
    }

    /** @test */
    public function withUri_givenRequestWithMethodAndUri_requestWithNewUriReturned(): void
    {
        $method = \Phake::mock(HttpMethodEnum::class);
        $uri = \Phake::mock(UriInterface::class);
        $request = new Request($method, $uri);
        $newUri = \Phake::mock(UriInterface::class);

        $requestWithNewUri = $request->withUri($newUri);

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($uri, $request->getUri());
        $this->assertSame($method, $requestWithNewUri->getMethod());
        $this->assertSame($newUri, $requestWithNewUri->getUri());
    }
}
