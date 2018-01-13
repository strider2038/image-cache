<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use Strider2038\ImgCache\Imaging\Processing\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    private const X = 1;
    private const Y = 2;

    /** @test */
    public function construct_givenXAndY_xAndYAreProperlySet(): void
    {
        $point = new Point(self::X, self::Y);

        $this->assertEquals(self::X, $point->getX());
        $this->assertEquals(self::Y, $point->getY());
    }
}
