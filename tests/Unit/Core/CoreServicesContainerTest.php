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
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\CoreServicesContainer;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseSenderInterface;
use Strider2038\ImgCache\Core\RequestHandlerInterface;
use Strider2038\ImgCache\Core\ServiceLoaderInterface;

class CoreServicesContainerTest extends TestCase
{
    /** @test */
    public function construct_givenServices_servicesSetAndAvailable(): void
    {
        $logger = \Phake::mock(LoggerInterface::class);
        $serviceLoader = \Phake::mock(ServiceLoaderInterface::class);
        $request = \Phake::mock(RequestInterface::class);
        $requestHandler = \Phake::mock(RequestHandlerInterface::class);
        $responseSender = \Phake::mock(ResponseSenderInterface::class);

        $container = new CoreServicesContainer(
            $logger,
            $serviceLoader,
            $request,
            $requestHandler,
            $responseSender
        );

        $this->assertSame($logger, $container->getLogger());
        $this->assertSame($serviceLoader, $container->getServiceLoader());
        $this->assertSame($request, $container->getRequest());
        $this->assertSame($requestHandler, $container->getRequestHandler());
        $this->assertSame($responseSender, $container->getResponseSender());
    }
}
