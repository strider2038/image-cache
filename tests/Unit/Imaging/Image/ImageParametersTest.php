<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Image\ImageParameters;

class ImageParametersTest extends TestCase
{
    private const QUALITY_VALUE_DEFAULT = 85;

    /** @test */
    public function getQuality_classConstructed_returnedValuesHasDefaultValue(): void
    {
        $parameters = new ImageParameters();

        $result = $parameters->getQuality();

        $this->assertEquals(self::QUALITY_VALUE_DEFAULT, $result);
    }

    /**
     * @test
     * @dataProvider getValidQualityValues
     */
    public function setQuality_validQualityValueIsSet_returnedValuesMatchesSetValue(int $quality): void
    {
        $parameters = new ImageParameters();

        $parameters->setQuality($quality);

        $result = $parameters->getQuality();
        $this->assertEquals($quality, $result);
    }

    public function getValidQualityValues(): array
    {
        return [
            [15],
            [100],
        ];
    }

    /**
     * @test
     * @dataProvider getInvalidQualityValues
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Quality value must be between 15 and 100
     */
    public function setQuality_invalidQualityValueIsSet_invalidValueExceptionThrown(int $quality): void
    {
        $parameters = new ImageParameters();

        $parameters->setQuality($quality);
    }

    public function getInvalidQualityValues(): array
    {
        return [
            [14],
            [101],
        ];
    }
}
