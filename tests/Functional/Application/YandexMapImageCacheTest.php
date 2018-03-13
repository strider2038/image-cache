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

use Strider2038\ImgCache\Configuration\Configuration;
use Strider2038\ImgCache\Configuration\ImageSource\GeoMapImageSource;
use Strider2038\ImgCache\Configuration\ImageSource\ImageSourceCollection;
use Strider2038\ImgCache\Core\Streaming\ResourceStream;
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Enum\ResourceStreamModeEnum;
use Strider2038\ImgCache\Tests\Support\ApplicationTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapImageCacheTest extends ApplicationTestCase
{
    private const PNG_FILENAME = self::TEMPORARY_DIRECTORY . '/image.png';
    private const JPEG_FILENAME = self::TEMPORARY_DIRECTORY . '/image.jpg';
    private const IMAGE_WITH_INVALID_PARAMETERS = '/center40,60_size0x0.jpg';
    private const IMAGE_JPEG_CACHE_KEY = '/center60.715799,28.729073_size150x100.jpg';
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . '/center60.715799,28.729073_size150x100.jpg';
    private const IMAGE_PNG_CACHE_KEY = '/center60.715799,28.729073_size150x100.png';
    private const IMAGE_PNG_WEB_FILENAME = self::WEB_DIRECTORY . '/center60.715799,28.729073_size150x100.png';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadConfigurationToContainer(new Configuration(
            'test-token',
            85,
            new ImageSourceCollection([
                new GeoMapImageSource('/', 'yandex', '')
            ])
        ));

        $this->registerFakeHttpClient();
        $this->setBearerAccessToken('test-token');
    }

    /** @test */
    public function GET_givenNameWithInvalidParameters_badRequestResponseReturned(): void
    {
        $this->sendGET(self::IMAGE_WITH_INVALID_PARAMETERS);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::BAD_REQUEST);
    }

    /** @test */
    public function GET_givenJpegName_apiReturnsPngAndJpegImageIsCreated(): void
    {
        $this->givenImagePng(self::PNG_FILENAME);
        $stream = $this->givenStream(self::PNG_FILENAME);
        $this->givenHttpClient_request_returnsResponse(HttpStatusCodeEnum::OK, $stream);

        $this->sendGET(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
        $this->assertFileHasMimeType(self::IMAGE_JPEG_WEB_FILENAME, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function GET_givenPngName_apiReturnsJpegAndPngImageIsCreated(): void
    {
        $this->givenImageJpeg(self::JPEG_FILENAME);
        $stream = $this->givenStream(self::JPEG_FILENAME);
        $this->givenHttpClient_request_returnsResponse(HttpStatusCodeEnum::OK, $stream);

        $this->sendGET(self::IMAGE_PNG_CACHE_KEY);

        $this->assertResponseHasStatusCode(HttpStatusCodeEnum::CREATED);
        $this->assertFileExists(self::IMAGE_PNG_WEB_FILENAME);
        $this->assertFileHasMimeType(self::IMAGE_PNG_WEB_FILENAME, self::MIME_TYPE_PNG);
    }

    private function givenStream(string $filename): StreamInterface
    {
        return new ResourceStream(fopen($filename, ResourceStreamModeEnum::READ_AND_WRITE));
    }
}
