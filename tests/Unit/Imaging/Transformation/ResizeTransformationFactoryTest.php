<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Transformation;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Enum\ResizeModeEnum;
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformation;
use Strider2038\ImgCache\Imaging\Transformation\ResizeTransformationFactory;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ResizeTransformationFactoryTest extends TestCase
{
    /**
     * @test
     * @param string $configuration
     * @param int $width
     * @param int $height
     * @param string $mode
     * @dataProvider resizeConfigProvider
     */
    public function create_validConfig_classIsConstructed(
        string $configuration,
        int $width,
        int $height,
        string $mode
    ): void {
        $factory = new ResizeTransformationFactory();

        /** @var ResizeTransformation $resize */
        $resize = $factory->create($configuration);

        $this->assertInstanceOf(ResizeTransformation::class, $resize);
        $this->assertEquals($width, $resize->getParameters()->getWidth());
        $this->assertEquals($height, $resize->getParameters()->getHeight());
        $this->assertEquals($mode, $resize->getParameters()->getMode());
    }

    public function resizeConfigProvider(): array
    {
        return [
            ['100x100f', 100, 100, ResizeModeEnum::FIT_IN],
            ['500x200s', 500, 200, ResizeModeEnum::STRETCH],
            ['50x1000w', 50, 1000, ResizeModeEnum::PRESERVE_WIDTH],
            ['300x200h', 300, 200, ResizeModeEnum::PRESERVE_HEIGHT],
            ['400X250H', 400, 250, ResizeModeEnum::PRESERVE_HEIGHT],
            ['200x300', 200, 300, ResizeModeEnum::STRETCH],
            ['200f', 200, 200, ResizeModeEnum::FIT_IN],
            ['150', 150, 150, ResizeModeEnum::STRETCH],
        ];
    }

    /**
     * @test
     * @param string $configuration
     * @dataProvider resizeInvalidConfigProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for resize transformation
     */
    public function create_invalidConfig_exceptionThrown(string $configuration): void
    {
        $factory = new ResizeTransformationFactory();

        $factory->create($configuration);
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
