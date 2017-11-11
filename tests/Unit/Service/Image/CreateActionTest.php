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
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Service\Image\CreateAction;
use Strider2038\ImgCache\Tests\Support\Phake\ResponseFactoryTrait;

class CreateActionTest extends TestCase
{
    use ResponseFactoryTrait;

    private const LOCATION = 'a.jpg';

    /** @var ImageCacheInterface */
    private $imageCache;

    /** @var RequestInterface */
    private $request;

    protected function setUp()
    {
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
        $this->givenResponseFactory();
    }

    /** @test */
    public function run_fileAlreadyExistsInCache_conflictResponseIsReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CONFLICT);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CONFLICT);
    }

    /** @test */
    public function run_fileDoesNotExistInCache_createdResponseIsReturned(): void
    {
        $action = $this->createAction();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    private function createAction(): CreateAction
    {
        return new CreateAction(self::LOCATION, $this->imageCache, $this->responseFactory, $this->request);
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
}
