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
use Strider2038\ImgCache\Imaging\DeprecatedImageCacheInterface;
use Strider2038\ImgCache\Service\Image\ReplaceAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class ReplaceActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';

    /** @var DeprecatedImageCacheInterface */
    private $imageCache;

    /** @var RequestInterface */
    private $request;

    protected function setUp()
    {
        $this->imageCache = \Phake::mock(DeprecatedImageCacheInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function run_fileDoesNotExistInCache_deleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $action = $this->createAction();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertImageCache_delete_isNeverCalled();
        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function run_fileExistsInCache_deleteIsCalledAndCreatedResponseIsReturned(): void
    {
        $action = $this->createAction();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertImageCache_delete_isCalledOnce();
        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    private function createAction(): ReplaceAction
    {
        return new ReplaceAction(self::LOCATION, $this->imageCache, $this->responseFactory, $this->request);
    }

    private function givenRequest_getBody_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->request)->getBody()->thenReturn($stream);

        return $stream;
    }

    private function assertImageCache_put_isCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->put(self::LOCATION, $stream);
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
