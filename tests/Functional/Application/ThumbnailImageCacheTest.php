<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Functional\Application;

use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Service\ImageController;
use Strider2038\ImgCache\Tests\Support\ApplicationTestCase;

class ThumbnailImageCacheTest extends ApplicationTestCase
{
    private const FILE_NOT_EXIST = '/not-exist.jpg';
    private const IMAGE_JPEG_CACHE_KEY = '/image.jpg';
    private const IMAGE_JPEG_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_TEMPORARY_FILENAME = self::TEMPORARY_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_THUMBNAIL_CACHE_KEY = '/image_s50x75.jpg';
    private const IMAGE_JPEG_THUMBNAIL_WIDTH = 50;
    private const IMAGE_JPEG_THUMBNAIL_HEIGHT = 75;
    private const IMAGE_JPEG_THUMBNAIL_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY = '/sub/dir/image.jpg';
    private const IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setConfigurationFilename('application/thumbnail-image-cache-parameters.yml');
        $this->setBearerAccessToken('test-token');
    }

    /** @test */
    public function GET_givenImageNotExistInStorage_notFoundResponseReturned(): void
    {
        $this->sendGET(self::FILE_NOT_EXIST);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    /** @test */
    public function GET_givenImageInRootOfStorage_imageCreatedInCacheAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
    }

    /** @test */
    public function GET_givenImageInRootOfStorageAndThumbnailRequested_thumbnailCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        [$width, $height, $type] = getimagesize(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_WIDTH, $width);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_HEIGHT, $height);
        $this->assertEquals(IMAGETYPE_JPEG, $type);
    }

    /** @test */
    public function GET_givenImageInSubdirectoryRequested_imageCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME);
    }

    /** @test */
    public function PUT_givenStream_imageIsCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_TEMPORARY_FILENAME);
        $stream = $this->givenStream(self::IMAGE_JPEG_TEMPORARY_FILENAME);

        $this->sendPUT(self::IMAGE_JPEG_CACHE_KEY, $stream);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function PUT_givenStream_imageIsCreatedInCacheSubdirectoryAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_TEMPORARY_FILENAME);
        $stream = $this->givenStream(self::IMAGE_JPEG_TEMPORARY_FILENAME);

        $this->sendPUT(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY, $stream);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function delete_imageExistsInStorageAndIsCachedAndThumbnailExists_imageAndThumbnailDeleted(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_WEB_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);

        $this->sendDELETE(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::OK);
        $this->assertFileNotExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_WEB_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
    }

    private function givenStream(string $filename): StreamInterface
    {
        return new ResourceStream(fopen($filename, ResourceStreamModeEnum::READ_AND_WRITE));
    }
}
