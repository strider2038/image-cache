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
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;

class WebDAVImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = 'cache_directory';
    private const STORAGE_DIRECTORY = 'storage_directory';
    private const PROCESSOR_TYPE = 'processor_type';
    private const DRIVER_URI = 'driver_uri';
    private const OAUTH_TOKEN = 'oauth_token';
    private const IMAGE_STORAGE_SERVICE_ID = 'filesystem_storage';

    /** @test */
    public function construct_givenParameters_parametersSetAndAccessible(): void
    {
        $source = new WebDAVImageSource(
            self::CACHE_DIRECTORY,
            self::STORAGE_DIRECTORY,
            self::PROCESSOR_TYPE,
            self::DRIVER_URI,
            self::OAUTH_TOKEN
        );

        $this->assertEquals(self::CACHE_DIRECTORY, $source->getCacheDirectory());
        $this->assertEquals(self::STORAGE_DIRECTORY, $source->getStorageDirectory());
        $this->assertEquals(self::PROCESSOR_TYPE, $source->getProcessorType());
        $this->assertEquals(self::DRIVER_URI, $source->getDriverUri());
        $this->assertEquals(self::OAUTH_TOKEN, $source->getOauthToken());
        $this->assertEquals(self::IMAGE_STORAGE_SERVICE_ID, $source->getImageStorageServiceId());
    }
}
