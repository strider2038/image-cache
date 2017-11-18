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
use Strider2038\ImgCache\Service\Routing\RoutingPath;
use Strider2038\ImgCache\Service\Routing\RoutingPathCollection;

class RoutingPathCollectionTest extends TestCase
{
    /** @test */
    public function construct_givenRoutingMapPath_pathIsSet(): void
    {
        $path = new RoutingPath('prefix', 'controller');

        $map = new RoutingPathCollection([$path]);

        $this->assertEquals($path, $map->first());
    }
}
