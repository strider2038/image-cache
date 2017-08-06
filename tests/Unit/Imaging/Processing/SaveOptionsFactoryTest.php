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
use Strider2038\ImgCache\Imaging\Processing\SaveOptionsFactory;

class SaveOptionsFactoryTest extends TestCase
{
    const QUALITY = 51;

    public function testCreate_GivenQuality_SaveOptionsWithInjectedPropertiesIsReturned(): void
    {
        $saveOptionsFactory = $this->createSaveOptionsFactory();
        $saveOptionsFactory->setQuality(self::QUALITY);

        $saveOptions = $saveOptionsFactory->create();

        $this->assertInstanceOf(SaveOptions::class, $saveOptions);
        $this->assertEquals(self::QUALITY, $saveOptions->getQuality());
    }

    public function testGetQuality_GivenQuality_QualityValueIsReturned(): void
    {
        $saveOptionsFactory = $this->createSaveOptionsFactory();
        $saveOptionsFactory->setQuality(self::QUALITY);

        $quality = $saveOptionsFactory->getQuality();

        $this->assertEquals(self::QUALITY, $quality);
    }

    private function createSaveOptionsFactory(): SaveOptionsFactory
    {
        $saveOptionsFactory = new SaveOptionsFactory();

        return $saveOptionsFactory;
    }
}
