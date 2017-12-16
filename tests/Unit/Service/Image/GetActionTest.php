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
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\Image;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameFactoryInterface;
use Strider2038\ImgCache\Imaging\Naming\ImageFilenameInterface;
use Strider2038\ImgCache\Service\Image\GetAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class GetActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const IMAGE_FILENAME = 'image.jpg';

    /** @var ImageFilenameFactoryInterface */
    private $filenameFactory;

    /** @var ImageStorageInterface */
    private $imageStorage;

    /** @var ImageCacheInterface */
    private $imageCache;

    protected function setUp(): void
    {
        $this->filenameFactory = \Phake::mock(ImageFilenameFactoryInterface::class);
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function processRequest_imageExistsInStorage_imageIsCachedAndFileResponseWasReturned(): void
    {
        $action = $this->createAction();
        $request = $this->givenRequest();
        $filename = $this->givenFilenameFactory_createImageFilenameFromRequest_returnsImageFilename();
        $storedImage = $this->givenImage();
        $this->givenImageStorage_getImage_returnsImage($storedImage);
        $cachedImage = $this->givenImageFileWithFilename(self::IMAGE_FILENAME);
        $this->givenImageCache_getImage_returnsImageFile($cachedImage);
        $this->givenResponseFactory_createFileResponse_returnsResponse();

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertFilenameFactory_createImageFilenameFromRequest_isCalledOnceWithRequest($request);
        $this->assertImageStorage_getImage_isCalledOnceWithFilename($filename);
        $this->assertImageCache_putImage_isCalledOnceWithFilenameAndImage($filename, $storedImage);
        $this->assertImageCache_getImage_isCalledOnceWithFilename($filename);
        $this->assertResponseFactory_createFileResponse_isCalledOnceWith(
            HttpStatusCodeEnum::CREATED,
            self::IMAGE_FILENAME
        );
    }

    private function createAction(): GetAction
    {
        return new GetAction(
            $this->responseFactory,
            $this->filenameFactory,
            $this->imageStorage,
            $this->imageCache
        );
    }

    private function givenRequest(): RequestInterface
    {
        return \Phake::mock(RequestInterface::class);
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

    private function assertImageStorage_getImage_isCalledOnceWithFilename(ImageFilenameInterface $filename): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->getImage($filename);
    }

    private function givenImageStorage_getImage_returnsImage(Image $storedImage): void
    {
        \Phake::when($this->imageStorage)->getImage(\Phake::anyParameters())->thenReturn($storedImage);
    }

    private function assertImageCache_putImage_isCalledOnceWithFilenameAndImage(
        ImageFilenameInterface $filename,
        Image $storedImage
    ): void {
        \Phake::verify($this->imageCache, \Phake::times(1))->putImage($filename, $storedImage);
    }

    private function assertImageCache_getImage_isCalledOnceWithFilename(ImageFilenameInterface $filename): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->getImage($filename);
    }

    private function givenImageCache_getImage_returnsImageFile(ImageFile $cachedImage): void
    {
        \Phake::when($this->imageCache)->getImage(\Phake::anyParameters())->thenReturn($cachedImage);
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
}
