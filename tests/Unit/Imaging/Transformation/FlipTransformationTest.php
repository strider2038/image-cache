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
use Strider2038\ImgCache\Imaging\Transformation\FlipTransformation;
use PHPUnit\Framework\TestCase;

class FlipTransformationTest extends TestCase
{
    /** @test */
    public function apply_givenImageTransformer_imageFlipped(): void
    {
        $imageTransformer = \Phake::mock(ImageTransformerInterface::class);
        $this->givenImageTransformer_flip_returnsItself($imageTransformer);
        $transformation = new FlipTransformation();

        $transformation->apply($imageTransformer);

        $this->assertImageTransformer_flip_isCalledOnce($imageTransformer);
    }

    private function givenImageTransformer_flip_returnsItself(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::when($imageTransformer)->flip()->thenReturn($imageTransformer);
    }

    private function assertImageTransformer_flip_isCalledOnce(ImageTransformerInterface $imageTransformer): void
    {
        \Phake::verify($imageTransformer, \Phake::times(1))->flip();
    }
}
