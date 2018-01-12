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

use Strider2038\ImgCache\Imaging\Processing\ImageTransformerInterface;
use Strider2038\ImgCache\Imaging\Transformation\RotatingTransformation;
use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\RotationParameters;

class RotatingTransformationTest extends TestCase
{
    private const ROTATION_DEGREE = 91;

    /** @test */
    public function apply_givenRotationDegree_transformerRotatesImageByDegree(): void
    {
        $parameters = new RotationParameters(self::ROTATION_DEGREE);
        $transformation = new RotatingTransformation($parameters);
        $transformer = \Phake::mock(ImageTransformerInterface::class);
        $this->givenImageTransformer_rotate_returnsItself($transformer);

        $transformation->apply($transformer);

        $this->assertImageTransformer_rotate_isCalledOnceWithDegree($transformer, self::ROTATION_DEGREE);
    }

    private function givenImageTransformer_rotate_returnsItself(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::when($imageTransformer)->rotate(\Phake::anyParameters())->thenReturn($imageTransformer);
    }

    private function assertImageTransformer_rotate_isCalledOnceWithDegree(
        ImageTransformerInterface $transformer,
        float $degree
    ): void {
        \Phake::verify($transformer, \Phake::times(1))->rotate($degree);
    }
}
