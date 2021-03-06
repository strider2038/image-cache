<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\NotAllowedRequestHandler;

class NotAllowedRequestHandlerTest extends TestCase
{
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\NotAllowedException
     * @expectedExceptionCode 405
     * @expectedExceptionMessage Method not allowed
     */
    public function handleRequest_givenRequest_methodNotAllowedExceptionThrown(): void
    {
        $requestHandler = new NotAllowedRequestHandler();
        $request = \Phake::mock(RequestInterface::class);

        $requestHandler->handleRequest($request);
    }
}
