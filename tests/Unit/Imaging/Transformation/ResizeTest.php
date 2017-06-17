<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\Resize;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTest extends TestCase
{

    /**
     * @dataProvider incorrectParamsProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionCode 400
     */
    public function testConstruct_IncorrectWidthHeigthOrMode_ExceptionThrown($width, $height, $mode): void
    {
        new Resize($width, $height, $mode);
    }

    public function incorrectParamsProvider(): array
    {
        return [
            [19, 100, Resize::MODE_FIT_IN],
            [1501, 100, Resize::MODE_FIT_IN],
            [100, 19, Resize::MODE_FIT_IN],
            [100, 1501, Resize::MODE_FIT_IN],
            [100, 100, 'unknown'],
        ];
    }
    
    public function testConstruct_ModeIsNotSet_DefaultModeReturned(): void
    {
        $resize = new Resize(100, 200);
        $this->assertEquals(100, $resize->getWidth());
        $this->assertEquals(200, $resize->getHeigth());
        $this->assertEquals(Resize::MODE_STRETCH, $resize->getMode());
    }
    
    public function testConstruct_HeightAndModeAreNotSet_DefaultsReturned(): void
    {
        $resize = new Resize(100);
        $this->assertEquals(100, $resize->getWidth());
        $this->assertEquals(100, $resize->getHeigth());
        $this->assertEquals(Resize::MODE_STRETCH, $resize->getMode());
    }
}
