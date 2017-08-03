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

use Strider2038\ImgCache\Core\RequestInterface;
use Strider2038\ImgCache\Core\ResponseInterface;
use Strider2038\ImgCache\Core\SecurityInterface;
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
    const LOCATION = 'a.jpg';
    const IMAGE_BODY = '0';

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

    public function testActionGet_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Get_Returns(null);

        $response = $controller->actionGet(self::LOCATION);
        
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testActionGet_FileExistsInCache_ImageResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $image = $this->givenImage();
        $this->givenImageCache_Get_Returns($image);

        $response = $controller->actionGet(self::LOCATION);

        $this->assertInstanceOf(ImageResponse::class, $response);
    }

    public function testActionCreate_FileAlreadyExistsInCache_ConflictResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertInstanceOf(ConflictResponse::class, $response);
    }

    public function testActionCreate_FileDoesNotExistInCache_CreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenRequest_GetBody_ReturnsString();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionCreate(self::LOCATION);

        $this->assertImageCache_Put_IsCalledOnce();
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testActionReplace_FileDoesNotExistInCache_DeleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenRequest_GetBody_ReturnsString();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_Delete_IsNeverCalled();
        $this->assertImageCache_Put_IsCalledOnce();
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testActionReplace_FileExistsInCache_DeleteIsCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenRequest_GetBody_ReturnsString();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionReplace(self::LOCATION);

        $this->assertImageCache_Delete_IsCalledOnce();
        $this->assertImageCache_Put_IsCalledOnce();
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testDelete_FileExistsInCache_DeleteIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_Delete_IsCalledOnce();
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testDelete_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionDelete(self::LOCATION);

        $this->assertImageCache_Delete_IsNeverCalled();
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testRebuild_FileExistsInCache_RebuildIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(true);

        $response = $controller->actionRebuild(self::LOCATION);

        $this->assertImageCache_Rebuild_IsCalledOnce();
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testRebuild_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $this->givenImageCache_Exists_Returns(false);

        $response = $controller->actionRebuild(self::LOCATION);

        $this->assertImageCache_Rebuild_IsNeverCalled();
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    /**
     * @param string $action
     * @param string $expectedResponse
     * @dataProvider safeActionsProvider
     */
    public function testRunAction_GivenActionAndSecurityReturnsFalse_ResponseIsReturned(
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

    private function givenRequest_GetBody_ReturnsString(): void
    {
        \Phake::when($this->request)->getBody()->thenReturn(self::IMAGE_BODY);
    }

    private function assertImageCache_Put_IsCalledOnce(): void
    {
        \Phake::verify($this->imageCache, \Phake::times(1))->put(self::LOCATION, self::IMAGE_BODY);
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
