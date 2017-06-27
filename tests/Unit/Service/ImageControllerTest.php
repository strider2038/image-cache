<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Strider2038\ImgCache\Tests\Support\FileTestCase;
use Strider2038\ImgCache\Service\ImageController;
use Strider2038\ImgCache\Core\{
    RequestInterface,
    SecurityInterface
};
use Strider2038\ImgCache\Response\{
    ImageResponse,
    NotFoundResponse
};
use Strider2038\ImgCache\Imaging\{
    Image,
    ImageCacheInterface
};


/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageControllerTest extends FileTestCase
{
    /** @var \Strider2038\ImgCache\Core\SecurityInterface */
    private $security;
    
    /** @var \Strider2038\ImgCache\Core\RequestInterface */
    private $request;
    
    /** @var Strider2038\ImgCache\Imaging\ImageCacheInterface */
    private $imgcache;
    
    protected function setUp()
    {
        $this->imgcache = new class implements ImageCacheInterface {
            public $testImage = null;
            public function get(string $key): ?Image
            {
                return $this->testImage;
            }
            public function put(string $key, $data): void {}
            public function delete(string $key): void {}
            public function exists(string $key): bool
            {
                return true;
            }
        };
        
        $this->request = new class implements RequestInterface {
            public function getMethod(): ?string
            {
                return null;
            }
            public function getHeader(string $key): ?string
            {
                return null;
            }
            public function getUrl(int $component = null): string
            {
                return 'testUrl';
            }
        };
        
        $this->security = new class implements SecurityInterface {
            public $isAuth = false;
            public function isAuthorized(): bool
            {
                return $this->isAuth;
            }
        };
    }
    
    public function testGet_FileDoesNotExistInCache_NotFoundResponseReturned(): void
    {
        $controller = new ImageController($this->security, $this->imgcache);
        
        $this->assertInstanceOf(NotFoundResponse::class, $controller->actionGet($this->request));
    }

    public function testGet_FileExistsInCache_ImageResponseReturned(): void
    {
        $this->imgcache->testImage = new class extends Image {
            public $testFile;
            public function __construct(){}
            public function getFilename(): string
            {
                return $this->testFile;
            }
        };
        
        // hard mocks ...
        $this->imgcache->testImage->testFile = $this->haveFile(self::IMAGE_CAT300);
        
        $controller = new ImageController($this->security, $this->imgcache);
        
        $this->assertInstanceOf(ImageResponse::class, $controller->actionGet($this->request));
    }
}
