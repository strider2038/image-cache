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
    public function run_imageExistsInStorage_imageDeletedFromStorageAndFromCacheAndOkResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(true);
        $this->givenImageStorage_getFileNameMask_returnsFileNameMask();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isCalledOnceWithLocation();
        $this->assertImageStorage_getFileNameMask_isCalledOnceWithLocation();
        $this->assertImageCache_deleteByMask_isCalledOnceWithFileNameMask();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
        $this->assertEquals(HttpStatusCodeEnum::OK, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function run_imageDoesNotExistInStorage_notFoundResponseReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertImageStorage_delete_isNeverCalled();
        $this->assertImageCache_deleteByMask_isNeverCalled();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
        $this->assertEquals(HttpStatusCodeEnum::NOT_FOUND, $response->getStatusCode()->getValue());
    }

    private function givenImageStorage_exists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->exists(\Phake::anyParameters())->thenReturn($value);
    }

    private function assertImageStorage_exists_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->exists(self::LOCATION);
    }

    private function createAction(): DeleteAction
    {
        return new DeleteAction(self::LOCATION, $this->responseFactory, $this->imageStorage, $this->imageCache);
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
