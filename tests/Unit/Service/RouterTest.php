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
use Psr\Log\LoggerInterface;
use Strider2038\ImgCache\Core\Http\RequestInterface;
use Strider2038\ImgCache\Core\Http\UriInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Enum\HttpMethodEnum;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Service\Router;
use Strider2038\ImgCache\Tests\Support\Phake\LoggerTrait;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RouterTest extends TestCase
{
    use LoggerTrait;

    private const REQUEST_URL = '/a.jpg';
    private const REQUEST_METHOD_GET = 'GET';

    /** @var RequestInterface */
    private $request;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->request = \Phake::mock(RequestInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
        $this->logger = $this->givenLogger();
    }

    /**
     * @test
     * @param array $map
     * @expectedException \Strider2038\ImgCache\Exception\InvalidConfigurationException
     * @expectedExceptionCode 500
     * @expectedExceptionMessage Url mask to controllers map is invalid
     * @dataProvider invalidUrlMaskToControllersMapProvider
     */
    public function construct_givenInvalidUrlMaskToControllersMap_exceptionThrown(array $map): void
    {
        $this->createRouter($map);
    }

    /**
     * @test
     * @param array $map
     * @dataProvider validUrlMaskToControllersMapProvider
     */
    public function construct_givenValidUrlMaskToControllersMap_classCreated(array $map): void
    {
        $router = $this->createRouter($map);

        $this->assertInstanceOf(Router::class, $router);
    }

    public function invalidUrlMaskToControllersMapProvider(): array
    {
        return [
            [['' => 'value']],
            [['/' => 'value']],
            [['/no_slash_at_end/' => 'value']],
            [['/кириллица' => 'value']],
            [['/ ' => 'value']],
            [['/  ' => 'value']],
            [['/i ' => 'value']],
            [['/i j' => 'value']],
            [['/i//j' => 'value']],
            [['/i' => '/']],
            [['/i' => '']],
            [['/i' => ' ']],
            [['/i' => 0]],
        ];
    }

    public function validUrlMaskToControllersMapProvider(): array
    {
        return [
            [['/i' => 'v']],
            [['/0' => 'value']],
            [['/key_1' => 'value']],
            [['/i/j/k' => 'value']],
            [['/i' => 'V1']],
        ];
    }

    /**
     * @test
     * @param string $requestMethod
     * @param string $actionName
     * @dataProvider requestMethodsProvider
     */
    public function getRoute_requestMethodIsSet_controllerAndActionReturned(
        string $requestMethod,
        string $actionName
    ): void {
        $router = $this->createRouter();
        $this->givenRequest_getMethod_returns($requestMethod);
        $this->givenRequest_getUri_getPath_returns(self::REQUEST_URL);
        $this->givenImageValidator_hasValidImageExtension_returns(self::REQUEST_URL,true);

        $route = $router->getRoute($this->request);
        
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('imageController', $route->getControllerId());
        $this->assertEquals($actionName, $route->getActionId());
        $this->assertEquals(self::REQUEST_URL, $route->getLocation());
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }
    
    public function requestMethodsProvider(): array
    {
        return [
            [self::REQUEST_METHOD_GET, 'get'],
            ['POST', 'create'],
            ['PUT', 'replace'],
            ['DELETE', 'delete'],
        ];
    }
    
    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Route not found
     */
    public function getRoute_requestMethodIsNotSet_exceptionThrown(): void
    {
        $router = $this->createRouter();
        $this->givenRequest_getUri_getPath_returns('');
        
        $router->getRoute($this->request);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRequestException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Requested file has incorrect extension
     */
    public function getRoute_requestedFileHasNotAllowedExtension_exceptionThrown(): void
    {
        $router = $this->createRouter();
        $this->givenRequest_getMethod_returns(self::REQUEST_METHOD_GET);
        $this->givenRequest_getUri_getPath_returns('/a.php');
        $this->givenImageValidator_hasValidImageExtension_returns(self::REQUEST_URL,false);
        
        $router->getRoute($this->request);
    }

    /**
     * @test
     * @param array $map
     * @param string $requestUrl
     * @param string $controllerId
     * @param string $location
     * @dataProvider validUrlMaskToControllersMapAndUrlAndControllerIdAndLocationProvider
     */
    public function getRoute_givenUrlMaskToControllersMap_routeWithGivenControllerIdAndLocationIsReturned(
        array $map,
        string $requestUrl,
        string $controllerId,
        string $location
    ): void {
        $router = $this->createRouter($map);
        $this->givenRequest_getMethod_returns(self::REQUEST_METHOD_GET);
        $this->givenRequest_getUri_getPath_returns($requestUrl);
        $this->givenImageValidator_hasValidImageExtension_returns($requestUrl,true);

        $route = $router->getRoute($this->request);

        $this->assertEquals($controllerId, $route->getControllerId());
        $this->assertEquals($location, $route->getLocation());
        $this->assertLogger_info_isCalledTimes($this->logger, 2);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Route not found
     * @param array $map
     * @param string $requestUrl
     * @dataProvider invalidUrlMaskToControllersMapAndUrlAndControllerIdAndLocationProvider
     */
    public function getRoute_givenInvalidUrlMaskToControllersMap_exceptionThrown(
        array $map,
        string $requestUrl
    ): void {
        $router = $this->createRouter($map);
        $this->givenRequest_getMethod_returns(self::REQUEST_METHOD_GET);
        $this->givenRequest_getUri_getPath_returns($requestUrl);
        $this->givenImageValidator_hasValidImageExtension_returns($requestUrl,true);

        $router->getRoute($this->request);
    }

    public function validUrlMaskToControllersMapAndUrlAndControllerIdAndLocationProvider(): array
    {
        return [
            [['/i' => 'controller1'], '/i/file.jpg', 'controller1', '/file.jpg'],
            [['/i/j' => 'controller2'], '/i/j/k/file.jpg', 'controller2', '/k/file.jpg'],
            [
                ['/i' => 'controller1', '/j' => 'controller2'],
                '/j/image.png',
                'controller2',
                '/image.png',
            ],
        ];
    }

    public function invalidUrlMaskToControllersMapAndUrlAndControllerIdAndLocationProvider(): array
    {
        return [
            [['/i' => 'controller1'], '/ij/file.jpg'],
            [['/i' => 'controller1'], 'file.jpg'],
        ];
    }

    private function createRouter(array $urlMaskToControllersMap = []): Router
    {
        $router = new Router($this->imageValidator, $urlMaskToControllersMap);

        $router->setLogger($this->logger);

        return $router;
    }

    private function givenRequest_getMethod_returns(string $requestMethod): void
    {
        \Phake::when($this->request)
            ->getMethod()
            ->thenReturn(new HttpMethodEnum($requestMethod));
    }

    private function givenRequest_getUri_getPath_returns(string $value): void
    {
        $uri = \Phake::mock(UriInterface::class);
        \Phake::when($this->request)->getUri()->thenReturn($uri);
        \Phake::when($uri)->getPath()->thenReturn($value);
    }

    private function givenImageValidator_hasValidImageExtension_returns(string $url, bool $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension($url)->thenReturn($value);
    }
}
