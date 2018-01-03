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
use Strider2038\ImgCache\Imaging\Image\ImageParametersFactory;

class ImageParametersFactoryTest extends TestCase
{
    private const QUALITY = 51;

    public function testCreate_GivenQuality_SaveOptionsWithInjectedPropertiesIsReturned(): void
    {
        $imageParametersFactory = $this->createImageParametersFactory();
        $imageParametersFactory->setQuality(self::QUALITY);

        $imageParameters = $imageParametersFactory->createImageParameters();

        $this->assertInstanceOf(ImageParameters::class, $imageParameters);
        $this->assertEquals(self::QUALITY, $imageParameters->getQuality());
    }

    public function testGetQuality_GivenQuality_QualityValueIsReturned(): void
    {
        $imageParametersFactory = $this->createImageParametersFactory();
        $imageParametersFactory->setQuality(self::QUALITY);

        $quality = $imageParametersFactory->getQuality();

        $this->assertEquals(self::QUALITY, $quality);
    }

    private function createImageParametersFactory(): ImageParametersFactory
    {
        return new ImageParametersFactory();
    }
}
