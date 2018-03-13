<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Services;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ConfigurationFactory;
use Strider2038\ImgCache\Configuration\ConfigurationLoader;
use Strider2038\ImgCache\Configuration\ConfigurationLoaderInterface;
use Strider2038\ImgCache\Configuration\ConfigurationTreeGenerator;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactory;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Utility\ConfigurationFileParserInterface;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationLoaderTest extends TestCase
{
    private const CONFIGURATION_FILENAME = 'configuration-loader-parameters';
    private const ACCESS_CONTROL_TOKEN = 'test-access-control-token';
    private const CACHED_IMAGE_QUALITY = 65;

    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;
    /** @var ConfigurationFileParserInterface */
    private $configurationFileParser;

    protected function setUp(): void
    {
        $this->configurationFileParser = \Phake::mock(ConfigurationFileParserInterface::class);
        $this->configurationLoader = new ConfigurationLoader(
            $this->configurationFileParser,
            new ConfigurationTreeGenerator(),
            new Processor(),
            new ConfigurationFactory(
                new ImageSourceFactory()
            )
        );
    }

    /** @test */
    public function loadConfiguration_validConfigurationArrayLoaded_configurationCreatedAndReturned(): void
    {
        $configurationArray = $this->givenConfigurationArray();
        $this->givenConfigurationFileParser_parseConfigurationFile_returnsArray($configurationArray);

        $configuration = $this->configurationLoader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals(self::ACCESS_CONTROL_TOKEN, $configuration->getAccessControlToken());
        $this->assertEquals(self::CACHED_IMAGE_QUALITY, $configuration->getCachedImageQuality());
        $this->assertImageSourcesInConfigurationAreValid($configuration);
    }

    /** @test */
    public function loadConfiguration_validConfigurationArrayWithEmptyDirectoriesLoaded_configurationCreatedAndReturned(): void
    {
        $configurationArray = [
            'image_sources' => [
                'filesystem_source' => [
                    'type' => 'filesystem',
                    'cache_directory' => '/',
                    'storage_directory' => '/',
                ],
            ]
        ];
        $this->givenConfigurationFileParser_parseConfigurationFile_returnsArray($configurationArray);

        $configuration = $this->configurationLoader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);

        /** @var FilesystemImageSource $filesystemSource */
        $filesystemSource = $configuration->getSourceCollection()->first();
        $this->assertEquals('/', $filesystemSource->getCacheDirectory());
        $this->assertEquals('/', $filesystemSource->getStorageDirectory());
    }

    /**
     * @test
     * @dataProvider invalidConfigurationArray
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @param array $configurationArray
     */
    public function loadConfiguration_invalidConfigurationArrayLoaded_invalidConfigurationExceptionThrown(
        array $configurationArray
    ): void {
        $this->givenConfigurationFileParser_parseConfigurationFile_returnsArray($configurationArray);

        $this->configurationLoader->loadConfigurationFromFile(self::CONFIGURATION_FILENAME);
    }

    public function invalidConfigurationArray(): array
    {
        return [
            [
                [
                    'image_sources' => []
                ],
            ],
            [
                [
                    'image_sources' => [
                        'filesystem_source' => [
                            'type' => 'filesystem',
                            'cache_directory' => '',
                        ],
                    ],
                ],
            ],
            [
                [
                    'image_sources' => [
                        'filesystem_source' => [
                            'type' => 'filesystem',
                            'cache_directory' => '/$invalid_directory',
                        ],
                    ],
                ],
            ],
            [
                [
                    'image_sources' => [
                        'filesystem_source' => [
                            'type' => 'filesystem',
                            'cache_directory' => '/valid_directory',
                            'storage_directory' => '/$invalid_directory',
                        ],
                    ],
                ],
            ],
            [
                [
                    'image_sources' => [
                        'webdav_source' => [
                            'type' => 'webdav',
                            'cache_directory' => '/valid_directory',
                            'driver_uri' => '',
                            'oauth_token' => '',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function givenConfigurationArray(): array
    {
        return [
            'access_control_token' => self::ACCESS_CONTROL_TOKEN,
            'cached_image_quality' => self::CACHED_IMAGE_QUALITY,
            'image_sources' => [
                'filesystem_source' => [
                    'type' => 'filesystem',
                    'cache_directory' => '/fs',
                    'storage_directory' => '/image-storage',
                ],
                'yandex_disk_source' => [
                    'type' => 'webdav',
                    'cache_directory' => '/yd',
                    'storage_directory' => '/imgcache',
                    'driver_uri' => 'https://webdav.yandex.ru/v1',
                    'oauth_token' => 'test-oauth-token',
                ],
                'yandex_map_source' => [
                    'type' => 'geomap',
                    'cache_directory' => '/map',
                    'driver' => 'yandex',
                    'driver_uri' => 'https://example.com',
                    'oauth_token' => 'token',
                ],
            ],
        ];
    }

    private function givenConfigurationFileParser_parseConfigurationFile_returnsArray(array $configurationArray): void
    {
        \Phake::when($this->configurationFileParser)
            ->parseConfigurationFile(\Phake::anyParameters())
            ->thenReturn($configurationArray);
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
