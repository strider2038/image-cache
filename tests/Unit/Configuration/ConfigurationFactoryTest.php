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
use Strider2038\ImgCache\Configuration\ConfigurationFactory;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactoryInterface;

class ConfigurationFactoryTest extends TestCase
{
    private const ACCESS_CONTROL_TOKEN = 'access-control-token';
    private const CACHED_IMAGE_QUALITY = 55;
    private const SOURCE_CONFIGURATION = [
        'source_configuration'
    ];

    /** @var \Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactoryInterface */
    private $imageSourceFactory;

    protected function setUp(): void
    {
        $this->imageSourceFactory = \Phake::mock(\Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactoryInterface::class);
    }

    /** @test */
    public function createConfiguration_givenConfigurationArray_configurationClassCreatedAndReturned(): void
    {
        $factory = new ConfigurationFactory($this->imageSourceFactory);
        $imageSource = $this->givenImageSourceFactory_createImageSourceByConfiguration_returnsImageSource();

        $configuration = $factory->createConfiguration([
            'access_control_token' => self::ACCESS_CONTROL_TOKEN,
            'cached_image_quality' => self::CACHED_IMAGE_QUALITY,
            'image_sources' => [
                'source' => self::SOURCE_CONFIGURATION,
            ],
        ]);

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals(self::ACCESS_CONTROL_TOKEN, $configuration->getAccessControlToken());
        $this->assertEquals(self::CACHED_IMAGE_QUALITY, $configuration->getCachedImageQuality());
        $this->assertImageSourceFactory_createImageSourceByConfiguration_isCalledOnceWithConfiguration(
            self::SOURCE_CONFIGURATION
        );
        $this->assertCount(1, $configuration->getSourceCollection());
        $this->assertSame($imageSource, $configuration->getSourceCollection()->first());
    }

    private function givenImageSourceFactory_createImageSourceByConfiguration_returnsImageSource(): \Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource
    {
        $imageSource = \Phake::mock(\Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource::class);
        \Phake::when($this->imageSourceFactory)
            ->createImageSourceByConfiguration(\Phake::anyParameters())
            ->thenReturn($imageSource);

        return $imageSource;
    }

    private function assertImageSourceFactory_createImageSourceByConfiguration_isCalledOnceWithConfiguration(
        array $configuration
    ): void {
        \Phake::verify($this->imageSourceFactory, \Phake::times(1))
            ->createImageSourceByConfiguration($configuration);
    }
}
