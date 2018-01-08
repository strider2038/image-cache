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
    private const DEFAULT_QUALITY = 85;

    /** @test */
    public function construct_givenQuality_imageParametersWithSpecifiedValuesReturned(): void
    {
        $imageParametersFactory = new ImageParametersFactory(self::QUALITY);

        $imageParameters = $imageParametersFactory->createImageParameters();

        $this->assertInstanceOf(ImageParameters::class, $imageParameters);
        $this->assertEquals(self::QUALITY, $imageParameters->getQuality());
    }

    /** @test */
    public function construct_noParameters_imageParametersWithDefaultValuesReturned(): void
    {
        $imageParametersFactory = new ImageParametersFactory();

        $imageParameters = $imageParametersFactory->createImageParameters();

        $this->assertInstanceOf(ImageParameters::class, $imageParameters);
        $this->assertEquals(self::DEFAULT_QUALITY, $imageParameters->getQuality());
    }
}
