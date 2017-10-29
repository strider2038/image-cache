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
use Strider2038\ImgCache\Imaging\Transformation\ResizeParameters;

class ResizeParametersTest extends TestCase
{
    private const MIN_VALUE = 20;
    private const MAX_VALUE = 2000;

    /** @test */
    public function construct_givenParameters_parametersAreSet(): void
    {
        $mode = new ResizeModeEnum(ResizeModeEnum::STRETCH);

        $parameters = new ResizeParameters(self::MIN_VALUE, self::MAX_VALUE, $mode);

        $this->assertEquals(self::MIN_VALUE, $parameters->getWidth());
        $this->assertEquals(self::MAX_VALUE, $parameters->getHeight());
        $this->assertEquals(ResizeModeEnum::STRETCH, $parameters->getMode()->getValue());
    }

    /**
     * @dataProvider incorrectParametersProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage of the image must be between
     * @param int $width
     * @param int $height
     */
    public function testConstruct_IncorrectWidthHeightOrMode_ExceptionThrown(int $width, int $height): void
    {
        $mode = new ResizeModeEnum(ResizeModeEnum::STRETCH);

        new ResizeParameters($width, $height, $mode);
    }

    public function incorrectParametersProvider(): array
    {
        return [
            [self::MIN_VALUE - 1, 100],
            [self::MAX_VALUE + 1, 100],
            [100, self::MIN_VALUE - 1],
            [100, self::MAX_VALUE + 1],
        ];
    }
}
