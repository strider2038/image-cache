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
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Service\Image\ReplaceAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class ReplaceActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';
    private const FILE_NAME_MASK = 'file_name_mask';

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function processRequest_fileDoesNotExistInStorage_imagePutToStorageAndCreatedResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_imageExists_returns(false);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);
        $stream = $this->givenRequest_getBody_returnsStream($request);
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_imageExists_isCalledOnceWithLocation();
        $this->assertImageStorage_deleteImage_isNeverCalled();
        $this->assertImageCache_deleteImagesByMask_isNeverCalled();
        $this->assertRequest_getBody_isCalledOnceWithRequest($request);
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_putImage_isCalledOnceWithLocationAnd($image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function processRequest_fileExistsInStorage_imageReplacedInStorageAndCreatedResponseReturned(): void
    {
        $action = $this->createAction();
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);
        $this->givenImageStorage_imageExists_returns(true);
        $this->givenImageStorage_getImageFileNameMask_returnsFileNameMask();
        $stream = $this->givenRequest_getBody_returnsStream($request);
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_imageExists_isCalledOnceWithLocation();
        $this->assertImageStorage_deleteImage_isCalledOnceWithLocation();
        $this->assertImageStorage_getImageFileNameMask_isCalledOnceWithLocation();
        $this->assertImageCache_deleteImagesByMask_isCalledOnceWithFileNameMask();
        $this->assertRequest_getBody_isCalledOnceWithRequest($request);
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_putImage_isCalledOnceWithLocationAnd($image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    private function createAction(): ReplaceAction
    {
        return new ReplaceAction($this->responseFactory, $this->imageStorage, $this->imageCache, $this->imageFactory);
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

    private function givenRequest_getBody_returnsStream(RequestInterface $request): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($request)->getBody()->thenReturn($stream);

        return $stream;
    }

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function givenImageStorage_imageExists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->imageExists(\Phake::anyParameters())->thenReturn($value);
    }

    private function givenImageStorage_getImageFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageStorage)->getImageFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
    }

    private function assertRequest_getBody_isCalledOnceWithRequest(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getBody();
    }

    private function assertImageFactory_createFromStream_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromStream($stream);
    }

    private function assertImageStorage_putImage_isCalledOnceWithLocationAnd(Image $image): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->putImage(self::LOCATION, $image);
    }

    private function assertImageStorage_imageExists_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->imageExists(self::LOCATION);
    }

    private function assertImageStorage_deleteImage_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->deleteImage(self::LOCATION);
    }

    private function assertImageStorage_deleteImage_isNeverCalled(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(0))->deleteImage(\Phake::anyParameters());
    }

    private function assertImageStorage_getImageFileNameMask_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->getImageFileNameMask(self::LOCATION);
    }

    private function assertImageCache_deleteImagesByMask_isCalledOnceWithFileNameMask(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->deleteImagesByMask(self::FILE_NAME_MASK);
    }

    private function assertImageCache_deleteImagesByMask_isNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(0))->deleteImagesByMask(\Phake::anyParameters());
    }
}
