<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Processing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

class SaveOptionsTest extends TestCase
{
    const QUALITY_VALUE_DEFAULT = 85;

    public function testGetQuality_ClassConstructed_ReturnedValuesHasDefaultValue(): void
    {
        $saveOptions = new SaveOptions();

        $result = $saveOptions->getQuality();

        $this->assertEquals(self::QUALITY_VALUE_DEFAULT, $result);
    }

    /**
     * @dataProvider getValidQualityValues
     */
    public function testSetQuality_ValidQualityValueIsSet_ReturnedValuesMatchesSetValue(int $quality): void
    {
        $saveOptions = new SaveOptions();

        $saveOptions->setQuality($quality);

        $result = $saveOptions->getQuality();
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
     * @dataProvider getInvalidQualityValues
     * @expectedException \Strider2038\ImgCache\Exception\InvalidValueException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Quality value must be between 15 and 100
     */
    public function testSetQuality_InvalidQualityValueIsSet_InvalidValueExceptionThrown(int $quality): void
    {
        $saveOptions = new SaveOptions();

        $saveOptions->setQuality($quality);
    }

    public function getInvalidQualityValues(): array
    {
        return [
            [14],
            [101],
        ];
    }
}
