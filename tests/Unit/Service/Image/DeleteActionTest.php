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
use Strider2038\ImgCache\Imaging\DeprecatedImageCacheInterface;
use Strider2038\ImgCache\Service\Image\DeleteAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class DeleteActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';

    /** @var DeprecatedImageCacheInterface */
    private $imageCache;

    protected function setUp()
    {
        $this->imageCache = \Phake::mock(DeprecatedImageCacheInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function delete_fileExistsInCache_deleteIsCalledAndOkResponseIsReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);

        $response = $action->run();

        $this->assertImageCache_delete_isCalledOnce();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
    }

    /** @test */
    public function delete_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $action->run();

        $this->assertImageCache_delete_isNeverCalled();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    private function createAction(): DeleteAction
    {
        return new DeleteAction(self::LOCATION, $this->imageCache, $this->responseFactory);
    }

    private function givenImageCache_exists_returns(bool $value): void
    {
        \Phake::when($this->imageCache)->exists(self::LOCATION)->thenReturn($value);
    }

    private function assertImageCache_delete_isNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::never())->delete(\Phake::anyParameters());
    }

    private function assertImageCache_delete_isCalledOnce(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->delete(self::LOCATION);
    }
}
