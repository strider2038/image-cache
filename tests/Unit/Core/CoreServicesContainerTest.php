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
use Strider2038\ImgCache\Core\ServiceLoaderInterface;

class CoreServicesContainerTest extends TestCase
{
    /** @test */
    public function construct_givenServices_servicesSetAndAvailable(): void
    {
        $logger = \Phake::mock(LoggerInterface::class);
        $serviceLoader = \Phake::mock(ServiceLoaderInterface::class);

        $container = new CoreServicesContainer($logger, $serviceLoader);

        $this->assertSame($logger, $container->getLogger());
        $this->assertSame($serviceLoader, $container->getServiceLoader());
    }
}
