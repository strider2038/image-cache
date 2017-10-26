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
use Strider2038\ImgCache\Imaging\Processing\Size;

class SizeTest extends TestCase
{
    private const WIDTH = 1;
    private const HEIGHT = 2;

    /** @test */
    public function construct_givenWidthAndHeight_parametersAreSet(): void
    {
        $size = new Size(self::WIDTH, self::HEIGHT);

        $this->assertEquals(self::WIDTH, $size->getWidth());
        $this->assertEquals(self::HEIGHT, $size->getHeight());
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Width or height cannot be less than or equal to 0
     */
    public function construct_givenInvalidWidthAndHeight_exceptionThrown(): void
    {
        new Size(0, 0);
    }
}
