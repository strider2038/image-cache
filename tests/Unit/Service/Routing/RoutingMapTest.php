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
use Strider2038\ImgCache\Service\Routing\RoutingMap;
use Strider2038\ImgCache\Service\Routing\RoutingMapPath;

class RoutingMapTest extends TestCase
{
    /** @test */
    public function construct_givenRoutingMapPath_pathIsSet(): void
    {
        $path = new RoutingMapPath('prefix', 'controller');

        $map = new RoutingMap([$path]);

        $this->assertEquals($path, $map->first());
    }
}
