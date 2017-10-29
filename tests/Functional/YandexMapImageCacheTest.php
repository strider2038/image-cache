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

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCache;
use Strider2038\ImgCache\Tests\Support\FunctionalTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class YandexMapImageCacheTest extends FunctionalTestCase
{
    private const PNG_FILENAME = self::RUNTIME_DIRECTORY . '/image.png';
    private const JPEG_FILENAME = self::RUNTIME_DIRECTORY . '/image.jpg';
    private const IMAGE_WITH_INVALID_PARAMETERS = '/size=0,0.jpg';
    private const IMAGE_JPEG_CACHE_KEY = '/ll=60.715799,28.729073_size=150,100.jpg';
    private const IMAGE_JPEG_WEB_FILENAME = self::WEB_DIRECTORY . '/ll=60.715799,28.729073_size=150,100.jpg';
    private const IMAGE_PNG_CACHE_KEY = '/ll=60.715799,28.729073_size=150,100.png';
    private const IMAGE_PNG_WEB_FILENAME = self::WEB_DIRECTORY . '/ll=60.715799,28.729073_size=150,100.png';

    /** @var ClientInterface */
    private $client;

    /** @var ImageCache */
    private $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $container = $this->loadContainer('yandex-map-image-cache.yml');
        $this->client = \Phake::mock(ClientInterface::class);
        $container->set('client_mock', $this->client);
        $this->cache = $container->get('image_cache');
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestValueException
     * @expectedExceptionCode 400
     */
    public function get_givenNameWithInvalidParameters_exceptionThrown(): void
    {
        $this->cache->get(self::IMAGE_WITH_INVALID_PARAMETERS);
    }

    /** @test */
    public function get_givenJpegName_apiReturnsPngAndJpegImageIsCreated(): void
    {
        $response = $this->givenClient_request_returnsResponseWithStatus200();
        $this->givenImagePng(self::PNG_FILENAME);
        $this->givenResponse_getBody_getContents_returns($response, file_get_contents(self::PNG_FILENAME));

        $image = $this->cache->get(self::IMAGE_JPEG_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_JPEG_WEB_FILENAME);
        $this->assertFileHasMimeType(self::IMAGE_JPEG_WEB_FILENAME, self::MIME_TYPE_JPEG);
    }

    /** @test */
    public function get_givenPngName_apiReturnsJpegAndPngImageIsCreated(): void
    {
        $response = $this->givenClient_request_returnsResponseWithStatus200();
        $this->givenImageJpeg(self::JPEG_FILENAME);
        $this->givenResponse_getBody_getContents_returns($response, file_get_contents(self::JPEG_FILENAME));

        $image = $this->cache->get(self::IMAGE_PNG_CACHE_KEY);

        $this->assertInstanceOf(ImageFile::class, $image);
        $this->assertFileExists(self::IMAGE_PNG_WEB_FILENAME);
        $this->assertFileHasMimeType(self::IMAGE_PNG_WEB_FILENAME, self::MIME_TYPE_PNG);
    }

    private function givenClient_request_returnsResponseWithStatus200(): ResponseInterface
    {
        $response = \Phake::mock(ResponseInterface::class);
        \Phake::when($this->client)->request(\Phake::anyParameters())->thenReturn($response);
        \Phake::when($response)->getStatusCode()->thenReturn(HttpStatusCodeEnum::OK);

        return $response;
    }

    private function givenResponse_getBody_getContents_returns(ResponseInterface $response, string $imageContents): void
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($response)->getBody()->thenReturn($stream);
        \Phake::when($stream)->getContents()->thenReturn($imageContents);
    }
}
