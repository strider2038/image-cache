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

use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\ResponseInterface;
use Strider2038\ImgCache\Core\SecurityInterface;
use Strider2038\ImgCache\Core\StreamInterface;
use Strider2038\ImgCache\Imaging\Image\ImageFile;
use Strider2038\ImgCache\Imaging\Image\ImageInterface;
use Strider2038\ImgCache\Imaging\ImageCacheInterface;
use Strider2038\ImgCache\Response\ConflictResponse;
use Strider2038\ImgCache\Response\CreatedResponse;
use Strider2038\ImgCache\Response\ForbiddenResponse;
use Strider2038\ImgCache\Response\ImageResponse;
use Strider2038\ImgCache\Response\NotFoundResponse;
use Strider2038\ImgCache\Response\SuccessResponse;
use Strider2038\ImgCache\Service\ImageController;
use Strider2038\ImgCache\Tests\Support\FileTestCase;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageControllerTest extends FileTestCase
{
    private const LOCATION = 'a.jpg';

    /** @var SecurityInterface */
    private $security;
    
    /** @var RequestInterface */
    private $request;
    
    /** @var ImageCacheInterface */
    private $imageCache;
    
    protected function setUp()
    {
        parent::setUp();
        $this->imageCache = \Phake::mock(ImageCacheInterface::class);
        $this->security = \Phake::mock(SecurityInterface::class);
        $this->request = \Phake::mock(RequestInterface::class);
    }

    /** @test */
    public function actionGet_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Get_Returns(null);

        $response = $controller->actionGet(self::LOCATION);
        
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    /** @test */
    public function actionGet_fileExistsInCache_imageResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $image = $this->givenImage();
        $this->givenImageCache_Get_Returns($image);

        $response = $controller->actionGet(self::LOCATION);

        $this->assertInstanceOf(ImageResponse::class, $response);
    }

    /** @test */
    public function actionCreate_fileAlreadyExistsInCache_conflictResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertInstanceOf(ConflictResponse::class, $response);
    }

    /** @test */
    public function actionCreate_fileDoesNotExistInCache_createdResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_GetBody_ReturnsStream();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertImageCache_Put_IsCalledOnce($stream);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    /** @test */
    public function actionReplace_fileDoesNotExistInCache_deleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_GetBody_ReturnsStream();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_Delete_IsNeverCalled();
        $this->assertImageCache_Put_IsCalledOnce($stream);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    /** @test */
    public function actionReplace_fileExistsInCache_deleteIsCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $stream = $this->givenRequest_GetBody_ReturnsStream();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_Delete_IsCalledOnce();
        $this->assertImageCache_Put_IsCalledOnce($stream);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    /** @test */
    public function delete_fileExistsInCache_deleteIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_Delete_IsCalledOnce();
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    /** @test */
    public function delete_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_Delete_IsNeverCalled();
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    /** @test */
    public function rebuild_fileExistsInCache_rebuildIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionRebuild(self::LOCATION);

        $this->assertImageCache_Rebuild_IsCalledOnce();
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    /** @test */
    public function rebuild_fileDoesNotExistInCache_notFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionRebuild(self::LOCATION);

        $this->assertImageCache_Rebuild_IsNeverCalled();
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    /**
     * @test
     * @param string $action
     * @param string $expectedResponse
     * @dataProvider safeActionsProvider
     */
    public function runAction_givenActionAndSecurityReturnsFalse_responseIsReturned(
        string $action,
        string $expectedResponse
    ): void {
        $controller = $this->createImageControllerWithStubbedActions();
        \Phake::when($this->security)->isAuthorized()->thenReturn(false);

        $response = $controller->runAction($action, self::LOCATION);

        $this->assertInstanceOf($expectedResponse, $response);
    }

    public function safeActionsProvider(): array
    {
        return [
            ['get', SuccessResponse::class],
            ['create', ForbiddenResponse::class],
            ['replace', ForbiddenResponse::class],
            ['delete', ForbiddenResponse::class],
            ['rebuild', ForbiddenResponse::class],
        ];
    }

    private function createImageController(): ImageController
    {
        $controller = new ImageController(
            $this->security,
            $this->imageCache,
            $this->request
        );

        return $controller;
    }

    private function createImageControllerWithStubbedActions(): ImageController
    {
        $security = $this->security;
        $imageCache = $this->imageCache;
        $request = $this->request;

        $controller = new class ($security, $imageCache, $request) extends ImageController {
            public function actionGet(string $location): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionCreate(string $location): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionReplace(string $location): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionDelete(string $location): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionRebuild(string $location): ResponseInterface
            {
                return new SuccessResponse();
            }
        };

        return $controller;
    }

    private function givenImageCache_Get_Returns(?ImageInterface $image): void
    {
        \Phake::when($this->imageCache)->get(self::LOCATION)->thenReturn($image);
    }

    private function givenImage(): ImageFile
    {
        $image = \Phake::mock(ImageFile::class);

        \Phake::when($image)->getFilename()->thenReturn($this->givenAssetFile(self::IMAGE_BOX_PNG));

        return $image;
    }

    private function givenImageCache_Exists_Returns(bool $value): void
    {
        \Phake::when($this->imageCache)->exists(self::LOCATION)->thenReturn($value);
    }

    private function givenRequest_GetBody_ReturnsStream(): StreamInterface
    {
        $stream = \Phake::mock(StreamInterface::class);
        \Phake::when($this->request)->getBody()->thenReturn($stream);

        return $stream;
    }

    private function assertImageCache_Put_IsCalledOnce(StreamInterface $stream): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->put(self::LOCATION, $stream);
    }

    private function assertImageCache_Delete_IsNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::never())->delete(\Phake::anyParameters());
    }

    private function assertImageCache_Delete_IsCalledOnce(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->delete(self::LOCATION);
    }

    private function assertImageCache_Rebuild_IsCalledOnce(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->rebuild(self::LOCATION);
    }

    private function assertImageCache_Rebuild_IsNeverCalled(): void
    {
        \Phake::verify($this->imageCache, \Phake::never())->rebuild(\Phake::anyParameters());
    }
}
