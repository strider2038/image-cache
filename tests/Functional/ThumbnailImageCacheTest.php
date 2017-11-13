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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\ReadOnlyResourceStream;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Service\ImageController;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

class ThumbnailImageCacheTest extends FunctionalTestCase
{
    private const FILE_NOT_EXIST = '/not-exist.jpg';
    private const IMAGE_JPEG_CACHE_KEY = '/image.jpg';
    private const IMAGE_JPEG_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_RUNTIME_FILENAME = self::RUNTIME_DIRECTORY . self::IMAGE_JPEG_CACHE_KEY;
    private const IMAGE_JPEG_THUMBNAIL_CACHE_KEY = '/image_s50x75.jpg';
    private const IMAGE_JPEG_THUMBNAIL_WIDTH = 50;
    private const IMAGE_JPEG_THUMBNAIL_HEIGHT = 75;
    private const IMAGE_JPEG_THUMBNAIL_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY = '/sub/dir/image.jpg';
    private const IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME = self::FILESOURCE_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;

    /** @var ImageController */
    private $controller;

    /** @var RequestInterface */
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->loadContainer('thumbnail-image-cache.yml');
        $this->request = \Phake::mock(RequestInterface::class);
        $container->set('request', $this->request);
        $this->controller = $container->get('image_controller');
    }

    /** @test */
    public function get_givenImageNotExistInSource_notFoundResponseReturned(): void
    {
        $response = $this->controller->runAction('get',self::FILE_NOT_EXIST);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function get_givenImageInRootOfSource_imageCreatedInCacheAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $response = $this->controller->runAction('get',self::IMAGE_JPEG_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
    }

    /** @test */
    public function get_givenImageInRootOfSourceAndThumbnailRequested_thumbnailCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);

        $response = $this->controller->runAction('get',self::IMAGE_JPEG_THUMBNAIL_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertFileExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        [$width, $height, $type] = getimagesize(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_WIDTH, $width);
        $this->assertEquals(self::IMAGE_JPEG_THUMBNAIL_HEIGHT, $height);
        $this->assertEquals(IMAGETYPE_JPEG, $type);
    }

    /** @test */
    public function get_givenImageInSubdirectoryRequested_imageCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);

        $response = $this->controller->runAction('get',self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME);
    }

    /** @test */
    public function replace_givenStream_imageIsCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $stream = new ReadOnlyResourceStream(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $this->givenRequest_getBody_returns($stream);

        $response = $this->controller->runAction('replace',self::IMAGE_JPEG_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertFileExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function replace_givenStream_imageIsCreatedInCacheSubdirectoryAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $stream = new ReadOnlyResourceStream(self::IMAGE_JPEG_RUNTIME_FILENAME);
        $this->givenRequest_getBody_returns($stream);

        $response = $this->controller->runAction('replace',self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_FILESYSTEM_FILENAME);
    }

    /** @test */
    public function delete_imageExistsInSourceAndIsCachedAndThumbnailExists_imageAndThumbnailDeleted(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_WEB_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);

        $response = $this->controller->runAction('delete',self::IMAGE_JPEG_CACHE_KEY);

        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
        $this->assertFileNotExists(self::IMAGE_JPEG_FILESYSTEM_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_WEB_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
    }

    private function givenRequest_getBody_returns(StreamInterface $stream): void
    {
        \Phake::when($this->request)->getBody()->thenReturn($stream);
    }
}
