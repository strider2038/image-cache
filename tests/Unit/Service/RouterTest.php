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
use Strider2038\ImgCache\Core\RequestInterface;
use Strider2038\ImgCache\Core\Route;
use Strider2038\ImgCache\Imaging\Validation\ImageValidatorInterface;
use Strider2038\ImgCache\Service\Router;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class RouterTest extends TestCase
{
    const REQUEST_URL = '/a.jpg';

    /** @var RequestInterface */
    private $request;

    /** @var ImageValidatorInterface */
    private $imageValidator;

    protected function setUp()
    {
        $this->request = \Phake::mock(RequestInterface::class);
        $this->imageValidator = \Phake::mock(ImageValidatorInterface::class);
    }
    
    /**
     * @dataProvider requestMethodsProvider
     */
    public function testGetRoute_RequestMethodIsSet_ControllerAndActionReturned(
        string $requestMethod,
        string $actionName
    ): void {
        $router = $this->createRouter();
        $this->givenRequest_GetMethod_Returns($requestMethod);
        $this->givenRequest_GetUrl_Returns(self::REQUEST_URL);
        $this->givenImageValidator_HasValidImageExtension_Returns(true);

        $route = $router->getRoute($this->request);
        
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('imageController', $route->getControllerId());
        $this->assertEquals($actionName, $route->getActionId());
        $this->assertEquals(self::REQUEST_URL, $route->getLocation());
    }
    
    public function requestMethodsProvider(): array
    {
        return [
            ['GET', 'get'],
            ['POST', 'create'],
            ['PUT', 'replace'],
            ['PATCH', 'rebuild'],
            ['DELETE', 'delete'],
        ];
    }
    
    /**
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionMessage Route not found
     */
    public function testGetRoute_RequestMethodIsNotSet_ExceptionThrown(): void
    {
        $router = $this->createRouter();
        
        $router->getRoute($this->request);
    }

    /**
     * @expectedException \Strider2038\ImgCache\Exception\RequestException
     * @expectedExceptionMessage Requested file has incorrect extension
     */
    public function testGetRoute_RequestedFileHasNotAllowedExtension_ExceptionThrown(): void 
    {
        $router = $this->createRouter();
        $this->givenRequest_GetMethod_Returns('GET');
        $this->givenRequest_GetUrl_Returns('/a.php');
        $this->givenImageValidator_HasValidImageExtension_Returns(false);
        
        $router->getRoute($this->request);
    }

    private function createRouter(): Router
    {
        $router = new Router($this->imageValidator);

        return $router;
    }

    private function givenRequest_GetMethod_Returns(string $requestMethod): void
    {
        \Phake::when($this->request)->getMethod()->thenReturn($requestMethod);
    }

    private function givenRequest_GetUrl_Returns(string $value): void
    {
        \Phake::when($this->request)->getUrl(\Phake::anyParameters())->thenReturn($value);
    }

    private function givenImageValidator_HasValidImageExtension_Returns(bool $value): void
    {
        \Phake::when($this->imageValidator)->hasValidImageExtension(self::REQUEST_URL)->thenReturn($value);
    }
}
