<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional;

use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

class ConfigurationLoaderTest extends FunctionalTestCase
{
    private const ACCESS_CONTROL_TOKEN = 'test-access-control-token';
    private const CACHED_IMAGE_QUALITY = 65;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    protected function setUp(): void
    {
        $container = $this->loadContainer('configuration-loader.yml');
        $this->configurationLoader = $container->get('configuration_loader');
    }

    /** @test */
    public function loadConfiguration_noParameters_validConfigurationLoadedAndReturned(): void
    {
        $configuration = $this->configurationLoader->loadConfiguration();

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals(self::ACCESS_CONTROL_TOKEN, $configuration->getAccessControlToken());
        $this->assertEquals(self::CACHED_IMAGE_QUALITY, $configuration->getCachedImageQuality());
        $this->assertImageSourcesInConfigurationAreValid($configuration);
    }

    private function assertImageSourcesInConfigurationAreValid(Configuration $configuration): void
    {
        $sources = $configuration->getSourceCollection();
        $this->assertCount(3, $sources);
        $this->assertFilesystemImageSourceIsValid($sources->first());
        $this->assertWebDAVImageSourceIsValid($sources[1]);
        $this->assertGeoMapImageSourceIsValid($sources->last());
    }

    private function assertFilesystemImageSourceIsValid(FilesystemImageSource $imageSource): void
    {
        $this->assertEquals('/fs/', $imageSource->getCacheDirectory());
        $this->assertEquals('/image-storage/', $imageSource->getStorageDirectory());
        $this->assertEquals('thumbnail', $imageSource->getProcessorType());
    }

    private function assertWebDAVImageSourceIsValid(WebDAVImageSource $imageSource): void
    {
        $this->assertEquals('/yd/', $imageSource->getCacheDirectory());
        $this->assertEquals('/imgcache/', $imageSource->getStorageDirectory());
        $this->assertEquals('https://webdav.yandex.ru/v1', $imageSource->getDriverUri());
        $this->assertEquals('test-oauth-token', $imageSource->getOauthToken());
    }

    private function assertGeoMapImageSourceIsValid(GeoMapImageSource $imageSource): void
    {
        $this->assertEquals('/map/', $imageSource->getCacheDirectory());
        $this->assertEquals('yandex', $imageSource->getDriver());
        $this->assertEquals('', $imageSource->getApiKey());
    }
}
