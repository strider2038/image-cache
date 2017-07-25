<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging\Parsing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfigurator;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

class SaveOptionsConfiguratorTest extends TestCase
{
    const SAVE_OPTIONS_QUALITY = 51;
    const CONFIG_NOT_SUPPORTED = 'a';
    const CONFIG_INVALID = 'qa';
    const CONFIG_VALID = 'q51';

    public function testConfigure_ConfigIsNotSupported_SaveOptionsValueNotModified(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->configure($saveOptions, self::CONFIG_NOT_SUPPORTED);

        $this->assertSetQualityCalled($saveOptions, 0);
    }

    private function givenSaveOptions(): SaveOptions
    {
        $saveOptions = new class extends SaveOptions
        {
            public $testSetQualityValue;
            public $testSetQualityCount = 0;

            public function setQuality(?int $quality): void
            {
                $this->testSetQualityValue = $quality;
                $this->testSetQualityCount++;
            }
        };

        return $saveOptions;
    }

    private function assertSetQualityCalled(SaveOptions $saveOptions, int $times, int $withValue = null): void
    {
        $this->assertEquals($times, $saveOptions->testSetQualityCount);
        if ($withValue !== null) {
            $this->assertEquals($withValue, $saveOptions->testSetQualityValue);
        }
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for quality transformation
     */
    public function testConfigure_ConfigIsInvalid_ExceptionThrown(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->configure($saveOptions, self::CONFIG_INVALID);
    }

    public function testConfigure_ConfigIsValid_ValueIsSetToSaveOptions(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->configure($saveOptions, self::CONFIG_VALID);

        $this->assertSetQualityCalled($saveOptions, 1, self::SAVE_OPTIONS_QUALITY);
    }
}
