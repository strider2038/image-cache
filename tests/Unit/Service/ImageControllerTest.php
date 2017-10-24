<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\Response;
use Strider2038\ImgCache\Core\Http\ResponseFactoryInterface;
use Strider2038\ImgCache\Core\Http\ResponseInterface;
use Strider2038\ImgCache\Core\SecurityInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Enum\HttpStatusCodeEnum;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Service\ImageController;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageControllerTest extends TestCase
{
    private const LOCATION = 'a.jpg';
    private const IMAGE_FILENAME = 'image.jpg';

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var SecurityInterface */
    private $security;
    
    /** @var RequestInterface */
    private $request;
    
    /** @var ImageCacheInterface */
    private $imageCache;
    
    protected function setUp()
    {
        parent::setUp();
        $this->responseFactory = \Phake::mock(ResponseFactoryInterface::class);
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->security = \Phake::mock(SecurityInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /** @test */
    public function actionGet_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_get_returns(null);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $controller->actionGet(self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    /** @test */
    public function actionGet_fileExistsInCache_fileResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $image = $this->givenImage();
        $this->givenImageCache_get_returns($image);
        $this->givenResponseFactory_createFileResponse_returnsResponse();

        $response = $controller->actionGet(self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createFileResponse_isCalledOnceWith(
            HttpStatusCodeEnum::CREATED,
            self::IMAGE_FILENAME
        );
    }

    /** @test */
    public function actionCreate_fileAlreadyExistsInCache_conflictResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CONFLICT);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CONFLICT);
    }

    /** @test */
    public function actionCreate_fileDoesNotExistInCache_createdResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function actionReplace_fileDoesNotExistInCache_deleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_delete_isNeverCalled();
        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function actionReplace_fileExistsInCache_deleteIsCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_getBody_returnsStream();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::CREATED);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_delete_isCalledOnce();
        $this->assertImageCache_put_isCalledOnce($stream);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::CREATED);
    }

    /** @test */
    public function delete_fileExistsInCache_deleteIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_exists_returns(true);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::OK);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_delete_isCalledOnce();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::OK);
    }

    /** @test */
    public function delete_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_exists_returns(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::NOT_FOUND);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_delete_isNeverCalled();
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertResponseFactory_createMessageResponse_isCalledOnceWithCode(HttpStatusCodeEnum::NOT_FOUND);
    }

    /**
     * @test
     * @param string $action
     * @param int $expectedHttpStatusCode
     * @dataProvider safeActionsProvider
     */
    public function runAction_givenActionAndSecurityReturnsFalse_responseIsReturned(
        string $action,
        int $expectedHttpStatusCode
    ): void {
        $controller = $this->createImageControllerWithStubbedActions();
        \Phake::when($this->security)->isAuthorized()->thenReturn(false);
        $this->givenResponseFactory_createMessageResponse_returnsResponseWithCode(HttpStatusCodeEnum::FORBIDDEN);

        $response = $controller->runAction($action, self::LOCATION);

        $this->assertEquals($expectedHttpStatusCode, $response->getStatusCode()->getValue());
    }

    public function safeActionsProvider(): array
    {
        return [
            ['get', HttpStatusCodeEnum::OK],
            ['create', HttpStatusCodeEnum::FORBIDDEN],
            ['replace', HttpStatusCodeEnum::FORBIDDEN],
            ['delete', HttpStatusCodeEnum::FORBIDDEN],
        ];
    }

    private function createImageController(): ImageController
    {
        $controller = new ImageController(
            $this->responseFactory,
            $this->security,
            $this->imageCache,
            $this->request
        );

        return $controller;
    }

    private function createImageControllerWithStubbedActions(): ImageController
    {
        $responseFactory = $this->responseFactory;
        $security = $this->security;
        $imageCache = $this->imageCache;
        $request = $this->request;

        $controller = new class ($responseFactory, $security, $imageCache, $request) extends ImageController {
            public function actionGet(string $location): ResponseInterface
            {
                return new Response(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
            }

            public function actionCreate(string $location): ResponseInterface
            {
                return new Response(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
            }

            public function actionReplace(string $location): ResponseInterface
            {
                return new Response(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
            }

            public function actionDelete(string $location): ResponseInterface
            {
                return new Response(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));
            }

        };

        return $controller;
    }

    private function givenImageCache_get_returns(?ImageFile $image): void
    {
        \Phake::when($this->imageCache)->get(self::LOCATION)->thenReturn($image);
    }

    private function givenImage(): ImageFile
    {
        $image = \Phake::mock(ImageFile::class);

        \Phake::when($image)->getFilename()->thenReturn(self::IMAGE_FILENAME);

        return $image;
    }

    private function givenImageCache_exists_returns(bool $value): void
    {
        \Phake::when($this->imageCache)->exists(self::LOCATION)->thenReturn($value);
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

    private function assertImageCache_delete_isNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::never())->delete(\Phake::anyParameters());
    }

    private function assertImageCache_delete_isCalledOnce(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->delete(self::LOCATION);
    }

    private function givenResponseFactory_createMessageResponse_returnsResponseWithCode(int $code): void
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCodeEnum($code));

        \Phake::when($this->responseFactory)
            ->createMessageResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }

    private function assertResponseFactory_createMessageResponse_isCalledOnceWithCode(int $code): void
    {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createMessageResponse(\Phake::capture($httpStatusCode), \Phake::capture($message));

        /** @var HttpStatusCodeEnum $httpStatusCode */
        $this->assertEquals($code, $httpStatusCode->getValue());
    }

    private function givenResponseFactory_createFileResponse_returnsResponse(): void
    {
        $response = \Phake::mock(ResponseInterface::class);

        \Phake::when($response)
            ->getStatusCode()
            ->thenReturn(new HttpStatusCodeEnum(HttpStatusCodeEnum::OK));

        \Phake::when($this->responseFactory)
            ->createFileResponse(\Phake::anyParameters())
            ->thenReturn($response);
    }

    private function assertResponseFactory_createFileResponse_isCalledOnceWith(
        int $expectedCode,
        string $expectedFilename
    ): void {
        \Phake::verify($this->responseFactory, \Phake::times(1))
            ->createFileResponse(\Phake::capture($httpStatusCode), \Phake::capture($filename));

        /** @var HttpStatusCodeEnum $httpStatusCode */
        $this->assertEquals($expectedCode, $httpStatusCode->getValue());
        $this->assertEquals($expectedFilename, $filename);
    }
}
