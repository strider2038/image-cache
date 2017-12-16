<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Imaging\Parsing;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Parsing\SaveOptionsConfigurator;
use Strider2038\ImgCache\Imaging\Processing\SaveOptions;

class SaveOptionsConfiguratorTest extends TestCase
{
    private const SAVE_OPTIONS_QUALITY = 51;
    private const NOT_SUPPORTED_CONFIGURATION = 'a';
    private const INVALID_CONFIGURATION = 'qa';
    private const VALID_CONFIGURATION = 'q51';

    /** @test */
    public function updateSaveOptionsByConfiguration_configurationIsNotSupported_saveOptionsValueNotModified(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->updateSaveOptionsByConfiguration($saveOptions, self::NOT_SUPPORTED_CONFIGURATION);

        \Phake::verifyNoInteraction($saveOptions);
    }

    private function givenSaveOptions(): SaveOptions
    {
        return \Phake::mock(SaveOptions::class);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid configuration for quality transformation
     */
    public function updateSaveOptionsByConfiguration_configurationIsInvalid_exceptionThrown(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->updateSaveOptionsByConfiguration($saveOptions, self::INVALID_CONFIGURATION);
    }

    /** @test */
    public function updateSaveOptionsByConfiguration_configurationIsValid_valueIsSetToSaveOptions(): void
    {
        $saveOptions = $this->givenSaveOptions();
        $configurator = new SaveOptionsConfigurator();

        $configurator->updateSaveOptionsByConfiguration($saveOptions, self::VALID_CONFIGURATION);

        \Phake::verify($saveOptions, \Phake::times(1))->setQuality(self::SAVE_OPTIONS_QUALITY);
    }
}
