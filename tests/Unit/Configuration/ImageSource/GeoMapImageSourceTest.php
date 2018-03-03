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
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Imaging\Naming\DirectoryName;

class GeoMapImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory/';
    private const DRIVER = 'driver';
    private const API_KEY = 'api_key';

    /** @test */
    public function construct_givenParameters_parametersSetAndAccessible(): void
    {
        $source = new GeoMapImageSource(
            new DirectoryName(self::CACHE_DIRECTORY),
            self::DRIVER,
            self::API_KEY
        );

        $this->assertEquals(self::CACHE_DIRECTORY, $source->getCacheDirectory());
        $this->assertEquals(self::DRIVER, $source->getDriver());
        $this->assertEquals(self::API_KEY, $source->getApiKey());
    }
}
