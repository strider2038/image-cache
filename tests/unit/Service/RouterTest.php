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
use Strider2038\ImgCache\Core\{
    Route,
    RequestInterface
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
    /**
     * @dataProvider requestMethodsProvider
     */
    public function testGetRoute_RequestMethodIsSet_ControllerAndActionReturned(
        string $requestMethod,
        string $actionName
    ) {
        $app = new class extends Application {
            public function __construct() {}
        };
        
        $router = new Router($app);
        
        $request = new class implements RequestInterface {
            public $method;
            public function getMethod(): ?string
            {
                return $this->method;
            }
            public function getHeader(string $key): ?string
            {
                return null;
            }
        };
        
        $request->method = $requestMethod;
        
        $route = $router->getRoute($request);
        
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
    public function testGetRoute_RequestMethodIsNotSet_ExceptionThrown() {
        $app = new class extends Application {
            public function __construct() {}
        };
        
        $router = new \Strider2038\ImgCache\Service\Router($app);
        
        $request = new class implements RequestInterface {
            public function getMethod(): ?string
            {
                return null;
            }
            public function getHeader(string $key): ?string
            {
                return null;
            }
        };
        
        $router->getRoute($request);
    }
}
