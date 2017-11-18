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
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Imaging\ImageStorageInterface;
use Strider2038\ImgCache\Service\Image\DeleteAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class DeleteActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';
    private const FILE_NAME_MASK = 'file_name_mask';

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
    public function processRequest_imageExistsInStorage_imageDeletedFromStorageAndFromCacheAndOkResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(true);
        $this->givenImageStorage_getFileNameMask_returnsFileNameMask();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isCalledOnceWithLocation();
        $this->assertImageStorage_getFileNameMask_isCalledOnceWithLocation();
        $this->assertImageCache_deleteByMask_isCalledOnceWithFileNameMask();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function processRequest_imageDoesNotExistInStorage_notFoundResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $request = $this->givenRequest();
        $uri = $this->givenRequest_getUri_returnsUriWithPath($request, self::LOCATION);

        $response = $action->processRequest($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertRequest_getUri_isCalledOnce($request);
        $this->assertUri_getPath_isCalledOnce($uri);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isNeverCalled();
        $this->assertImageCache_deleteByMask_isNeverCalled();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    private function createAction(): DeleteAction
    {
        return new DeleteAction($this->responseFactory, $this->imageStorage, $this->imageCache);
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

    private function givenImageStorage_exists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->exists(\Phake::anyParameters())->thenReturn($value);
    }

    private function assertRequest_getUri_isCalledOnce(RequestInterface $request): void
    {
        \Phake::verify($request, \Phake::times(1))->getUri();
    }

    private function assertUri_getPath_isCalledOnce(UriInterface $uri): void
    {
        \Phake::verify($uri, \Phake::times(1))->getPath();
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

    private function givenImageStorage_getFileNameMask_returnsFileNameMask(): void
    {
        \Phake::when($this->imageStorage)->getFileNameMask(\Phake::anyParameters())->thenReturn(self::FILE_NAME_MASK);
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
