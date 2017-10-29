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

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\Rectangle;

class RectangleTest extends TestCase
{
    private const WIDTH = 1;
    private const HEIGHT = 2;
    private const LEFT = 3;
    private const TOP = 4;

    /** @test */
    public function construct_givenParameters_parametersAreSet(): void
    {
        $rectangle = new Rectangle(self::WIDTH, self::HEIGHT, self::LEFT, self::TOP);

        $this->assertEquals(self::WIDTH, $rectangle->getWidth());
        $this->assertEquals(self::HEIGHT, $rectangle->getHeight());
        $this->assertEquals(self::LEFT, $rectangle->getLeft());
        $this->assertEquals(self::TOP, $rectangle->getTop());
    }
}
