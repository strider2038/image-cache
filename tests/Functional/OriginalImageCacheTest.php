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

use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

class OriginalImageCacheTest extends FunctionalTestCase
{
    private const FILE_NOT_EXIST = '/not-exist.jpg';
    private const IMAGE_JPEG_CACHE_KEY = '/image.jpg';
    private const IMAGE_JPEG_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_RUNTIME_FILENAME = self::RUNTIME_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY = '/sub/dir/image.jpg';
    private const IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;

    /** @var ImageCache */
    private $cache;

    protected function setUp()
    {
        parent::setUp();
        $container = $this->loadContainer('original-image-cache.yml');
        $this->cache = $container->get('image_cache');
    }

    /** @test */
    public function get_givenImageNotExistInSource_nullIsReturned(): void
    {
        $image = $this->cache->get(self::FILE_NOT_EXIST);

        $this->assertNull($image);
    }

    /** @test */
    public function get_givenImageInRootOfSource_imageIsReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $image = $this->cache->get(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
    }

    /** @test */
    public function get_givenImageInSubdirectoryRequested_imageIsReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);

        $image = $this->cache->get(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME);
    }

    /** @test */
    public function put_givenStream_imageIsCreated(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $stream = new ReadOnlyResourceStream(self::IMAGE_JPEG_RUNTIME_FILENAME);

        $this->cache->put(self::IMAGE_JPEG_CACHE_KEY, $stream);

        $this->assertFileExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function put_givenStream_imageIsCreatedInSubdirectory(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $stream = new ReadOnlyResourceStream(self::IMAGE_JPEG_RUNTIME_FILENAME);

        $this->cache->put(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY, $stream);

        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function delete_imageExistsInCache_imageIsDeleted(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $this->cache->delete(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertFileNotExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
    }
}
