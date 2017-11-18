<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Service\Image\GetAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class GetActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';
    private const IMAGE_FILENAME = 'image.jpg';

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    protected function setUp(): void
    {
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function processRequest_imageDoesNotExistInStorage_notFoundResponseWasReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_find_returns(null);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_find_isCalledOnceWithLocation();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function processRequest_imageExistsInStorage_imageIsCachedAndFileResponseWasReturned(): void
    {
        $action = $this->createAction();
        $storedImage = $this->givenImage();
        $this->givenImageStorage_find_returns($storedImage);
        $cachedImage = $this->givenImageFileWithFilename(self::IMAGE_FILENAME);
        $this->givenImageCache_get_returns($cachedImage);
        $this->givenResponseFactory_createFileResponse_returnsResponse();
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_find_isCalledOnceWithLocation();
        $this->assertImageCache_put_isCalledOnceWithLocationAnd($storedImage);
        $this->assertImageCache_get_isCalledOnceWithLocation();
        $this->assertResponseFactory_createFileResponse_isCalledOnceWith(
            HttpStatusCodeEnum::CREATED,
            self::IMAGE_FILENAME
        );
    }

    private function createAction(): GetAction
    {
        return new GetAction($this->responseFactory, $this->imageStorage, $this->imageCache);
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function givenRequest_getUri_returnsUriWithPath(RequestInterface $request, string $path): UriInterface
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($request)->getUri()->thenReturn($uri);
        \Phake::when($uri)->getPath()->thenReturn($path);

        return $uri;
    }

    private function givenImage(): Image
    {
        return \Phake::mock(Image::class);
    }

    private function givenImageFileWithFilename(string $filename): ImageFile
    {
        $image = \Phake::mock(ImageFile::class);
        \Phake::when($image)->getFilename()->thenReturn($filename);

        return $image;
    }

    private function assertImageStorage_find_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->find(self::LOCATION);
    }

    private function givenImageStorage_find_returns(?Image $storedImage): void
    {
        \Phake::when($this->imageStorage)->find(\Phake::anyParameters())->thenReturn($storedImage);
    }

    private function assertImageCache_put_isCalledOnceWithLocationAnd(Image $storedImage): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->put(self::LOCATION, $storedImage);
    }

    private function assertImageCache_get_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->get(self::LOCATION);
    }

    private function givenImageCache_get_returns(ImageFile $cachedImage): void
    {
        \Phake::when($this->imageCache)->get(\Phake::anyParameters())->thenReturn($cachedImage);
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }
}
