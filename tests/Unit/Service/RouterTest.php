<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Application;
use Strider2038\ImgCache\Imaging\{
    Image,
    ImageCacheInterface
};
use Strider2038\ImgCache\Core\{
    Route,
    RequestInterface,
    SecurityInterface
};
use Strider2038\ImgCache\Service\{
    ImageController,
    Router
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RouterTest extends TestCase
{
    /** @var \Strider2038\ImgCache\Core\RequestInterface */
    private $request;
    
    /** @var \Strider2038\ImgCache\Application */
    private $app;
    
    protected function setUp()
    {
        $this->app = new class extends Application {
            public $security;
            public $imgcache;
            public function __construct() {
                $this->security = new class implements SecurityInterface {
                    public function isAuthorized(): bool {}
                };
                $this->imgcache = new class implements ImageCacheInterface {
                    public function get(string $key): ?Image {}
                    public function put(string $key, $data): void {}
                    public function delete(string $key): void {}
                    public function exists(string $key): bool {}
                };
            }
        };
        
        $this->request = new class implements RequestInterface {
            public $method;
            public $url = '/a.jpg';
            public function getMethod(): ?string
            {
                return $this->method;
            }
            public function getHeader(string $key): ?string
            {
                return null;
            }
            public function getUrl(int $component = null): string
            {
                return $this->url;
            }
        };
    }
    
    /**
     * @dataProvider requestMethodsProvider
     */
    public function testGetRoute_RequestMethodIsSet_ControllerAndActionReturned(
        string $requestMethod,
        string $actionName
    ): void {
        $router = new Router($this->app);
        $this->request->method = $requestMethod;
        $route = $router->getRoute($this->request);
        
        $this->assertInstanceOf(Route::class, $route);
        $this->assertInstanceOf(ImageController::class, $route->getController());
        $this->assertEquals($actionName, $route->getAction());
    }
    
    public function requestMethodsProvider(): array
    {
        return [
            ['GET', 'get'],
            ['POST', 'create'],
            ['PUT', 'replace'],
            ['PATCH', 'refresh'],
            ['DELETE', 'delete'],
        ];
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionMessage Route not found
     */
    public function testGetRoute_RequestMethodIsNotSet_ExceptionThrown(): void
    {
        $router = new Router($this->app);
        
        $router->getRoute($this->request);
    }
    
    /**
     * @dataProvider allowedExtensionsProvider
     */
    public function testGetRoute_RequestedFileHasAllowedExtension_ControllerAndActionReturned(string $url): void
    {
        $router = new Router($this->app);
        $this->request->method = 'GET';
        $this->request->url = $url;
        
        $route = $router->getRoute($this->request);
        
        $this->assertInstanceOf(Route::class, $route);
        $this->assertInstanceOf(ImageController::class, $route->getController());
        $this->assertEquals('get', $route->getAction());
    }
    
    public function allowedExtensionsProvider(): array
    {
        return [
            ['/a.jpg'],
            ['/a.jpeg'],
            ['/a.png'],
        ];
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\RequestException
     * @expectedExceptionMessage Requested file has incorrect extension
     */
    public function testGetRoute_RequestedFileHasNotAllowedExtension_ExceptionThrown(): void 
    {
        $router = new Router($this->app);
        $this->request->method = 'GET';
        $this->request->url = '/a.php';
        
        $router->getRoute($this->request);
    }
}
