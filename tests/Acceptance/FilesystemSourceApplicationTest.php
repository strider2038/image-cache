<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Acceptance;

use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Tests\Support\AcceptanceTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class FilesystemSourceApplicationTest extends AcceptanceTestCase
{
    private const URL_NOT_EXIST = '/i/not-exist.jpg';
    private const URL_INCORRECT_EXTENSION = '/index.php';
    private const URL_IMAGE_JPEG = '/sub/dir/image.jpg';
    private const URL_IMAGE_JPEG_THUMBNAIL = '/sub/dir/image_s150x150_q60.jpg';

    /** @test */
    public function GET_givenUrlWithIncorrectExtension_404NotFound(): void
    {
        $response = $this->sendGET(self::URL_INCORRECT_EXTENSION);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function GET_imageDoesNotExist_404NotFound(): void
    {
        $response = $this->sendGET(self::URL_NOT_EXIST);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function POST_invalidImageBody_400BadRequest(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);

        $response = $this->sendPOST(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::BAD_REQUEST, $response->getStatusCode());
    }

    /** @test */
    public function POST_imageNotExistInStorage_201Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();

        $response = $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode());
    }

    /** @test */
    public function POST_imageExistInStorage_209Conflict(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $response = $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $this->assertEquals(HttpStatusCodeEnum::CONFLICT, $response->getStatusCode());
    }

    /** @test */
    public function GET_imageExistInStorageAndNotExistInCache_201Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $response = $this->sendGET(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode());
    }

    /** @test */
    public function GET_imageExistInStorageAndExistInCache_200Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);
        $this->sendGET(self::URL_IMAGE_JPEG);

        $response = $this->sendGET(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode());
    }

    /** @test */
    public function PUT_imageNotExistInStorage_201Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();

        $response = $this->sendPUT(self::URL_IMAGE_JPEG, $imageBody);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode());
    }

    /** @test */
    public function PUT_imageExistInStorage_201Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $response = $this->sendPUT(self::URL_IMAGE_JPEG, $imageBody);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode());
    }

    /** @test */
    public function DELETE_imageNotExistInStorage_404NotFound(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);

        $response = $this->sendDELETE(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function DELETE_imageExistInStorage_200Ok(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $response = $this->sendDELETE(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode());
    }

    /** @test */
    public function GET_givenThumbnailNameAndImageExistInStorage_201Created(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);

        $response = $this->sendGET(self::URL_IMAGE_JPEG_THUMBNAIL);

        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode());
    }

    /** @test */
    public function DELETE_imageExistInStorageAndInCacheWithThumbnail_200OkAndThumbnailNotAccessible(): void
    {
        $this->givenAccessToken(self::ACCESS_CONTROL_TOKEN);
        $imageBody = $this->givenImageJpegContents();
        $this->sendPOST(self::URL_IMAGE_JPEG, $imageBody);
        $this->sendGET(self::URL_IMAGE_JPEG);
        $this->sendGET(self::URL_IMAGE_JPEG_THUMBNAIL);

        $response = $this->sendDELETE(self::URL_IMAGE_JPEG);

        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode());
        $this->assertGETUri404(self::URL_IMAGE_JPEG);
        $this->assertGETUri404(self::URL_IMAGE_JPEG_THUMBNAIL);
    }

    private function assertGETUri404(string $uri): void
    {
        $response = $this->sendGET($uri);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode());
    }
}
