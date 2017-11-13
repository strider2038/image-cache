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
use Strider2038\ImgCache\Core\StreamInterface;
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

    /** @var RequestInterface */
    private $request;

    protected function setUp(): void
    {
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->givenResponseFactory();
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /** @test */
    public function run_fileDoesNotExistInStorage_imagePutToStorageAndCreatedResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(false);
        $stream = $this->givenRequest_getBody_returnsStream();
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isNeverCalled();
        $this->assertImageCache_deleteByMask_isNeverCalled();
        $this->assertRequest_getBody_isCalledOnce();
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_put_isCalledOnceWithLocationAnd($image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function run_fileExistsInStorage_imageReplacedInStorageAndCreatedResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(true);
        $this->givenImageStorage_getFileNameMask_returnsFileNameMask();
        $stream = $this->givenRequest_getBody_returnsStream();
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isCalledOnceWithLocation();
        $this->assertImageStorage_getFileNameMask_isCalledOnceWithLocation();
        $this->assertImageCache_deleteByMask_isCalledOnceWithFileNameMask();
        $this->assertRequest_getBody_isCalledOnce();
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_put_isCalledOnceWithLocationAnd($image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    private function createAction(): ReplaceAction
    {
        return new ReplaceAction(self::LOCATION, $this->responseFactory, $this->imageStorage, $this->imageCache, $this->imageFactory, $this->request);
    }

    private function givenRequest_getBody_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->request)->getBody()->thenReturn($stream);

        return $stream;
    }

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }

    private function givenImageStorage_exists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->exists(\Phake::anyParameters())->thenReturn($value);
    }

    private function givenImageStorage_getFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageStorage)->getFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
    }

    private function assertRequest_getBody_isCalledOnce(): void
    {
        \Phake::verify($this->request, \Phake::times(1))->getBody();
    }

    private function assertImageFactory_createFromStream_isCalledOnceWith(StreamInterface $stream): void
    {
        \Phake::verify($this->imageFactory, \Phake::times(1))->createFromStream($stream);
    }

    private function assertImageStorage_put_isCalledOnceWithLocationAnd(Image $image): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->put(self::LOCATION, $image);
    }

    private function assertImageStorage_exists_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->exists(self::LOCATION);
    }

    private function assertImageStorage_delete_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->delete(self::LOCATION);
    }

    private function assertImageStorage_delete_isNeverCalled(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(0))->delete(\Phake::anyParameters());
    }

    private function assertImageStorage_getFileNameMask_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->getFileNameMask(self::LOCATION);
    }

    private function assertImageCache_deleteByMask_isCalledOnceWithFileNameMask(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->deleteByMask(self::FILE_NAME_MASK);
    }

    private function assertImageCache_deleteByMask_isNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(0))->deleteByMask(\Phake::anyParameters());
    }
}
