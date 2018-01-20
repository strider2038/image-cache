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
        $sources = $configuration->getSourceCollection();
        $this->assertCount(3, $sources);
    }
}
