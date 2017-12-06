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
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Service\Image\CreateAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class CreateActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->givenResponseFactory();
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function processRequest_imageAlreadyExistsInStorage_conflictResponseWasReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_imageExists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CONFLICT);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_imageExists_isCalledOnceWithLocation(self::LOCATION);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CONFLICT);
        $this->assertEquals(HttpStatusCodeEnum::CONFLICT, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function processRequest_imageDoesNotExistInStorage_imagePutToStorageAndCreatedResponseWasReturned(): void
    {
        $action = $this->createAction();
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);
        $this->givenImageStorage_imageExists_returns(false);
        $stream = $this->givenRequest_getBody_returnsStream($request);
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_imageExists_isCalledOnceWithLocation(self::LOCATION);
        $this->assertRequest_getBody_isCalledOnce($request);
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_putImage_isCalledOnceWithLocationAndImage(self::LOCATION, $image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
    }

    private function createAction(): CreateAction
    {
        return new CreateAction($this->responseFactory, $this->imageStorage, $this->imageFactory);
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

    private function givenImageStorage_imageExists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->imageExists(\Phake::anyParameters())->thenReturn($value);
    }

    private function assertImageStorage_imageExists_isCalledOnceWithLocation(string $location): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->imageExists($location);
    }

    private function assertRequest_getBody_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getBody();
    }

    private function assertImageFactory_createFromStream_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromStream($stream);
    }

    private function assertImageStorage_putImage_isCalledOnceWithLocationAndImage(string $location, Image $image): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->putImage($location, $image);
    }

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
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
