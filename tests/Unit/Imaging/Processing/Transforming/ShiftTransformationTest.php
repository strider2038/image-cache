<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing\Transforming;

use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Processing\PointInterface;
use Strider2038\ImgCache\Imaging\Processing\Transforming\ShiftTransformation;
use PHPUnit\Framework\TestCase;

class ShiftTransformationTest extends TestCase
{
    /** @test */
    public function construct_givenPoint_pointIsAccessible(): void
    {
        $point = \Phake::mock(PointInterface::class);

        $transformation = new ShiftTransformation($point);

        $this->assertSame($point, $transformation->getParameters());
    }

    /** @test */
    public function apply_givenPointAndTransformer_transformerShiftsImageByParameters(): void
    {
        $point = \Phake::mock(PointInterface::class);
        $transformation = new ShiftTransformation($point);
        $transformer = \Phake::mock(ImageTransformerInterface::class);
        $this->givenImageTransformer_shift_returnsItself($transformer);

        $transformation->apply($transformer);

        $this->assertImageTransformer_shift_isCalledOnceWithPoint($transformer, $point);
    }

    private function givenImageTransformer_shift_returnsItself(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::when($imageTransformer)->shift(\Phake::anyParameters())->thenReturn($imageTransformer);
    }

    private function assertImageTransformer_shift_isCalledOnceWithPoint(
        ImageTransformerInterface $transformer,
        PointInterface $point
    ): void {
        \Phake::verify($transformer, \Phake::times(1))->shift($point);
    }
}
