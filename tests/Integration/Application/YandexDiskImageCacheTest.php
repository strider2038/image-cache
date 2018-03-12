<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Integration\Application;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Configuration\ImageSource\WebDAVImageSource;
use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Enum\WebDAVMethodEnum;
use Strider2038\ImgCache\Tests\Support\ApplicationTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexDiskImageCacheTest extends ApplicationTestCase
{
    private const WEBDAV_DRIVER_URI = 'https://webdav.yandex.ru/v1';
    private const STORAGE_ROOT = '/imgcache-test';
    private const FILE_NOT_EXIST = '/not-exist.jpg';
    private const IMAGE_JPEG_FILENAME = '/image.jpg';
    private const IMAGE_JPEG_STORAGE_FILENAME = self::STORAGE_ROOT . self::IMAGE_JPEG_FILENAME;
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_FILENAME;
    private const IMAGE_JPEG_TEMPORARY_FILENAME = self::TEMPORARY_DIRECTORY . self::IMAGE_JPEG_FILENAME;
    private const IMAGE_JPEG_THUMBNAIL_CACHE_FILENAME = '/image_s50x75.jpg';
    private const IMAGE_JPEG_THUMBNAIL_WIDTH = 50;
    private const IMAGE_JPEG_THUMBNAIL_HEIGHT = 75;
    private const IMAGE_JPEG_THUMBNAIL_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_THUMBNAIL_CACHE_FILENAME;
    private const SUBDIRECTORY_LEVEL1_NAME = '/sub';
    private const SUBDIRECTORY_LEVEL2_NAME = '/sub/dir';
    private const IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY = '/sub/dir/image.jpg';
    private const IMAGE_JPEG_IN_SUBDIRECTORY_STORAGE_FILENAME = self::STORAGE_ROOT . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;
    private const IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME = self::WEB_DIRECTORY . self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY;

    /** @var ClientInterface */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $token = getenv('YANDEX_DISK_ACCESS_TOKEN') ?? '';

        $this->loadConfigurationToContainer(new Configuration(
            'test-token',
            85,
            new ImageSourceCollection([
                new WebDAVImageSource(
                    '/',
                    self::STORAGE_ROOT,
                    'thumbnail',
                    self::WEBDAV_DRIVER_URI,
                    $token
                )
            ])
        ));

        $this->client = $this->createWebDAVHttpClient($token);

        $this->setBearerAccessToken('test-token');

        $this->deleteStorageDirectory(self::STORAGE_ROOT);
        $this->givenStorageDirectory(self::STORAGE_ROOT);
    }

    /** @test */
    public function GET_givenImageNotExistInStorage_notFoundExceptionThrown(): void
    {
        $this->sendGET(self::FILE_NOT_EXIST);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    /** @test */
    public function GET_givenImageInRootOfStorage_imageCreatedInCacheAndCreatedResponseReturned(): void
    {
        $this->givenJpegImageInStorage(self::IMAGE_JPEG_STORAGE_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_FILENAME);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
    }

    /** @test */
    public function GET_givenImageInRootOfStorageAndThumbnailRequested_thumbnailCreatedAndCreatedResponseReturned(): void
    {
        $this->givenJpegImageInStorage(self::IMAGE_JPEG_STORAGE_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_THUMBNAIL_CACHE_FILENAME);

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
        $this->givenStorageDirectory(self::STORAGE_ROOT . self::SUBDIRECTORY_LEVEL1_NAME);
        $this->givenStorageDirectory(self::STORAGE_ROOT . self::SUBDIRECTORY_LEVEL2_NAME);
        $this->givenJpegImageInStorage(self::IMAGE_JPEG_IN_SUBDIRECTORY_STORAGE_FILENAME);

        $this->sendGET(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_WEB_FILENAME);
    }

    /** @test */
    public function PUT_givenStream_imageIsCreatedAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_TEMPORARY_FILENAME);
        $stream = $this->givenStream(self::IMAGE_JPEG_TEMPORARY_FILENAME);

        $this->sendPUT(self::IMAGE_JPEG_FILENAME, $stream);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertStorageFileExists(self::IMAGE_JPEG_STORAGE_FILENAME);
    }

    /** @test */
    public function PUT_givenStream_imageIsCreatedInCacheSubdirectoryAndCreatedResponseReturned(): void
    {
        $this->givenImageJpeg(self::IMAGE_JPEG_TEMPORARY_FILENAME);
        $stream = $this->givenStream(self::IMAGE_JPEG_TEMPORARY_FILENAME);

        $this->sendPUT(self::IMAGE_JPEG_IN_SUBDIRECTORY_CACHE_KEY, $stream);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertStorageFileExists(self::IMAGE_JPEG_IN_SUBDIRECTORY_STORAGE_FILENAME);
    }

    /** @test */
    public function DELETE_imageExistsInStorageAndIsCachedAndThumbnailExists_imageAndThumbnailDeleted(): void
    {
        $this->givenJpegImageInStorage(self::IMAGE_JPEG_STORAGE_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_WEB_FILENAME);
        $this->givenImageJpeg(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);

        $this->sendDELETE(self::IMAGE_JPEG_FILENAME);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::OK);
        $this->assertStorageFileNotExists(self::IMAGE_JPEG_STORAGE_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_WEB_FILENAME);
        $this->assertFileNotExists(self::IMAGE_JPEG_THUMBNAIL_WEB_FILENAME);
    }

    private function givenStream(string $filename): StreamInterface
    {
        return new ResourceStream(fopen($filename, ResourceStreamModeEnum::READ_AND_WRITE));
    }

    private function deleteStorageDirectory(string $directory): void
    {
        try {
            $this->client->request(WebDAVMethodEnum::DELETE, $directory);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() !== HttpStatusCodeEnum::NOT_FOUND) {
                throw $exception;
            }
        }
    }

    private function givenStorageDirectory(string $directory): void
    {
        $this->client->request(WebDAVMethodEnum::MKCOL, $directory);
    }

    private function givenJpegImageInStorage(string $filename): void
    {
        $contents = $this->givenImageJpegContents();

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $contents);
        finfo_close($finfo);

        $this->client->request(
            WebDAVMethodEnum::PUT,
            $filename,
            [
                'headers' => [
                    'Content-Type' => $mime,
                    'Content-Length' => strlen($contents),
                    'Etag' => md5($contents),
                    'Sha256' => hash('sha256', $contents),
                ],
                'body' => $contents,
                'expect' => true,
            ]
        );
    }

    private function assertStorageFileExists(string $filename): void
    {
        $response = $this->requestProperties($filename);
        $this->assertEquals(HttpStatusCodeEnum::MULTI_STATUS, $response->getStatusCode());
    }

    private function assertStorageFileNotExists(string $filename): void
    {
        $response = $this->requestProperties($filename);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    private function requestProperties(string $filename): ResponseInterface
    {
        try {
            $response = $this->client->request(
                WebDAVMethodEnum::PROPFIND,
                $filename,
                [
                    'headers' => [
                        'Depth' => '0',
                    ],
                ]
            );
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
        }

        return $response;
    }

    private function createWebDAVHttpClient(string $token): Client
    {
        return new Client([
            'base_uri' => self::WEBDAV_DRIVER_URI,
            'headers' => [
                'Authorization' => 'OAuth ' . $token,
                'Host' => 'webdav.yandex.ru',
                'User-Agent' => 'Image Caching Microservice',
                'Accept' => '*/*'
            ],
        ]);
    }
}
