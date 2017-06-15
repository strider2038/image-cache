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
use Strider2038\ImgCache\Imaging\Transformation\{
    Resize,
    ResizeBuilder
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeBuilderTest extends TestCase
{

    /**
     * @dataProvider resizeConfigProvider
     */
    public function testBuild_ValidConfig_ClassIsConstructed($config, $width, $height, $mode): void
    {
        $builder = new ResizeBuilder();
        $resize = $builder->build($config);
        $this->assertInstanceOf(Resize::class, $resize);
        $this->assertEquals($width, $resize->getWidth());
        $this->assertEquals($height, $resize->getHeigth());
        $this->assertEquals($mode, $resize->getMode());
    }
    
    public function resizeConfigProvider(): array
    {
        return [
            ['100x100f', 100, 100, Resize::MODE_FIT_IN],
            ['500x200s', 500, 200, Resize::MODE_STRETCH],
            ['50x1000w', 50, 1000, Resize::MODE_PRESERVE_WIDTH],
            ['300x200h', 300, 200, Resize::MODE_PRESERVE_HEIGHT],
            ['400X250H', 400, 250, Resize::MODE_PRESERVE_HEIGHT],
            ['200x300', 200, 300, Resize::MODE_STRETCH],
            ['200f', 200, 200, Resize::MODE_FIT_IN],
            ['150', 150, 150, Resize::MODE_STRETCH],
        ];
    }
    
    /**
     * @dataProvider resizeInvalidConfigProvider
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for resize transformation
     */
    public function testBuild_InvalidConfig_ExceptionThrown($config): void
    {
        $builder = new ResizeBuilder();
        $builder->build($config);
    }
    
    public function resizeInvalidConfigProvider(): array
    {
        return [
            ['1500k'],
            ['100x15i'],
            ['100x156sp'],
            ['100x'],
        ];
    }

}
