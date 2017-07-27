<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Strider2038\ImgCache\Core\{
    RequestInterface, SecurityInterface
};
use Strider2038\ImgCache\Imaging\{
    Image\ImageFile, ImageCacheInterface
};
use Strider2038\ImgCache\Response\{
    ConflictResponse, CreatedResponse, ImageResponse, NotFoundResponse, SuccessResponse
};
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
        \Phake::when($this->security)->isAuthorized()->thenReturn(true);
        $this->request = $request = \Phake::mock(RequestInterface::class);
    }

    public function testActionGet_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->get(self::IMAGE_FILENAME)->thenReturn(null);

        $response = $controller->actionGet($this->request);
        
        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testActionGet_FileExistsInCache_ImageResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        $image = \Phake::mock(ImageFile::class);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->get(self::IMAGE_FILENAME)->thenReturn($image);
        \Phake::when($image)->getFilename()->thenReturn($this->givenFile(self::IMAGE_CAT300));

        $response = $controller->actionGet($this->request);

        $this->assertInstanceOf(ImageResponse::class, $response);
    }

    public function testActionCreate_FileAlreadyExistsInCache_ConflictResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionCreate($this->request);

        $this->assertInstanceOf(ConflictResponse::class, $response);
    }

    public function testActionCreate_FileDoesNotExistInCache_CreatedResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->request)->getBody()->thenReturn(self::IMAGE_BODY);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionCreate($this->request);

        \Phake::verify($this->imageCache)->put(self::IMAGE_FILENAME, self::IMAGE_BODY);
        $this->assertInstanceOf(CreatedResponse::class, $response);
    }

    public function testActionReplace_FileDoesNotExistInCache_DeleteNotCalledAndCreatedResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
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
        $controller = new ImageController($this->security, $this->imageCache);
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
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionDelete($this->request);

        \Phake::verify($this->imageCache)->delete(self::IMAGE_FILENAME);
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testDelete_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionDelete($this->request);

        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }

    public function testRebuild_FileExistsInCache_RebuildIsCalledAndOkResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(true);

        $response = $controller->actionRebuild($this->request);

        \Phake::verify($this->imageCache)->rebuild(self::IMAGE_FILENAME);
        $this->assertInstanceOf(SuccessResponse::class, $response);
    }

    public function testRebuild_FileDoesNotExistInCache_NotFoundResponseIsReturned(): void
    {
        $controller = new ImageController($this->security, $this->imageCache);
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn(self::IMAGE_FILENAME);
        \Phake::when($this->imageCache)->exists(self::IMAGE_FILENAME)->thenReturn(false);

        $response = $controller->actionRebuild($this->request);

        $this->assertInstanceOf(NotFoundResponse::class, $response);
    }
}
