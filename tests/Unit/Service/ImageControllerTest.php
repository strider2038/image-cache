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
    const IMAGE_FILENAME = 'a.jpg';
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
        $this->request = $request = \Phake::mock(RequestInterface::class);
    }

    public function testActionGet_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->get(self::IMAGE_FILENAME)->thenReturn(null);

        $response = $controller->actionGet($this->request);
        
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testActionGet_FileExistsInCache_ImageResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        $image = \Phake::mock(ImageFile::class);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->get(self::IMAGE_FILENAME)->thenReturn($image);
        \Phake::when($image)->getFilename()->thenReturn($this->givenFile(self::IMAGE_BOX_PNG));

        $response = $controller->actionGet($this->request);

        $this->assertInstanceOf(ImageResponse::class, $response);
    }

    public function testActionCreate_FileAlreadyExistsInCache_ConflictResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionCreate($this->request);

        $this->assertInstanceOf(ConflictResponse::class, $response);
    }

    public function testActionCreate_FileDoesNotExistInCache_CreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->request)->getBody()->thenReturn(self::IMAGE_BODY);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionCreate($this->request);

        \Phake::verify($this->imageCache)->put(self::IMAGE_FILENAME, self::IMAGE_BODY);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testActionReplace_FileDoesNotExistInCache_DeleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->request)->getBody()->thenReturn(self::IMAGE_BODY);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionReplace($this->request);

        \Phake::verify($this->imageCache, \Phake::never())->delete(\Phake::anyParameters());
        \Phake::verify($this->imageCache)->put(self::IMAGE_FILENAME, self::IMAGE_BODY);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testActionReplace_FileExistsInCache_DeleteIsCalledAndCreatedResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->request)->getBody()->thenReturn(self::IMAGE_BODY);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionReplace($this->request);

        \Phake::verify($this->imageCache)->delete(self::IMAGE_FILENAME);
        \Phake::verify($this->imageCache)->put(self::IMAGE_FILENAME, self::IMAGE_BODY);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testDelete_FileExistsInCache_DeleteIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionDelete($this->request);

        \Phake::verify($this->imageCache)->delete(self::IMAGE_FILENAME);
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testDelete_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionDelete($this->request);

        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testRebuild_FileExistsInCache_RebuildIsCalledAndOkResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionRebuild($this->request);

        \Phake::verify($this->imageCache)->rebuild(self::IMAGE_FILENAME);
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testRebuild_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = $this->createImageController();
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionRebuild($this->request);

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

        $response = $controller->runAction($action, $this->request);

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
        $controller = new ImageController($this->security, $this->imageCache);

        return $controller;
    }

    private function createImageControllerWithStubbedActions(): ImageController
    {
        $security = $this->security;
        $imageCache = $this->imageCache;

        $controller = new class ($security, $imageCache) extends ImageController {
            public function actionGet(RequestInterface $request): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionCreate(RequestInterface $request): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionReplace(RequestInterface $request): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionDelete(RequestInterface $request): ResponseInterface
            {
                return new SuccessResponse();
            }

            public function actionRebuild(RequestInterface $request): ResponseInterface
            {
                return new SuccessResponse();
            }
        };

        return $controller;
    }
}
