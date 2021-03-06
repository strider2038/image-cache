<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Configuration\ImageSource;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Configuration\ImageSource\FilesystemImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceFactory;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Enum\ImageProcessorTypeEnum;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;

class ImageSourceFactoryTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache-directory/';

    /**
     * @test
     * @param array $configurationArray
     * @param string $imageSourceClass
     * @dataProvider configurationAndImageSourceClassProvider
     */
    public function createImageSourceByConfiguration_givenConfigurationArray_concreteImageSourceCreatedAndReturned(
        array $configurationArray,
        string $imageSourceClass
    ): void {
        $factory = new ImageSourceFactory();

        $imageSource = $factory->createImageSourceByConfiguration($configurationArray);

        $this->assertInstanceOf($imageSourceClass, $imageSource);
        $this->assertEquals(self::CACHE_DIRECTORY, $imageSource->getCacheDirectory());
    }

    public function configurationAndImageSourceClassProvider(): array
    {
        return [
            [
                [
                    'type' => 'filesystem',
                    'cache_directory' => new DirectoryName(self::CACHE_DIRECTORY),
                    'storage_directory' => new DirectoryName(''),
                    'processor_type' => new ImageProcessorTypeEnum(ImageProcessorTypeEnum::THUMBNAIL),
                ],
                FilesystemImageSource::class,
            ],
            [
                [
                    'type' => 'webdav',
                    'cache_directory' => new DirectoryName(self::CACHE_DIRECTORY),
                    'storage_directory' => new DirectoryName(''),
                    'processor_type' => new ImageProcessorTypeEnum(ImageProcessorTypeEnum::THUMBNAIL),
                    'driver_uri' => '',
                    'oauth_token' => '',
                ],
                WebDAVImageSource::class,
            ],
            [
                [
                    'type' => 'geomap',
                    'cache_directory' => new DirectoryName(self::CACHE_DIRECTORY),
                    'driver' => '',
                    'api_key' => '',
                ],
                GeoMapImageSource::class,
            ],
        ];
    }
}
