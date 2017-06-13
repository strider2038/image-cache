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
use Strider2038\ImgCache\Imaging\Image;
use Strider2038\ImgCache\Imaging\Transformation\Quality;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class QualityTest extends TestCase
{

    /**
     * @dataProvider incorrectValueProvider
     * @expectedException \Strider2038\ImgCache\Exception\InvalidImageException
     * @expectedExceptionMessage Wrong value for quality transformation
     * @expectedExceptionCode 400
     */
    public function testConstruct_IncorrectValue_ExceptionThrown(int $value): void
    {
        new Quality($value);
    }
    
    public function incorrectValueProvider(): array
    {
        return [
            [10],
            [101],
        ];
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\ApplicationException
     * @expectedExceptionMessage This transformation cannot be applied to image
     * @expectedExceptionCode 500
     */
    public function testApply_Created_ExceptionThrown()
    {
        $image = new class extends Image {
            public function __construct() {}
        };
        $quality = new Quality(50);
        $quality->apply($image);
    }
    
    /**
     * @dataProvider possibleValueProvider
     */
    public function testConstruct_PossibleValue_ValueReturned(int $value): void
    {
        $quality = new Quality($value);
        $this->assertEquals($value, $quality->getValue());
    }
    
    public function possibleValueProvider(): array
    {
        return [
            [15],
            [51],
            [100],
        ];
    }

}
