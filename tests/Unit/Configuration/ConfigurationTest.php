<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;

class ConfigurationTest extends TestCase
{
    private const ACCESS_CONTROL_TOKEN = 'access_control_token';
    private const CACHED_IMAGE_QUALITY = 80;

    /** @test */
    public function construct_givenConfigurationParameters_parametersSetAndAccessible(): void
    {
        $sourceCollection = \Phake::mock(ImageSourceCollection::class);

        $configuration = new Configuration(
            self::ACCESS_CONTROL_TOKEN,
            self::CACHED_IMAGE_QUALITY,
            $sourceCollection
        );

        $this->assertEquals(self::ACCESS_CONTROL_TOKEN, $configuration->getAccessControlToken());
        $this->assertEquals(self::CACHED_IMAGE_QUALITY, $configuration->getCachedImageQuality());
        $this->assertSame($sourceCollection, $configuration->getSourceCollection());
    }
}
