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
use Strider2038\ImgCache\Configuration\ImageSource\AbstractImageSource;

class AbstractImageSourceTest extends TestCase
{
    private const CACHE_DIRECTORY = '/cache_directory/';

    /** @test */
    public function construct_givenCacheDirectory_cacheDirectorySet(): void
    {
        $cacheDirectory = self::CACHE_DIRECTORY;
        $source = new class($cacheDirectory) extends AbstractImageSource {
            public function getId(): string
            {
                return '';
            }
            public function getImageStorageServiceId(): string
            {
                return '';
            }
        };

        $this->assertEquals(self::CACHE_DIRECTORY, $source->getCacheDirectory());
    }
}
