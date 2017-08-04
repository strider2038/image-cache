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

use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

class ImageCacheWithFilesystemSourceTest extends FunctionalTestCase
{
    const FILE_NOT_EXIST = '/not-exist.jpg';
    const IMAGE_JPEG_CACHE_KEY = '/image.jpg';
    const IMAGE_JPEG_THUMBNAIL_CACHE_KEY = '/image_s50x75.jpg';
    const IMAGE_JPEG_THUMBNAIL_WIDTH = 50;
    const IMAGE_JPEG_THUMBNAIL_HEIGHT = 75;
    const IMAGE_JPEG_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    const IMAGE_JPEG_THUMBNAIL_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY;

    /** @var ImageCache */
    private $cache;

    protected function setUp()
    {
        parent::setUp();
        $container = $this->loadContainer('file-source.yml');
        $this->cache = $container->get('imageCache');
    }

    public function testGet_GivenImageNotExistInSource_NullIsReturned(): void
    {
        $image = $this->cache->get(self::FILE_NOT_EXIST);

        $this->assertNull($image);
    }

    public function testGet_GivenImageInRootOfSource_ImageIsReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $image = $this->cache->get(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
    }

    public function testGet_GivenImageInRootOfSourceAndThumbnailRequested_ThumbnailCreatedAndImageIsReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $image = $this->cache->get(self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        [$width, $height, $type] = getimagesize(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_WIDTH, $width);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_HEIGHT, $height);
        $this->assertEquals(IMAGETYPE_JPEG, $type);
    }
}
