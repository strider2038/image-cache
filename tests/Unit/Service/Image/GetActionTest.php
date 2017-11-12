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
use Strider2038\ImgCache\Core\Http\ResponseInterface;
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
    public function run_imageDoesNotExistInStorage_notFoundResponseWasReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_find_returns(null);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_find_isCalledOnceWithLocation();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function run_imageExistsInStorage_imageIsCachedAndFileResponseWasReturned(): void
    {
        $action = $this->createAction();
        $storedImage = \Phake::mock(Image::class);
        $this->givenImageStorage_find_returns($storedImage);
        $cachedImage = $this->givenImageFile();
        $this->givenImageCache_get_returns($cachedImage);
        $this->givenResponseFactory_createFileResponse_returnsResponse();

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
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
        return new GetAction(self::LOCATION, $this->responseFactory, $this->imageStorage, $this->imageCache);
    }

    private function givenImageFile(): ImageFile
    {
        $image = \Phake::mock(ImageFile::class);
        \Phake::when($image)->getFilename()->thenReturn(self::IMAGE_FILENAME);

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
}
