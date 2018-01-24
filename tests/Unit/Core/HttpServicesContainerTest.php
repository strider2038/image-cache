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
use Strider2038\ImgCache\Core\Http\RequestHandlerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\HttpServicesContainer;

class HttpServicesContainerTest extends TestCase
{
    /** @test */
    public function construct_givenServices_servicesSetAndAvailable(): void
    {
        $request = \Phake::mock(RequestInterface::class);
        $requestHandler = \Phake::mock(RequestHandlerInterface::class);
        $responseSender = \Phake::mock(ResponseSenderInterface::class);

        $container = new HttpServicesContainer(
            $request,
            $requestHandler,
            $responseSender
        );

        $this->assertSame($request, $container->getRequest());
        $this->assertSame($requestHandler, $container->getRequestHandler());
        $this->assertSame($responseSender, $container->getResponseSender());
    }
}
