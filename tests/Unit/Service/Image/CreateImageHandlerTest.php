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
use Strider2038\ImgCache\Core\Streaming\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFactoryInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Service\Image\CreateImageHandler;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class CreateImageHandlerTest extends TestCase
{
    use ResponseFactoryTrait;

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageFactoryInterface */
    private $imageFactory;

    protected function setUp(): void
    {
        $this->givenResponseFactory();
        $this->filenameFactory = \Phake::mock(ImageFilenameFactoryInterface::class);
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
    }

    /** @test */
    public function handleRequest_imageAlreadyExistsInStorage_conflictResponseWasReturned(): void
    {
        $handler = $this->createCreateImageHandler();
        $request = $this->givenRequest();
        $filename = $this->givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename();
        $this->givenImageStorage_imageExists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CONFLICT);

        $response = $handler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest($request);
        $this->assertImageStorage_imageExists_isCalledOnceWithFilename($filename);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CONFLICT);
        $this->assertEquals(HttpStatusCodeEnum::CONFLICT, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function handleRequest_imageDoesNotExistInStorage_imagePutToStorageAndCreatedResponseWasReturned(): void
    {
        $handler = $this->createCreateImageHandler();
        $request = $this->givenRequest();
        $filename = $this->givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename();
        $this->givenImageStorage_imageExists_returns(false);
        $stream = $this->givenRequest_getBody_returnsStream($request);
        $image = $this->givenImageFactory_createImageFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $handler->handleRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest($request);
        $this->assertImageStorage_imageExists_isCalledOnceWithFilename($filename);
        $this->assertRequest_getBody_isCalledOnce($request);
        $this->assertImageFactory_createImageFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_putImage_isCalledOnceWithFilenameAndImage($filename, $image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
    }

    private function createCreateImageHandler(): CreateImageHandler
    {
        return new CreateImageHandler(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageFactory
        );
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
    }

    private function assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest(
        RequestInterface $request
    ): void {
        \Phake::verify($this->filenameFactory, \Phake::times(1))->createImageFilenameFromRequest($request);
    }

    private function givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename(): ImageFilenameInterface
    {
        $filename = \Phake::mock(ImageFilenameInterface::class);
        \Phake::when($this->filenameFactory)
            ->createImageFilenameFromRequest(\Phake::anyParameters())
            ->thenReturn($filename);

        return $filename;
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

    private function assertImageStorage_imageExists_isCalledOnceWithFilename(ImageFilenameInterface $filename): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->imageExists($filename);
    }

    private function assertRequest_getBody_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getBody();
    }

    private function assertImageFactory_createImageFromStream_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createImageFromStream($stream);
    }

    private function assertImageStorage_putImage_isCalledOnceWithFilenameAndImage(
        ImageFilenameInterface $filename,
        Image $image
    ): void {
        \Phake::verify($this->imageStorage, \Phake::times(1))->putImage($filename, $image);
    }

    private function givenImageFactory_createImageFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createImageFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
