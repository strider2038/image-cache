<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Imaging;

use Strider2038\ImgCache\Imaging\{
    Image, ImageCache
};
use Strider2038\ImgCache\Imaging\Extraction\ImageExtractorInterface;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

class ImageCacheTest extends FileTestCase
{

    /** @var ImageExtractorInterface */
    private $imageExtractor;

    protected function setUp()
    {
        $this->imageExtractor = \Phake::mock(ImageExtractorInterface::class);
    }

    public function testGet_ImageDoesNotExist_NullIsReturned(): void
    {
        $cache = new ImageCache(
            self::TEST_CACHE_DIR,
            $this->imageExtractor
        );
        \Phake::when($this->imageExtractor)->extract(\Phake::anyParameters())->thenReturn(null);

        $image = $cache->get('a.jpg');

        $this->assertNull($image);
    }
}
