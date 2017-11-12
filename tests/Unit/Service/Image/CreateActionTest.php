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

    /** @var RequestInterface */
    private $request;

    protected function setUp(): void
    {
        $this->givenResponseFactory();
        $this->imageStorage = \Phake::mock(ImageStorageInterface::class);
        $this->imageFactory = \Phake::mock(ImageFactoryInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /** @test */
    public function run_imageAlreadyExistsInStorage_conflictResponseWasReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CONFLICT);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CONFLICT);
        $this->assertEquals(HttpStatusCodeEnum::CONFLICT, $response->getStatusCode()->getValue());
    }

    /** @test */
    public function run_imageDoesNotExistInStorage_imagePutToStorageAndCreatedResponseWasReturned(): void
    {
        $action = $this->createAction();
        $this->givenImageStorage_exists_returns(false);
        $stream = $this->givenRequest_getBody_returnsStream();
        $image = $this->givenImageFactory_createFromStream_returnsImage();
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $action->run();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertImageStorage_exists_isCalledOnceWithLocation();
        $this->assertRequest_getBody_isCalledOnce();
        $this->assertImageFactory_createFromStream_isCalledOnceWith($stream);
        $this->assertImageStorage_put_isCalledOnceWithLocationAnd($image);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
        $this->assertEquals(HttpStatusCodeEnum::CREATED, $response->getStatusCode()->getValue());
    }

    private function createAction(): CreateAction
    {
        return new CreateAction(self::LOCATION, $this->responseFactory, $this->imageStorage, $this->imageFactory, $this->request);
    }

    private function givenRequest_getBody_returnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->request)->getBody()->thenReturn($stream);

        return $stream;
    }

    private function givenImageStorage_exists_returns(bool $value): void
    {
        \Phake::when($this->imageStorage)->exists(\Phake::anyParameters())->thenReturn($value);
    }

    private function assertImageStorage_exists_isCalledOnceWithLocation(): void
    {
        \Phake::verify($this->imageStorage, \Phake::times(1))->exists(self::LOCATION);
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

    private function givenImageFactory_createFromStream_returnsImage(): Image
    {
        $image = \Phake::mock(Image::class);
        \Phake::when($this->imageFactory)->createFromStream(\Phake::anyParameters())->thenReturn($image);

        return $image;
    }
}
