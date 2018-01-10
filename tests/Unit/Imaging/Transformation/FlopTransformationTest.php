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
use Strider2038\ImgCache\Imaging\Transformation\FlopTransformation;
use PHPUnit\Framework\TestCase;

class FlopTransformationTest extends TestCase
{
    /** @test */
    public function apply_givenImageTransformer_imageFlopped(): void
    {
        $imageTransformer = \Phake::mock(ImageTransformerInterface::class);
        $this->givenImageTransformer_flop_returnsItself($imageTransformer);
        $transformation = new FlopTransformation();

        $transformation->apply($imageTransformer);

        $this->assertImageTransformer_flop_isCalledOnce($imageTransformer);
    }

    private function givenImageTransformer_flop_returnsItself(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::when($imageTransformer)->flop()->thenReturn($imageTransformer);
    }

    private function assertImageTransformer_flop_isCalledOnce(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::verify($imageTransformer, \Phake::times(1))->flop();
    }
}
